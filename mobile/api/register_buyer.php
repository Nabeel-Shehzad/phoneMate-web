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
    $buyer_name = isset($data['name']) ? mysqli_real_escape_string($conn, $data['name']) : '';
    $buyer_address = isset($data['address']) ? mysqli_real_escape_string($conn, $data['address']) : '';
    $buyer_contact = isset($data['phone']) ? mysqli_real_escape_string($conn, $data['phone']) : '';
    $buyer_email = isset($data['email']) ? mysqli_real_escape_string($conn, $data['email']) : '';
    $buyer_password = isset($data['password']) ? mysqli_real_escape_string($conn, $data['password']) : '';
    
    // Default values for other required fields
    $buyer_cnic = isset($data['cnic']) ? mysqli_real_escape_string($conn, $data['cnic']) : '';
    $buyer_image = isset($data['image']) ? mysqli_real_escape_string($conn, $data['image']) : 'default.jpg';
    $shop_img = isset($data['shop_img']) ? mysqli_real_escape_string($conn, $data['shop_img']) : '';
    $location = isset($data['location']) ? mysqli_real_escape_string($conn, $data['location']) : '';
    $buyer_status = 'pending'; // Default status for new buyers
    
    // Handle foreign keys with default values (1) instead of NULL
    $fk_bd_id = isset($data['bd_id']) && !empty($data['bd_id']) ? intval($data['bd_id']) : 1;
    $fk_rider_id = isset($data['rider_id']) && !empty($data['rider_id']) ? intval($data['rider_id']) : 1;
    
    // Validate required fields
    if (empty($buyer_name) || empty($buyer_address) || empty($buyer_contact) || empty($buyer_email) || empty($buyer_password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }
    
    // Validate email format
    if (!filter_var($buyer_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }
    
    // Check if email already exists
    $check_email_query = "SELECT buyer_id FROM buyer WHERE buyer_email = '$buyer_email'";
    $check_email_result = mysqli_query($conn, $check_email_query);
    
    if (mysqli_num_rows($check_email_result) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
        exit;
    }
    
    // Hash the password for security
    $hashed_password = password_hash($buyer_password, PASSWORD_DEFAULT);
    
    // Insert new buyer into the database
    $insert_query = "INSERT INTO buyer (
        buyer_name, 
        buyer_address, 
        buyer_contact, 
        buyer_cnic, 
        buyer_image, 
        shop_img, 
        location, 
        buyer_email, 
        buyer_password, 
        buyer_status, 
        fk_bd_id, 
        fk_rider_id
    ) VALUES (
        '$buyer_name', 
        '$buyer_address', 
        '$buyer_contact', 
        '$buyer_cnic', 
        '$buyer_image', 
        '$shop_img', 
        '$location', 
        '$buyer_email', 
        '$hashed_password', 
        '$buyer_status', 
        $fk_bd_id, 
        $fk_rider_id
    )";
    
    if (mysqli_query($conn, $insert_query)) {
        $buyer_id = mysqli_insert_id($conn);
        
        // Create a notification for the new buyer registration
        $notification_message = "New buyer registration: $buyer_name";
        $current_date = date('Y-m-d');
        
        $notification_query = "INSERT INTO admin_notification (
            message, 
            date, 
            admin_role, 
            fk_wh_id
        ) VALUES (
            '$notification_message', 
            '$current_date', 
            'admin', 
            1
        )";
        
        mysqli_query($conn, $notification_query);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Registration successful! Your account is pending approval.',
            'buyer_id' => $buyer_id
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Registration failed: ' . mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Only POST method is allowed']);
}

// Close the database connection
mysqli_close($conn);
?>
