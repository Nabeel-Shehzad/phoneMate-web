<?php
include_once('../../../includes/init.php');

// Initialize database connection properly
$db = new DB();
$con = $db->conn; // Using the public conn property instead of the private connect() method

// Check if status ID is provided and user has permission
if (!isset($_POST['statusId']) || empty($_POST['statusId']) || !isset($_SESSION['admin_role']) || $_SESSION['admin_role'] != 'super_admin') {
  echo json_encode(['success' => false, 'message' => 'Invalid request or insufficient permissions']);
  exit;
}

$statusId = mysqli_real_escape_string($con, $_POST['statusId']);

// Start transaction
$con->begin_transaction();

try {
  // Get delivery status info
  $statusSql = "SELECT ds.*, ora.assignment_id 
                 FROM order_delivery_status ds
                 LEFT JOIN order_rider_assignment ora ON ds.tracking_id = ora.tracking_id
                 WHERE ds.status_id = '$statusId'";

  $statusResult = $con->query($statusSql);

  if (!$statusResult || $statusResult->num_rows == 0) {
    throw new Exception("Order status not found");
  }

  $status = $statusResult->fetch_assoc();

  // Get order items
  $itemsSql = "SELECT ois.*, is.sell_id
                FROM order_item_status ois
                JOIN items_sold is ON ois.fk_sell_id = is.sell_id
                WHERE ois.fk_status_id = '$statusId'";

  $itemsResult = $con->query($itemsSql);

  if (!$itemsResult) {
    throw new Exception("Failed to retrieve order items");
  }

  // Process each item in the order
  while ($item = $itemsResult->fetch_assoc()) {
    $sellId = $item['sell_id'];
    $status_name = '';

    // Update items_sold table based on item status
    switch ($item['item_status']) {
      case 'delivered':
        $status_name = 'delivered';
        break;
      case 'returned':
        $status_name = 'returned';
        // For returned items, you might want to add logic to update inventory
        break;
      case 'rejected':
        $status_name = 'rejected';
        // For rejected items, you might want to add logic to update inventory
        break;
    }

    // Update the order status in items_sold
    $updateSql = "UPDATE items_sold SET sell_status = '$status_name' WHERE sell_id = '$sellId'";
    if (!$con->query($updateSql)) {
      throw new Exception("Failed to update item status: " . $con->error);
    }
  }

  // Mark rider assignment as completed
  if (!empty($status['assignment_id'])) {
    $updateAssignmentSql = "UPDATE order_rider_assignment SET assignment_status = 'completed' WHERE assignment_id = '{$status['assignment_id']}'";
    if (!$con->query($updateAssignmentSql)) {
      throw new Exception("Failed to update rider assignment: " . $con->error);
    }
  }

  // Record any payment dues if applicable
  if ($status['status_type'] == 'payment_due' && $status['amount_due'] > 0) {
    // Add code here to record the payment due in the appropriate table
    // This would depend on your specific business requirements
  }

  // Add a notification for the admin dashboard
  $trackingId = $status['tracking_id'];
  $riderId = $status['fk_rider_id'];

  // Get rider name for the notification
  $riderSql = "SELECT rider_name FROM rider WHERE rider_id = '$riderId'";
  $riderResult = $con->query($riderSql);
  $rider = ($riderResult && $riderResult->num_rows > 0) ? $riderResult->fetch_assoc() : ['rider_name' => 'Unknown'];

  $message = "Order #$trackingId has been processed after delivery by {$rider['rider_name']}.";
  $date = date('Y-m-d');

  // Use warehouse ID from session or default to 1
  $warehouseId = isset($_SESSION['fk_wh_id']) ? $_SESSION['fk_wh_id'] : 1;

  $notificationSql = "INSERT INTO admin_notification (message, date, admin_role, fk_wh_id) 
                        VALUES ('$message', '$date', 'super_admin', '$warehouseId')";

  if (!$con->query($notificationSql)) {
    throw new Exception("Failed to create notification: " . $con->error);
  }

  // Commit the transaction
  $con->commit();

  echo json_encode(['success' => true, 'message' => 'Order processed successfully']);
} catch (Exception $e) {
  // Rollback if any error occurs
  $con->rollback();
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
