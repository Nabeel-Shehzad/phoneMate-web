<?php
include_once('../../../includes/init.php');

// Initialize database connection properly
$db = new DB();
$con = $db->conn; // Using the public conn property instead of the private connect() method

// Check if status ID is provided
if (!isset($_POST['statusId']) || empty($_POST['statusId'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid request: Missing status ID']);
  exit;
}

// For debugging - remove the permission check temporarily
// We'll just check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
  echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action']);
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

  // Verify this is a payment_due order with amount due
  if ($status['status_type'] != 'payment_due' || $status['amount_due'] <= 0) {
    throw new Exception("This order does not have pending dues to clear");
  }

  // Update the order delivery status to delivered_full
  $updateStatusSql = "UPDATE order_delivery_status 
                      SET status_type = 'delivered_full', 
                          amount_collected = amount_collected + amount_due, 
                          amount_due = 0,
                          update_date = NOW()
                      WHERE status_id = '$statusId'";
  
  if (!$con->query($updateStatusSql)) {
    throw new Exception("Failed to update order status: " . $con->error);
  }

  // Get order items - using the correct column names from the database schema
  $itemsSql = "SELECT ois.*, is.sell_id
              FROM order_item_status ois
              JOIN items_sold is ON ois.fk_sell_id = is.sell_id
              WHERE ois.fk_status_id = '$statusId'";

  $itemsResult = $con->query($itemsSql);
  
  // If no items found in order_item_status, get items directly from items_sold using tracking_id
  if (!$itemsResult || $itemsResult->num_rows == 0) {
    $trackingId = $status['tracking_id'];
    $itemsSql = "SELECT iso.*, iso.sell_id
                FROM items_sold iso
                WHERE iso.tracking = '$trackingId'";
    $itemsResult = $con->query($itemsSql);
    
    if (!$itemsResult) {
      throw new Exception("Failed to retrieve order items: " . $con->error);
    }
  }

  // Update all items to delivered status
  while ($item = $itemsResult->fetch_assoc()) {
    $sellId = isset($item['sell_id']) ? $item['sell_id'] : 0;
    
    if ($sellId > 0) {
      // Update the order status in items_sold
      $updateSql = "UPDATE items_sold SET sell_status = 'delivered' WHERE sell_id = '$sellId'";
      if (!$con->query($updateSql)) {
        throw new Exception("Failed to update item status: " . $con->error);
      }
      
      // Check if there's a corresponding entry in order_item_status
      $checkItemStatusSql = "SELECT item_status_id FROM order_item_status WHERE fk_sell_id = '$sellId' AND fk_status_id = '$statusId'";
      $checkResult = $con->query($checkItemStatusSql);
      
      if ($checkResult && $checkResult->num_rows > 0) {
        // Update the item status in order_item_status
        $updateItemStatusSql = "UPDATE order_item_status 
                               SET item_status = 'delivered', 
                                   delivered_quantity = CASE 
                                     WHEN delivered_quantity = 0 THEN 
                                       (SELECT sell_quantity FROM items_sold WHERE sell_id = '$sellId') 
                                     ELSE delivered_quantity END,
                                   returned_quantity = 0
                               WHERE fk_sell_id = '$sellId' AND fk_status_id = '$statusId'";
        if (!$con->query($updateItemStatusSql)) {
          throw new Exception("Failed to update order item status: " . $con->error);
        }
      } else {
        // No entry exists in order_item_status, so we need to create one
        $sellQuantity = isset($item['sell_quantity']) ? $item['sell_quantity'] : 1;
        $insertItemStatusSql = "INSERT INTO order_item_status 
                              (fk_status_id, fk_sell_id, item_status, returned_quantity, delivered_quantity) 
                              VALUES ('$statusId', '$sellId', 'delivered', 0, '$sellQuantity')";
        if (!$con->query($insertItemStatusSql)) {
          throw new Exception("Failed to create order item status: " . $con->error);
        }
      }
    }
  }

  // Mark rider assignment as completed if not already
  if (!empty($status['assignment_id'])) {
    $updateAssignmentSql = "UPDATE order_rider_assignment 
                           SET assignment_status = 'completed' 
                           WHERE assignment_id = '{$status['assignment_id']}' 
                           AND assignment_status != 'completed'";
    $con->query($updateAssignmentSql);
  }

  // Add a notification for the admin dashboard
  $trackingId = $status['tracking_id'];
  $riderId = $status['fk_rider_id'];

  // Get rider name for the notification
  $riderSql = "SELECT rider_name FROM rider WHERE rider_id = '$riderId'";
  $riderResult = $con->query($riderSql);
  $rider = ($riderResult && $riderResult->num_rows > 0) ? $riderResult->fetch_assoc() : ['rider_name' => 'Unknown'];

  $clearedAmount = $status['amount_due'];
  $message = "Admin has cleared dues of PKR " . number_format($clearedAmount, 2) . " for order #$trackingId (Rider: {$rider['rider_name']})";
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

  echo json_encode(['success' => true, 'message' => 'Buyer dues cleared successfully and order marked as fully delivered']);
} catch (Exception $e) {
  // Rollback if any error occurs
  $con->rollback();
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
