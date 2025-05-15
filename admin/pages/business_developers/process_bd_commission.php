<?php
include_once('../../../includes/init.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $db = new DB();
  $con = $db->conn;

  $commissionId = isset($_POST['commission_id']) ? intval($_POST['commission_id']) : 0;

  if ($commissionId > 0) {
    // Begin transaction
    $con->begin_transaction();

    try {
      // Update commission record
      $updateQuery = "UPDATE bd_monthly_commission 
                          SET is_paid = 1, payment_date = NOW() 
                          WHERE commission_id = ? AND is_paid = 0";
      $stmt = $con->prepare($updateQuery);
      $stmt->bind_param("i", $commissionId);
      $stmt->execute();

      if ($stmt->affected_rows > 0) {
        // Get commission details for notification
        $detailsQuery = "SELECT bmc.*, bd.bd_name 
                               FROM bd_monthly_commission bmc
                               JOIN business_developer bd ON bmc.fk_bd_id = bd.bd_id
                               WHERE bmc.commission_id = ?";
        $detailsStmt = $con->prepare($detailsQuery);
        $detailsStmt->bind_param("i", $commissionId);
        $detailsStmt->execute();
        $commission = $detailsStmt->get_result()->fetch_assoc();

        // Create notification
        $message = "Your commission payment of PKR " .
          number_format($commission['commission_amount'], 2) .
          " for " . date('F Y', strtotime($commission['commission_month'])) .
          " has been processed.";

        $notifyQuery = "INSERT INTO bd_notification (message, date, fk_bd_id) 
                               VALUES (?, NOW(), ?)";
        $notifyStmt = $con->prepare($notifyQuery);
        $notifyStmt->bind_param("si", $message, $commission['fk_bd_id']);
        $notifyStmt->execute();

        $con->commit();
        echo json_encode(['success' => true, 'message' => 'Commission payment processed successfully']);
      } else {
        throw new Exception("Commission payment already processed or not found");
      }
    } catch (Exception $e) {
      $con->rollback();
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
  } else {
    echo json_encode(['success' => false, 'message' => 'Invalid commission ID']);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
