<?php

function tokenGenerate($aData, $sTokenSecret = '') {
  global $sSecret;

  $sTokenSecret = empty($sTokenSecret) ? md5(time() .uniqid($sSecret)) : $sTokenSecret;
  $sDataBase64 = base64_encode(json_encode($aData));
  $sHash = hash('sha256', $sDataBase64 . $sTokenSecret);
  $sToken = $sDataBase64 . '.' . $sHash;

  return [
    'token' => $sToken,
    'secret' => $sTokenSecret
  ];
}

function tokenSaveSecret($sToken) {
  if (session_id()) {
    $_SESSION['tokenSecret'] = $sToken;
    return true;
  }
  throw new \Exception('tokenSaveSecret');
  return false;
}

function tokenGetSecret() {
  if (session_id()) {
    return empty($_SESSION['tokenSecret']) ? '' : $_SESSION['tokenSecret'];
  }
  throw new \Exception('tokenSaveSecret');
  return false;
}

function tokenVerify($sToken) {
  $sTokenSecret = tokenGetSecret();
  $aData = explode('.', $sToken);
  if (count($aData) != 2) {
    return false;
  }

  $sDataBase64 = $aData[0];
  $aData = json_decode(base64_decode($sDataBase64), true);
  $aToken = tokenGenerate($aData, $sTokenSecret);
  return $aToken['token'] == $sToken;
}

function fileTokenGenerate($sFileName, $sSecret) {
  return md5('filename_' . $sFileName . $sSecret);
}

function fileTokenVerify($sFileToken, $sFileName, $sSecret) {
  return $sFileToken == fileTokenGenerate($sFileName, $sSecret);
}
