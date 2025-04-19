<?php
// Set headers for API response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Check if the upload_image.php file exists
$file_path = __DIR__ . '/upload_image.php';
$exists = file_exists($file_path);

// Return the result
echo json_encode([
    'status' => $exists ? 'success' : 'error',
    'message' => $exists ? 'File exists' : 'File does not exist',
    'path' => $file_path,
    'directory_contents' => scandir(__DIR__)
]);
