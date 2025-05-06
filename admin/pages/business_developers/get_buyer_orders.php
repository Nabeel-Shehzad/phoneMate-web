<?php
include_once('../../../includes/init.php');

// Initialize database connection properly
$db = new DB();
$con = $db->conn;

// Check if buyer ID is provided
if (!isset($_POST['buyer_id']) || empty($_POST['buyer_id'])) {
    echo '<div class="alert alert-danger">Invalid request</div>';
    exit;
}

$buyer_id = mysqli_real_escape_string($con, $_POST['buyer_id']);

// Get buyer information
$buyerSql = "SELECT * FROM buyer WHERE buyer_id = '$buyer_id'";
$buyerResult = $con->query($buyerSql);

if (!$buyerResult || $buyerResult->num_rows == 0) {
    echo '<div class="alert alert-danger">Buyer not found</div>';
    exit;
}

$buyer = $buyerResult->fetch_assoc();

// Get orders for this buyer
$ordersSql = "SELECT iso.*, 
                i.item_brand, i.item_number, i.item_description, i.item_image,
                ods.status_type, ods.amount_collected, ods.amount_due, ods.update_date
              FROM items_sold iso
              JOIN items i ON iso.fk_item_id = i.item_id
              LEFT JOIN order_delivery_status ods ON iso.tracking = ods.tracking_id
              WHERE iso.fk_buyer_id = '$buyer_id'
              ORDER BY iso.sell_date DESC";

$ordersResult = $con->query($ordersSql);

// Display buyer header
echo '<div class="mb-3">
        <h5>' . htmlspecialchars($buyer['buyer_name']) . '</h5>
        <p><strong>Contact:</strong> ' . htmlspecialchars($buyer['buyer_contact']) . '</p>
        <p><strong>Address:</strong> ' . htmlspecialchars($buyer['buyer_address']) . '</p>
      </div>';

// Display orders
if ($ordersResult && $ordersResult->num_rows > 0) {
    echo '<div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Tracking ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>';
    
    $currentTracking = '';
    $trackingTotals = [];
    
    while ($order = $ordersResult->fetch_assoc()) {
        // Calculate total for this item
        $itemTotal = $order['sell_price'] * $order['sell_quantity'];
        
        // Track totals by tracking ID
        if (!isset($trackingTotals[$order['tracking']])) {
            $trackingTotals[$order['tracking']] = [
                'total' => 0,
                'collected' => $order['amount_collected'] ?: 0,
                'due' => $order['amount_due'] ?: 0,
                'status' => $order['status_type'] ?: 'pending'
            ];
        }
        $trackingTotals[$order['tracking']]['total'] += $itemTotal;
        
        // Determine status class
        $statusClass = '';
        $statusText = '';
        
        switch ($order['status_type']) {
            case 'delivered_full':
                $statusClass = 'bg-success';
                $statusText = 'Complete Delivery';
                break;
            case 'delivered_partial':
                $statusClass = 'bg-warning';
                $statusText = 'Partial Delivery';
                break;
            case 'rejected':
                $statusClass = 'bg-danger';
                $statusText = 'Rejected';
                break;
            case 'payment_due':
                $statusClass = 'bg-info';
                $statusText = 'Payment Due';
                break;
            default:
                $statusClass = 'bg-secondary';
                $statusText = 'Pending';
        }
        
        // If this is a new tracking ID, add a separator row
        if ($currentTracking != $order['tracking'] && $currentTracking != '') {
            echo '<tr class="bg-dark"><td colspan="8"></td></tr>';
        }
        $currentTracking = $order['tracking'];
        
        echo '<tr>
                <td>' . $order['tracking'] . '</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="../../../uploads/item-images/' . $order['item_image'] . '" alt="Product" width="40" class="me-2">
                        <div>
                            <strong>' . $order['item_brand'] . ' ' . $order['item_number'] . '</strong><br>
                            <small>' . substr($order['item_description'], 0, 50) . (strlen($order['item_description']) > 50 ? '...' : '') . '</small>
                        </div>
                    </div>
                </td>
                <td>' . $order['sell_quantity'] . '</td>
                <td>PKR ' . number_format($order['sell_price'], 2) . '</td>
                <td>PKR ' . number_format($itemTotal, 2) . '</td>
                <td>' . date('M d, Y', strtotime($order['sell_date'])) . '</td>
                <td><span class="badge ' . $statusClass . '">' . $statusText . '</span></td>
                <td>';
        
        // Show payment status
        if ($order['status_type'] == 'payment_due') {
            echo '<span class="text-danger">Due: PKR ' . number_format($order['amount_due'], 2) . '</span>';
        } elseif ($order['status_type'] == 'delivered_full' || $order['status_type'] == 'delivered_partial') {
            echo '<span class="text-success">Paid: PKR ' . number_format($order['amount_collected'], 2) . '</span>';
            if ($order['amount_due'] > 0) {
                echo '<br><span class="text-danger">Due: PKR ' . number_format($order['amount_due'], 2) . '</span>';
            }
        }
        
        echo '</td>
              </tr>';
    }
    
    echo '</tbody>
          </table>
          </div>';
    
    // Display order summary
    echo '<div class="row mt-4">
            <div class="col-12">
                <h6>Order Summary</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tracking ID</th>
                                <th>Total Value</th>
                                <th>Amount Collected</th>
                                <th>Amount Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
    
    foreach ($trackingTotals as $tracking => $data) {
        $statusClass = '';
        switch ($data['status']) {
            case 'delivered_full':
                $statusClass = 'bg-success';
                $statusText = 'Complete Delivery';
                break;
            case 'delivered_partial':
                $statusClass = 'bg-warning';
                $statusText = 'Partial Delivery';
                break;
            case 'rejected':
                $statusClass = 'bg-danger';
                $statusText = 'Rejected';
                break;
            case 'payment_due':
                $statusClass = 'bg-info';
                $statusText = 'Payment Due';
                break;
            default:
                $statusClass = 'bg-secondary';
                $statusText = 'Pending';
        }
        
        echo '<tr>
                <td>' . $tracking . '</td>
                <td>PKR ' . number_format($data['total'], 2) . '</td>
                <td>PKR ' . number_format($data['collected'], 2) . '</td>
                <td>PKR ' . number_format($data['due'], 2) . '</td>
                <td><span class="badge ' . $statusClass . '">' . $statusText . '</span></td>
              </tr>';
    }
    
    echo '</tbody>
          </table>
          </div>
          </div>
          </div>';
} else {
    echo '<div class="alert alert-warning">No orders found for this buyer</div>';
}
?>
