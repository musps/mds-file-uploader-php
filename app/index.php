<?php

require_once './vendor/autoload.php';

// Load environment variables from '.env' file.
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Initialize global variables.
require_once './config.php';

// Require default functions file.
require_once './functions.php';

// Start session.
session_start();

/**
 * Fetch a target file.
 * Trigger case: /files/**.**?token=**
 * Example: /files/5eb19504c85f2f488756c06789ed1423.png?token=b3b00d46bcd2bfbba3380863be020656
 */
$aUrlPath = parse_url($_SERVER['REQUEST_URI']);
$sUrlPath = $aUrlPath['path'];
$dSearchPos = strpos($sUrlPath, $sUploadPath);

if ($dSearchPos === 1 && !empty($_GET['token'])) {
  $sFileToken = $_GET['token'];
  $sFilePath = trim($sUrlPath, '/' . $sUploadPath);

  try {
    if (!fileTokenVerify($sFileToken, $sFilePath, $sSecret)) {
      throw new \Exception('token failed verification.');
    }

    $sFileContent = @file_get_contents($sUploadPath . $sFilePath);

    if (empty($sFileContent)) {
      throw new \Exception('file doesn\'t exist.');
    }

    $sFileContentType = mime_content_type($sUploadPath . $sFilePath);
    header('Content-Type: ' . $sFileContentType);
    echo $sFileContent;

  } catch (\Exception $e) {
    header('location: /');
    exit();
  }
}

/**
 * Handle form submit.
 */
if (!empty($_POST) && !empty($_POST['csrf']) && !empty($_FILES)) {
  try {
    // Step 1: We check if the csrf is valid.
    if (!tokenVerify($_POST['csrf'])) {
      throw new \Exception('the csrf token is invalid.');
    }

    // Step 2: Handle file server exceptions.
    if ($_FILES['file']['error'] != 0) {
      switch ($_FILES['file']['error']) {
        case 1:
          throw new \Exception('max file size exceed.');
          break;
        default:
          throw new \Exception('an error happened while checking file.');
          break;
      }
    }

    // Step 3: Checking the file.
    // File params.
    $sFileName = $_FILES['file']['name'];
    $sFilePath = $_FILES['file']['tmp_name'];
    $sFileMimeType = mime_content_type($sFilePath);
    $dFileSize = filesize($sFilePath);
    // Convert file size in MB.
    $dFileSize = round($dFileSize / 1024 / 1024, 1);

    if (!in_array($sFileMimeType, $aMimeTypes)) {
      throw new \Exception('The file type is not accepted');
    } else if ($dFileSize > $dMaxFileSize) {
      throw new \Exception('The file size exceed the limit.');
    }

    // Step 4: Save the file.
    // File name.
    $sFileExtension = pathinfo($sFileName, PATHINFO_EXTENSION);
    $sFileNameUUID = md5(time() .uniqid('filename_')) . '.' . $sFileExtension;
    $sDestination = $sUploadPath . $sFileNameUUID;
    $sFileToken = fileTokenGenerate($sFileNameUUID, $sSecret);

    move_uploaded_file($sFilePath, $sDestination);
    chmod($sDestination, 0644);

  } catch (\Exception $e) {
    $sError = $e->getMessage();
  }
}

/**
 * Generate new csrf token
 * Save token secret in session.
 */
$aToken = tokenGenerate($aData);
tokenSaveSecret($aToken['secret']);

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>File upload</title>

  <style>
    .onError {
      color: red;
      font-weight: bold;
    }

    .fileUpload > form > fieldset {
      display: flex;
      flex-direction: column;
    }
    .fileUpload > form > fieldset > * {
      padding: 15px 0;
    }

    h1 {
      margin: 0;
    }
  </style>
</head>
  <body>

    <div class="fileUpload">
      <form method="post" enctype="multipart/form-data">
        <fieldset>
          <legend><h1>File upload</h1></legend>

          <?php
            if (!empty($sDestination)) {
              echo '<a href="'.$sDestination.'?token='.$sFileToken.'" target="_blank">open image in new tab</a>';
            }

            if (!empty($sError)) {
              echo '<p class="onError">'.$sError.'</p>';
            }
          ?>

          <input type="file" name="file" />
          <input type="hidden" name="csrf" value="<?php echo $aToken['token']; ?>" />
          <input type="submit" name="btnSubmit" value="Send" />
        </fieldset>
      </form>
    </div>

  </body>
</html>
