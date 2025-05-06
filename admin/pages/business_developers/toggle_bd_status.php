<?php
include_once('../../../includes/init.php');

// Initialize database connection properly
$db = new DB();
$con = $db->conn;

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action']);
    exit;
}

// Check if BD ID and new status are provided
if (!isset($_POST['bd_id']) || empty($_POST['bd_id']) || !isset($_POST['new_status']) || empty($_POST['new_status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request: Missing required parameters']);
    exit;
}

$bd_id = mysqli_real_escape_string($con, $_POST['bd_id']);
$new_status = mysqli_real_escape_string($con, $_POST['new_status']);

// Validate status value
if ($new_status != 'active' && $new_status != 'inactive') {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit;
}

// Update BD status
$updateSql = "UPDATE business_developer SET bd_status = '$new_status' WHERE bd_id = '$bd_id'";

if ($con->query($updateSql)) {
    // Get BD name for notification
    $bdSql = "SELECT bd_name FROM business_developer WHERE bd_id = '$bd_id'";
    $bdResult = $con->query($bdSql);
    $bd = ($bdResult && $bdResult->num_rows > 0) ? $bdResult->fetch_assoc() : ['bd_name' => 'Unknown'];
    
    // Create notification for the BD
    $notificationMessage = "Your account status has been changed to " . ucfirst($new_status) . " by the admin.";
    $date = date('Y-m-d');
    
    $notificationSql = "INSERT INTO bd_notification (message, date, is_read, fk_bd_id) 
                       VALUES ('$notificationMessage', '$date', 'unread', '$bd_id')";
    $con->query($notificationSql);
    
    // Create admin notification
    $adminNotificationMessage = "Business Developer " . $bd['bd_name'] . " status changed to " . ucfirst($new_status);
    $warehouseId = isset($_SESSION['fk_wh_id']) ? $_SESSION['fk_wh_id'] : 1;
    
    $adminNotificationSql = "INSERT INTO admin_notification (message, date, is_read, admin_role, fk_wh_id) 
                           VALUES ('$adminNotificationMessage', '$date', 'unread', 'super_admin', '$warehouseId')";
    $con->query($adminNotificationSql);
    
    echo json_encode(['success' => true, 'message' => 'Business developer status updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . $con->error]);
}
?>
