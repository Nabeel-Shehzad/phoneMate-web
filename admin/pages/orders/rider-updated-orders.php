<?php include_once('../../../includes/init.php'); ?>
<?php include_once('../../includes/nav-side.php'); ?>

<?php
// Get database connection properly initialized
$db = new DB();
$con = $db->conn; // Using the public conn property instead of the private connect() method

// Calculate account summary statistics
$totalCollected = 0;
$totalPending = 0;
$totalDue = 0;
$totalInTransit = 0;
$ordersInTransit = 0;

// Get total collected amount
$collectedSql = "SELECT SUM(amount_collected) AS total_collected FROM order_delivery_status";
$collectedResult = $con->query($collectedSql);
if ($collectedResult && $collectedResult->num_rows > 0) {
  $row = $collectedResult->fetch_assoc();
  $totalCollected = $row['total_collected'] ? $row['total_collected'] : 0;
}

// Get total due amount
$dueSql = "SELECT SUM(amount_due) AS total_due FROM order_delivery_status";
$dueResult = $con->query($dueSql);
if ($dueResult && $dueResult->num_rows > 0) {
  $row = $dueResult->fetch_assoc();
  $totalDue = $row['total_due'] ? $row['total_due'] : 0;
}

// Get orders assigned to riders (in transit)
$transitSql = "SELECT COUNT(DISTINCT ora.tracking_id) AS orders_count, 
                     SUM(iso.sell_price * iso.sell_quantity) AS total_value
              FROM order_rider_assignment ora
              JOIN items_sold iso ON ora.tracking_id = iso.tracking
              WHERE ora.assignment_status = 'assigned'";
$transitResult = $con->query($transitSql);
if ($transitResult && $transitResult->num_rows > 0) {
  $row = $transitResult->fetch_assoc();
  $totalInTransit = $row['total_value'] ? $row['total_value'] : 0;
  $ordersInTransit = $row['orders_count'] ? $row['orders_count'] : 0;
}

// Group statistics by rider
$riderStatsSql = "SELECT r.rider_id, r.rider_name, 
                    SUM(ods.amount_collected) AS rider_collected, 
                    SUM(ods.amount_due) AS rider_due
                  FROM rider r
                  LEFT JOIN order_delivery_status ods ON r.rider_id = ods.fk_rider_id
                  GROUP BY r.rider_id, r.rider_name
                  ORDER BY rider_collected DESC";
$riderStatsResult = $con->query($riderStatsSql);
?>

<!-- Account Summary Cards -->
<div class="container-fluid pt-4 px-4">
  <div class="row g-4">
    <div class="col-sm-6 col-xl-4">
      <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
        <i class="fa fa-money-bill fa-3x text-primary"></i>
        <div class="ms-3">
          <p class="mb-2">Total Amount Collected</p>
          <h6 class="mb-0">PKR <?= number_format($totalCollected, 2) ?></h6>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-4">
      <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
        <i class="fa fa-hourglass-half fa-3x text-primary"></i>
        <div class="ms-3">
          <p class="mb-2">Total Amount Due</p>
          <h6 class="mb-0">PKR <?= number_format($totalDue, 2) ?></h6>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-4">
      <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
        <i class="fa fa-truck fa-3x text-primary"></i>
        <div class="ms-3">
          <p class="mb-2">Orders in Transit (<?= $ordersInTransit ?>)</p>
          <h6 class="mb-0">PKR <?= number_format($totalInTransit, 2) ?></h6>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Orders in Transit Detail -->
