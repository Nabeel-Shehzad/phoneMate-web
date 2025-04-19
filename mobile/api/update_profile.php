<?php
// Include database configuration and connection
require_once '../db/config.php';
require_once '../db/connection.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get JSON data from the request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Check if all required fields are present
$required_fields = ['user_id', 'name', 'email', 'phone', 'address'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode([
            'status' => 'error',
            'message' => "Missing required field: $field"
        ]);
        exit;
    }
}

// Sanitize input data
$user_id = filter_var($data['user_id'], FILTER_SANITIZE_NUMBER_INT);
$name = filter_var($data['name'], FILTER_SANITIZE_STRING);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$phone = filter_var($data['phone'], FILTER_SANITIZE_STRING);
$address = filter_var($data['address'], FILTER_SANITIZE_STRING);
$cnic = isset($data['cnic']) ? filter_var($data['cnic'], FILTER_SANITIZE_STRING) : '';

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email format'
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
    
    // Check if the email is already in use by another user
    $check_email_stmt = $conn->prepare("SELECT buyer_id FROM buyer WHERE buyer_email = ? AND buyer_id != ?");
    $check_email_stmt->bind_param("si", $email, $user_id);
    $check_email_stmt->execute();
    $check_email_result = $check_email_stmt->get_result();
    
    if ($check_email_result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email is already in use by another account'
        ]);
        exit;
    }
    
    // Update user profile
    $update_stmt = $conn->prepare("UPDATE buyer SET buyer_name = ?, buyer_email = ?, buyer_contact = ?, buyer_address = ?, buyer_cnic = ? WHERE buyer_id = ?");
    $update_stmt->bind_param("sssssi", $name, $email, $phone, $address, $cnic, $user_id);
    $update_result = $update_stmt->execute();
    
    if ($update_result) {
        // Get updated user data
        $user_stmt = $conn->prepare("SELECT buyer_id, buyer_name, buyer_email, buyer_contact, buyer_address, buyer_cnic, buyer_status FROM buyer WHERE buyer_id = ?");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user_data_raw = $user_result->fetch_assoc();
        
        // Format the user data to match the structure used in login.php
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
            'message' => 'Profile updated successfully',
            'user' => $user_data
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update profile'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

// Close the database connection
$conn->close();
?>
