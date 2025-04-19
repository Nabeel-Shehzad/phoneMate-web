<?php
// Include database configuration and connection
require_once '../db/config.php';
require_once '../db/connection.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get user_id from the request
$user_id = isset($_GET['user_id']) ? filter_var($_GET['user_id'], FILTER_SANITIZE_NUMBER_INT) : null;

// Check if user_id is provided
if (!$user_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameter: user_id'
    ]);
    exit;
}

try {
    // Check if the user exists
    $check_user_stmt = $conn->prepare("SELECT buyer_id FROM buyer WHERE buyer_id = ?");
    $check_user_stmt->bind_param("i", $user_id);
    $check_user_stmt->execute();
    $check_user_result = $check_user_stmt->get_result();
    
    if ($check_user_result->num_rows === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not found'
        ]);
        exit;
    }
    
    // Get user data
    $user_stmt = $conn->prepare("SELECT buyer_id, buyer_name, buyer_email, buyer_contact, buyer_address, buyer_cnic, buyer_status FROM buyer WHERE buyer_id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_data_raw = $user_result->fetch_assoc();
    
    // Format the user data to match the structure used in login.php and update_profile.php
    $user_data = [
        'id' => $user_data_raw['buyer_id'],
        'name' => $user_data_raw['buyer_name'],
        'email' => $user_data_raw['buyer_email'],
        'phone' => $user_data_raw['buyer_contact'],
        'address' => $user_data_raw['buyer_address'],
        'cnic' => $user_data_raw['buyer_cnic'],
        'status' => $user_data_raw['buyer_status'],
        'user_type' => 'shopkeeper'
    ];
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Profile retrieved successfully',
        'user' => $user_data
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

// Close the database connection
$conn->close();
?>
