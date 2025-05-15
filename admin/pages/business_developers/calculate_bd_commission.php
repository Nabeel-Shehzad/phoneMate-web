<?php
include_once('../../../includes/init.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bd_id'])) {
  // Calculate for specific BD
  $result = calculateMonthlyCommissions(null, intval($_POST['bd_id']));
  echo json_encode($result);
  exit;
}

function calculateMonthlyCommissions($month = null, $specific_bd_id = null)
{
  $db = new DB();
  $con = $db->conn;

  // If no month specified, calculate for current month
  if ($month === null) {
    $month = date('Y-m-01'); // Current month
  }

  $monthStart = date('Y-m-01', strtotime($month));
  $monthEnd = date('Y-m-t', strtotime($month));

  // Get business developers - either specific one or all active ones
  $bdQuery = "SELECT bd_id, bd_name FROM business_developer WHERE bd_status = 'active'";
  if ($specific_bd_id !== null) {
    $bdQuery .= " AND bd_id = " . intval($specific_bd_id);
  }

  $bdResult = $con->query($bdQuery);
  $success = true;
  $message = "Commission calculation completed successfully";

  while ($bd = $bdResult->fetch_assoc()) {
    $bd_id = $bd['bd_id'];

    // Calculate total sales for this BD's buyers for the specified month
    $salesQuery = "SELECT 
                COALESCE(SUM(iso.sell_price * iso.sell_quantity), 0) as total_sales
            FROM business_developer bd
            JOIN buyer b ON bd.bd_id = b.fk_bd_id
            JOIN items_sold iso ON b.buyer_id = iso.fk_buyer_id
            WHERE bd.bd_id = ? 
            AND iso.sell_date BETWEEN ? AND ?
            AND iso.sell_status = 'completed'";

    $stmt = $con->prepare($salesQuery);
    $stmt->bind_param("iss", $bd_id, $monthStart, $monthEnd);
    $stmt->execute();
    $result = $stmt->get_result();
    $salesData = $result->fetch_assoc();

    $totalSales = $salesData['total_sales'];
    $commissionAmount = $totalSales * 0.01; // 1% commission

    // Check if commission record already exists
    $checkQuery = "SELECT commission_id FROM bd_monthly_commission 
                      WHERE fk_bd_id = ? AND commission_month = ?";
    $checkStmt = $con->prepare($checkQuery);
    $checkStmt->bind_param("is", $bd_id, $monthStart);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
      // Insert new commission record
      $insertQuery = "INSERT INTO bd_monthly_commission 
                          (fk_bd_id, commission_month, total_sales, commission_amount) 
                          VALUES (?, ?, ?, ?)";
      $insertStmt = $con->prepare($insertQuery);
      $insertStmt->bind_param("isdd", $bd_id, $monthStart, $totalSales, $commissionAmount);

      if (!$insertStmt->execute()) {
        $success = false;
        $message = "Error calculating commission for " . $bd['bd_name'];
        break;
      }

      // Create notification for BD
      $message = "Your commission for " . date('F Y', strtotime($monthStart)) .
        " has been calculated. Amount: PKR " . number_format($commissionAmount, 2);

      $notifyQuery = "INSERT INTO bd_notification (message, date, fk_bd_id) 
                           VALUES (?, NOW(), ?)";
      $notifyStmt = $con->prepare($notifyQuery);
      $notifyStmt->bind_param("si", $message, $bd_id);
      $notifyStmt->execute();
    }
  }

  return [
    'success' => $success,
    'message' => $message
  ];
}

// If script is run directly, calculate for all BDs
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
  $result = calculateMonthlyCommissions();
  echo json_encode($result);
}