<div class="container-fluid pt-4 px-4">
  <div class="bg-secondary text-center rounded p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h6 class="mb-0">Orders Currently in Transit</h6>
    </div>
    <div class="table-responsive">
      <table class="table text-start align-middle table-hover mb-0">
        <thead>
          <tr class="text-white">
            <th scope="col">#</th>
            <th scope="col">Tracking ID</th>
            <th scope="col">Rider</th>
            <th scope="col">Customer</th>
            <th scope="col">Assigned Date</th>
            <th scope="col">Order Value</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Get orders in transit with details
          $inTransitSql = "SELECT ora.tracking_id, ora.assignment_date, r.rider_name, b.buyer_name,
                                    SUM(iso.sell_price * iso.sell_quantity) AS order_value
                                 FROM order_rider_assignment ora
                                 JOIN rider r ON ora.fk_rider_id = r.rider_id
                                 JOIN items_sold iso ON ora.tracking_id = iso.tracking
                                 JOIN buyer b ON iso.fk_buyer_id = b.buyer_id
                                 WHERE ora.assignment_status = 'assigned'
                                 GROUP BY ora.tracking_id, ora.assignment_date, r.rider_name, b.buyer_name
                                 ORDER BY ora.assignment_date DESC";

          $inTransitResult = $con->query($inTransitSql);
          $count = 1;

          if ($inTransitResult && $inTransitResult->num_rows > 0) {
            while ($order = $inTransitResult->fetch_assoc()) {
          ?>
              <tr>
                <td><?= $count++ ?></td>
                <td><?= $order['tracking_id'] ?></td>
                <td><?= $order['rider_name'] ?></td>
                <td><?= $order['buyer_name'] ?></td>
                <td><?= date('M d, Y', strtotime($order['assignment_date'])) ?></td>
                <td>PKR <?= number_format($order['order_value'], 2) ?></td>
              </tr>
          <?php
            }
          } else {
            echo '<tr><td colspan="6" class="text-center">No orders currently in transit</td></tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Rider Performance Table -->
<div class="container-fluid pt-4 px-4">
  <div class="bg-secondary text-center rounded p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h6 class="mb-0">Rider Collection Summary</h6>
    </div>
    <div class="table-responsive">
      <table class="table text-start align-middle table-hover mb-0">
        <thead>
          <tr class="text-white">
            <th scope="col">#</th>
            <th scope="col">Rider Name</th>
            <th scope="col">Total Collected</th>
            <th scope="col">Total Due</th>
            <th scope="col">Collection Rate</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $count = 1;
          if ($riderStatsResult && $riderStatsResult->num_rows > 0) {
            while ($rider = $riderStatsResult->fetch_assoc()) {
              $riderTotal = $rider['rider_collected'] + $rider['rider_due'];
              $collectionRate = ($riderTotal > 0) ?
                (($rider['rider_collected'] / $riderTotal) * 100) : 0;

              // Determine class based on collection rate
              if ($collectionRate >= 90) {
                $rateClass = "text-success";
              } elseif ($collectionRate >= 70) {
                $rateClass = "text-info";
              } elseif ($collectionRate >= 50) {
                $rateClass = "text-warning";
              } else {
                $rateClass = "text-danger";
              }
          ?>
              <tr>
                <td><?= $count++ ?></td>
                <td><?= $rider['rider_name'] ?></td>
                <td>PKR <?= number_format($rider['rider_collected'], 2) ?></td>
                <td>PKR <?= number_format($rider['rider_due'], 2) ?></td>
                <td class="<?= $rateClass ?>"><?= number_format($collectionRate, 1) ?>%</td>
              </tr>
          <?php
            }
          } else {
            echo '<tr><td colspan="5" class="text-center">No rider statistics available</td></tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Rider Updates Table -->
<div class="container-fluid pt-4 px-4">
  <div class="bg-secondary text-center rounded p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h6 class="mb-0">Rider Updated Orders</h6>
      <a href="">Show All</a>
    </div>
    <div class="table-responsive">
      <table class="table text-start align-middle table-hover mb-0">
        <thead>
          <tr class="text-white">
            <th scope="col">#</th>
            <th scope="col">Tracking ID</th>
            <th scope="col">Rider</th>
            <th scope="col">Status Type</th>
            <th scope="col">Update Date</th>
            <th scope="col">Amount Collected</th>
            <th scope="col">Amount Due</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Fetch orders that have been updated by riders
          $sql = "SELECT ds.*, r.rider_name 
                            FROM order_delivery_status ds
                            JOIN rider r ON ds.fk_rider_id = r.rider_id
                            ORDER BY ds.update_date DESC";
          $result = $con->query($sql);
          $count = 1;

          if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $statusClass = '';
              $statusBadge = '';

              // Set appropriate class and badge based on status
              switch ($row['status_type']) {
                case 'delivered_full':
                  $statusClass = 'bg-success';
                  $statusBadge = 'Complete Delivery';
                  break;
                case 'delivered_partial':
                  $statusClass = 'bg-warning';
                  $statusBadge = 'Partial Delivery';
                  break;
                case 'rejected':
                  $statusClass = 'bg-danger';
                  $statusBadge = 'Rejected';
                  break;
                case 'payment_due':
                  $statusClass = 'bg-info';
                  $statusBadge = 'Payment Due';
                  break;
              }
          ?>
              <tr>
                <td><?= $count++ ?></td>
                <td><?= $row['tracking_id'] ?></td>
                <td><?= $row['rider_name'] ?></td>
                <td><span class="badge <?= $statusClass ?>"><?= $statusBadge ?></span></td>
                <td><?= date('M d, Y H:i', strtotime($row['update_date'])) ?></td>
                <td>PKR <?= number_format($row['amount_collected'], 2) ?></td>
                <td>PKR <?= number_format($row['amount_due'], 2) ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-primary view-details-btn" data-id="<?= $row['status_id'] ?>">
                    <i class="fa fa-eye"></i> View Details
                  </button>
                </td>
              </tr>
          <?php
            }
          } else {
            echo '<tr><td colspan="8" class="text-center">No rider updates found</td></tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-secondary">
      <div class="modal-header">
        <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="orderDetailsContent">
        <div class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 'super_admin') { ?>
          <button type="button" class="btn btn-primary" id="markComplete">Mark as Processed</button>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<?php include_once('../../includes/footer.php'); ?>

<!-- Custom JavaScript for this page -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to all "View Details" buttons
    var viewButtons = document.querySelectorAll('.view-details-btn');
    viewButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        var statusId = this.getAttribute('data-id');
        var modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));

        // Clear previous content and show loading spinner
        document.getElementById('orderDetailsContent').innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        // Show the modal
        modal.show();

        // Fetch order details via AJAX
        fetch('get_rider_order_details.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'statusId=' + statusId
          })
          .then(response => response.text())
          .then(data => {
            document.getElementById('orderDetailsContent').innerHTML = data;
          })
          .catch(error => {
            document.getElementById('orderDetailsContent').innerHTML = '<div class="alert alert-danger">Error loading order details: ' + error + '</div>';
          });
      });
    });

    // Add click event for the "Mark as Processed" button
    var markCompleteBtn = document.getElementById('markComplete');
    if (markCompleteBtn) {
      markCompleteBtn.addEventListener('click', function() {
        var statusId = document.getElementById('orderDetailsContent').getAttribute('data-status-id');

        fetch('process_rider_update.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'statusId=' + statusId
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('Order marked as processed successfully!');
              location.reload();
            } else {
              alert('Error: ' + data.message);
            }
          })
          .catch(error => {
            alert('An error occurred while processing the request.');
          });
      });
    }
  });
</script>