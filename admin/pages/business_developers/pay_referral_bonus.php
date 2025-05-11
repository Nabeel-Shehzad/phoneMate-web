<?php
include_once('../../../includes/init.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action']);
    exit;
}

// Get database connection
$db = new DB();
$con = $db->conn;

// Check if bonus ID is provided
if (!isset($_POST['bonus_id']) || empty($_POST['bonus_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing bonus ID']);
    exit;
}

$bonus_id = mysqli_real_escape_string($con, $_POST['bonus_id']);
$bd_id = isset($_POST['bd_id']) ? mysqli_real_escape_string($con, $_POST['bd_id']) : '';

// Begin transaction
$con->begin_transaction();

try {
    // Update the bonus record to mark it as paid
    $updateSql = "UPDATE bd_referral_bonus 
                  SET is_paid = 1, 
                      payment_date = NOW() 
                  WHERE bonus_id = '$bonus_id' AND is_paid = 0";
    
    $updateResult = $con->query($updateSql);
    
    if (!$updateResult) {
        throw new Exception("Failed to update bonus payment status");
    }
    
    // Check if any rows were affected
    if ($con->affected_rows == 0) {
        throw new Exception("Bonus not found or already paid");
    }
    
    // Get the bonus amount for notification
    $bonusSql = "SELECT brb.bonus_amount, brb.fk_bd_id, bd.bd_name 
                 FROM bd_referral_bonus brb
                 JOIN business_developer bd ON brb.fk_bd_id = bd.bd_id
                 WHERE brb.bonus_id = '$bonus_id'";
    
    $bonusResult = $con->query($bonusSql);
    
    if (!$bonusResult || $bonusResult->num_rows == 0) {
        throw new Exception("Could not retrieve bonus details");
    }
    
    $bonusData = $bonusResult->fetch_assoc();
    $bonusAmount = $bonusData['bonus_amount'];
    $bd_id = $bonusData['fk_bd_id'];
    $bd_name = $bonusData['bd_name'];
    
    // Create notification for BD
    $notificationMessage = "Your referral bonus of PKR " . number_format($bonusAmount, 2) . " has been paid.";
    $notificationSql = "INSERT INTO bd_notification (message, date, is_read, fk_bd_id) 
                        VALUES ('$notificationMessage', CURDATE(), 'unread', '$bd_id')";
    
    $notificationResult = $con->query($notificationSql);
    
    if (!$notificationResult) {
        throw new Exception("Failed to create notification");
    }
    
    // Commit transaction
    $con->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => "Referral bonus of PKR " . number_format($bonusAmount, 2) . " paid to " . $bd_name,
        'bd_id' => $bd_id
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $con->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
