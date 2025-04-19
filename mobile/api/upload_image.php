<?php
// Include database configuration and connection
require_once '../db/config.php';
require_once '../db/connection.php';

// Set headers for API response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Only POST method is allowed']);
    exit;
}

// Check if image file was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'No image uploaded or upload error: ' . ($_FILES['image']['error'] ?? 'Unknown error')
    ]);
    exit;
}

// Get the directory to save the image
$upload_directory = isset($_POST['directory']) ? $_POST['directory'] : 'uploads/buyer-profile';

// Create the directory if it doesn't exist (recursive)
$full_directory_path = "../../$upload_directory";
if (!file_exists($full_directory_path)) {
    if (!mkdir($full_directory_path, 0755, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
        exit;
    }
}

// Generate a unique filename
$timestamp = time();
$original_filename = basename($_FILES['image']['name']);
$extension = pathinfo($original_filename, PATHINFO_EXTENSION);
$new_filename = "shop_{$timestamp}." . $extension;

// Set the full path for the uploaded file
$upload_path = "$full_directory_path/$new_filename";

// Move the uploaded file to the target directory
if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Image uploaded successfully',
        'filename' => $new_filename
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to save the uploaded image'
    ]);
}

// Close the database connection
mysqli_close($conn);
?>
