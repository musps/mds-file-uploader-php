<?php

// Authorized file mine types.
$aMimeTypes = [
  'image/png',
  'image/jpeg'
];

// Maximum file size upload in M.
$dMaxFileSize = intval(getenv('D_MAX_FILE_SIZE', 4));

// The directory where files are stored.
$sUploadPath = getenv('S_UPLOAD_PATH', 'files/');

// Secret key used for csrf token and image access token.
$sSecret = getenv('S_SECRET', 'a5rGe=nfb+9D%zPV');

// Default csrf data validator.
$aData = [
  'createdAt' => time()
];

// The current error of a script execution.
$sError = '';
