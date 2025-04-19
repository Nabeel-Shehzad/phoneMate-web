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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if data is valid
    if (is_null($data)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
        exit;
    }
    
    // Extract data from request
    $email = isset($data['email']) ? mysqli_real_escape_string($conn, $data['email']) : '';
    $password = isset($data['password']) ? $data['password'] : '';
    
    // Validate required fields
    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
        exit;
    }
    
    // Check if email exists in buyer table (mobile app users/shopkeepers)
    $check_email_query = "SELECT * FROM buyer WHERE buyer_email = '$email'";
    $check_email_result = mysqli_query($conn, $check_email_query);
    
    if (mysqli_num_rows($check_email_result) > 0) {
        $user = mysqli_fetch_assoc($check_email_result);
        
        // Verify password
        if (password_verify($password, $user['buyer_password'])) {
            // Check if account is approved
            if ($user['buyer_status'] === 'pending') {
                echo json_encode(['status' => 'error', 'message' => 'Your account is pending approval']);
                exit;
            } else if ($user['buyer_status'] === 'rejected') {
                echo json_encode(['status' => 'error', 'message' => 'Your account has been rejected']);
                exit;
            }
            
            // Create user data for response
            $userData = [
                'id' => $user['buyer_id'],
                'name' => $user['buyer_name'],
                'email' => $user['buyer_email'],
                'address' => $user['buyer_address'],
                'phone' => $user['buyer_contact'],
                'cnic' => $user['buyer_cnic'],
                'image' => $user['buyer_image'],
                'user_type' => 'shopkeeper' // Changed from 'buyer' to 'shopkeeper' since mobile app users are shopkeepers
            ];
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Login successful',
                'user' => $userData
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
        }
    } else {
        // Check if email exists in rider table
        $check_rider_query = "SELECT * FROM rider WHERE rider_email = '$email'";
        $check_rider_result = mysqli_query($conn, $check_rider_query);
        
        if (mysqli_num_rows($check_rider_result) > 0) {
            $user = mysqli_fetch_assoc($check_rider_result);
            
            // Verify password
            if (password_verify($password, $user['rider_password'])) {
                // Check if account is approved
                if ($user['rider_status'] === 'pending') {
                    echo json_encode(['status' => 'error', 'message' => 'Your account is pending approval']);
                    exit;
                } else if ($user['rider_status'] === 'rejected') {
                    echo json_encode(['status' => 'error', 'message' => 'Your account has been rejected']);
                    exit;
                }
                
                // Create user data for response
                $userData = [
                    'id' => $user['rider_id'],
                    'name' => $user['rider_name'],
                    'email' => $user['rider_email'],
                    'address' => $user['rider_address'],
                    'phone' => $user['rider_contact'],
                    'cnic' => $user['rider_cnic'],
                    'image' => $user['rider_image'],
                    'user_type' => 'rider'
                ];
                
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Login successful',
                    'user' => $userData
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
            }
        } else {
            // Check if email exists in business developer table
            $check_bd_query = "SELECT * FROM business_developer WHERE bd_email = '$email'";
            $check_bd_result = mysqli_query($conn, $check_bd_query);
            
            if (mysqli_num_rows($check_bd_result) > 0) {
                $user = mysqli_fetch_assoc($check_bd_result);
                
                // Verify password
                if (password_verify($password, $user['bd_password'])) {
                    // Check if account is approved
                    if ($user['bd_status'] === 'pending') {
                        echo json_encode(['status' => 'error', 'message' => 'Your account is pending approval']);
                        exit;
                    } else if ($user['bd_status'] === 'rejected') {
                        echo json_encode(['status' => 'error', 'message' => 'Your account has been rejected']);
                        exit;
                    }
                    
                    // Create user data for response
                    $userData = [
                        'id' => $user['bd_id'],
                        'name' => $user['bd_name'],
                        'email' => $user['bd_email'],
                        'address' => $user['bd_address'],
                        'phone' => $user['bd_contact'],
                        'cnic' => $user['bd_cnic'],
                        'image' => $user['bd_image'],
                        'user_type' => 'business_developer'
                    ];
                    
                    echo json_encode([
                        'status' => 'success', 
                        'message' => 'Login successful',
                        'user' => $userData
                    ]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Email not found']);
            }
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Only POST method is allowed']);
}

// Close the database connection
mysqli_close($conn);
?>
