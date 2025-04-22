<?php
include_once('../../../includes/init.php');

// Initialize database connection properly
$db = new DB();
$con = $db->conn; // Using the public conn property instead of the private connect() method

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if status ID is provided
if (!isset($_POST['statusId']) || empty($_POST['statusId'])) {
  echo '<div class="alert alert-danger">Invalid request</div>';
  exit;
}

$statusId = mysqli_real_escape_string($con, $_POST['statusId']);

// Fetch delivery status information
$sql = "SELECT ds.*, r.rider_name 
        FROM order_delivery_status ds
        JOIN rider r ON ds.fk_rider_id = r.rider_id
        WHERE ds.status_id = '$statusId'";

$result = $con->query($sql);

if ($result && $result->num_rows > 0) {
  $status = $result->fetch_assoc();

  // Start output - add id to make it easier to select
  echo '<div id="orderDetailsData" data-status-id="' . $statusId . '" data-status-type="' . $status['status_type'] . '" data-amount-due="' . $status['amount_due'] . '">';

  // Status badge
  $statusClass = '';
  $statusText = '';
  switch ($status['status_type']) {
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
  }

  // Display order header information
  echo '<div class="row mb-3">
            <div class="col-md-6">
                <h5>Tracking ID: ' . $status['tracking_id'] . '</h5>
                <span class="badge ' . $statusClass . '">' . $statusText . '</span>
                <p class="mt-2"><strong>Updated:</strong> ' . date('M d, Y H:i', strtotime($status['update_date'])) . '</p>
            </div>
            <div class="col-md-6">
                <p><strong>Rider:</strong> ' . $status['rider_name'] . '</p>
                <p><strong>Amount Collected:</strong> PKR ' . number_format($status['amount_collected'], 2) . '</p>
                <p><strong>Amount Due:</strong> PKR ' . number_format($status['amount_due'], 2) . '</p>
            </div>
          </div>';

  // Get buyer information from tracking ID
  $buyerSql = "SELECT b.buyer_name, b.buyer_address, b.buyer_contact 
                FROM items_sold iso
                JOIN buyer b ON iso.fk_buyer_id = b.buyer_id
                WHERE iso.tracking = '{$status['tracking_id']}'
                LIMIT 1";

  $buyerResult = $con->query($buyerSql);
  $buyer = ($buyerResult && $buyerResult->num_rows > 0) ? $buyerResult->fetch_assoc() : null;

  if ($buyer) {
    echo '<div class="row mb-3">
                <div class="col-md-12">
                    <h6 class="mb-2">Customer Information</h6>
                    <p><strong>Name:</strong> ' . $buyer['buyer_name'] . '</p>
                    <p><strong>Contact:</strong> ' . $buyer['buyer_contact'] . '</p>
                    <p><strong>Address:</strong> ' . $buyer['buyer_address'] . '</p>
                </div>
              </div>';
  }

  // Display notes if available
  if (!empty($status['notes'])) {
    echo '<div class="row mb-3">
                <div class="col-md-12">
                    <h6>Rider Notes</h6>
                    <p class="p-2 border rounded">' . nl2br(htmlspecialchars($status['notes'])) . '</p>
                </div>
              </div>';
  }

  // If order was rejected, show rejection reason
  if ($status['status_type'] == 'rejected' && !empty($status['rejection_reason'])) {
    echo '<div class="row mb-3">
                <div class="col-md-12">
                    <h6>Rejection Reason</h6>
                    <p class="p-2 border rounded bg-danger text-white">' . nl2br(htmlspecialchars($status['rejection_reason'])) . '</p>
                </div>
              </div>';
  }

  // Check if there's returned items information for partial deliveries
  $returnedItems = [];
  if ($status['status_type'] == 'delivered_partial' && !empty($status['returned_items'])) {
    try {
      // The returned_items field might be a JSON string with item details
      $returnedItems = json_decode($status['returned_items'], true);
      if (!is_array($returnedItems)) {
        $returnedItems = [];
      }
    } catch (Exception $e) {
      // If there's an error parsing JSON, just continue with empty array
      $returnedItems = [];
    }
  }

  // CORRECTED APPROACH: First check if there are any items in order_item_status
  $itemsSql = "SELECT ois.*, iso.sell_quantity, iso.sell_price, i.item_brand, i.item_number, i.item_description, i.item_image
               FROM order_item_status ois
               JOIN items_sold iso ON ois.fk_sell_id = iso.sell_id
               JOIN items i ON iso.fk_item_id = i.item_id
               WHERE ois.fk_status_id = '$statusId'";

  $itemsResult = $con->query($itemsSql);

  // If there are no records in order_item_status, get items directly from items_sold table
  if (!$itemsResult || $itemsResult->num_rows == 0) {
    // Get items directly from the items_sold table using tracking ID
    $trackingId = $status['tracking_id'];

    $itemsSql = "SELECT iso.sell_id, iso.sell_quantity, iso.sell_price, iso.sell_status, 
                      i.item_id, i.item_brand, i.item_number, i.item_description, i.item_image
                FROM items_sold iso
                JOIN items i ON iso.fk_item_id = i.item_id
                WHERE iso.tracking = '$trackingId'";

    $itemsResult = $con->query($itemsSql);
  }

  // Display items
  if ($itemsResult && $itemsResult->num_rows > 0) {
    echo '<h6 class="mb-3">Order Items</h6>
              <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Ordered</th>
                            <th>Status</th>
                            <th>Delivered</th>
                            <th>Returned</th>
                        </tr>
                    </thead>
                    <tbody>';

    while ($item = $itemsResult->fetch_assoc()) {
      $itemStatusClass = '';
      $itemId = isset($item['item_id']) ? $item['item_id'] : 0;
      $sellId = isset($item['sell_id']) ? $item['sell_id'] : 0;

      // Check if the status comes from order_item_status or items_sold
      if (isset($item['item_status'])) {
        // This is from order_item_status
        $itemStatus = $item['item_status'];
        $deliveredQty = $item['delivered_quantity'];
        $returnedQty = $item['returned_quantity'];
      } else {
        // This is from items_sold
        // Default status based on the overall delivery status and any individual item status
        switch ($status['status_type']) {
          case 'delivered_full':
            $itemStatus = 'delivered';
            $deliveredQty = $item['sell_quantity'];
            $returnedQty = 0;
            break;

          case 'delivered_partial':
            // For partial delivery, check if this item is in the returned_items array
            $itemStatus = isset($item['sell_status']) && $item['sell_status'] != '' ? $item['sell_status'] : 'delivered';

            // Set default values
            $deliveredQty = $item['sell_quantity'];
            $returnedQty = 0;

            // Check if this item is in the returned_items list
            foreach ($returnedItems as $returnedItem) {
              if (isset($returnedItem['sell_id']) && $returnedItem['sell_id'] == $sellId) {
                $itemStatus = 'returned';
                $returnedQty = isset($returnedItem['quantity']) ? $returnedItem['quantity'] : $item['sell_quantity'];
                $deliveredQty = $item['sell_quantity'] - $returnedQty;
                break;
              }
            }

            // If sell_status is set to 'returned' but not in the returned_items array
            if ($itemStatus == 'returned' && $returnedQty == 0) {
              $returnedQty = $item['sell_quantity'];
              $deliveredQty = 0;
            }

            // If no specific info, split based on overall order status
            if ($itemStatus != 'returned' && $returnedQty == 0 && $status['status_type'] == 'delivered_partial') {
              // Default for partial deliveries: assume half delivered, half returned
              if (empty($returnedItems)) {
                $halfQty = ceil($item['sell_quantity'] / 2);
                $deliveredQty = $halfQty;
                $returnedQty = $item['sell_quantity'] - $halfQty;
              }
            }
            break;

          case 'rejected':
            $itemStatus = 'rejected';
            $deliveredQty = 0;
            $returnedQty = $item['sell_quantity'];
            break;

          case 'payment_due':
            $itemStatus = 'delivered';
            $deliveredQty = $item['sell_quantity'];
            $returnedQty = 0;
            break;

          default:
            $itemStatus = 'pending';
            $deliveredQty = 0;
            $returnedQty = 0;
        }
      }

      switch ($itemStatus) {
        case 'delivered':
          $itemStatusClass = 'bg-success';
          break;
        case 'returned':
          $itemStatusClass = 'bg-warning';
          break;
        case 'rejected':
          $itemStatusClass = 'bg-danger';
          break;
        default:
          $itemStatusClass = 'bg-secondary';
      }

      echo '<tr>
                    <td><img src="../../../uploads/item-images/' . $item['item_image'] . '" alt="Product" width="50"></td>
                    <td>
                        <strong>' . $item['item_brand'] . ' ' . $item['item_number'] . '</strong><br>
                        <small>' . $item['item_description'] . '</small>
                    </td>
                    <td>PKR ' . number_format($item['sell_price'], 2) . '</td>
                    <td>' . $item['sell_quantity'] . '</td>
                    <td><span class="badge ' . $itemStatusClass . '">' . ucfirst($itemStatus) . '</span></td>
                    <td>' . $deliveredQty . '</td>
                    <td>' . $returnedQty . '</td>
                  </tr>';
    }

    echo '</tbody></table></div>';
  } else {
    echo '<div class="alert alert-warning">No items found for this order. The tracking ID "' . $status['tracking_id'] . '" does not match any items in the database.</div>';
  }

  // Add Clear Dues button directly in the content for payment_due orders
  if ($status['status_type'] == 'payment_due' && $status['amount_due'] > 0) {
    echo '<div class="row mt-4">
            <div class="col-md-12 text-center">
              <button type="button" class="btn btn-danger btn-lg" id="clearDuesInline" onclick="clearBuyerDues(' . $statusId . ')">Clear Buyer Dues (PKR ' . number_format($status['amount_due'], 2) . ')</button>
            </div>
          </div>';
  }
  
  echo '</div>';
} else {
  echo '<div class="alert alert-danger">Order status not found</div>';
}
