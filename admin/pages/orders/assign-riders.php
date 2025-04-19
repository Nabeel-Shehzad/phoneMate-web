<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
// Create database connection
$db = new DB();
$conn = $db->conn;

// Process form submission for rider assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_rider'])) {
    // Get form data
    $tracking_id = mysqli_real_escape_string($conn, $_POST['tracking_id']);
    $rider_id = mysqli_real_escape_string($conn, $_POST['rider_id']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $current_date = date('Y-m-d');
    
    // Check if this order has already been assigned
    $check_query = "SELECT * FROM order_rider_assignment WHERE tracking_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $tracking_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Order already assigned - update the assignment
        $update_query = "UPDATE order_rider_assignment SET fk_rider_id = ?, assignment_date = ?, notes = ? WHERE tracking_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("isss", $rider_id, $current_date, $notes, $tracking_id);
        
        if ($stmt->execute()) {
            // Update the notification to the rider
            $get_rider_query = "SELECT rider_name FROM rider WHERE rider_id = ?";
            $rider_stmt = $conn->prepare($get_rider_query);
            $rider_stmt->bind_param("i", $rider_id);
            $rider_stmt->execute();
            $rider_result = $rider_stmt->get_result();
            $rider_data = $rider_result->fetch_assoc();
            
            // Create notification for rider
            $notification_msg = "Order #" . $tracking_id . " has been assigned to you";
            $rider_notif_query = "INSERT INTO rider_notification (message, date, fk_rider_id) VALUES (?, ?, ?)";
            $notif_stmt = $conn->prepare($rider_notif_query);
            $notif_stmt->bind_param("ssi", $notification_msg, $current_date, $rider_id);
            $notif_stmt->execute();
            
            echo "<div class='alert alert-success'>Order reassigned successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating assignment: " . $stmt->error . "</div>";
        }
    } else {
        // New assignment
        $insert_query = "INSERT INTO order_rider_assignment (tracking_id, fk_rider_id, assignment_date, notes) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("siss", $tracking_id, $rider_id, $current_date, $notes);
        
        if ($stmt->execute()) {
            // Create notification for rider
            $notification_msg = "New order #" . $tracking_id . " has been assigned to you";
            $rider_notif_query = "INSERT INTO rider_notification (message, date, fk_rider_id) VALUES (?, ?, ?)";
            $notif_stmt = $conn->prepare($rider_notif_query);
            $notif_stmt->bind_param("ssi", $notification_msg, $current_date, $rider_id);
            $notif_stmt->execute();
            
            echo "<div class='alert alert-success'>Order assigned successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error assigning order: " . $stmt->error . "</div>";
        }
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Orders</h3>

        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Assign Riders to Orders</h4>                <div class="table-responsive">
                    <table id="approved_orders" class="table table-dark table-striped table-hover">
                        <thead>
                            <tr class="text-white">
                                <th>Order ID</th>
                                <th>Items</th>
                                <th>Buyer</th>
                                <th>Selling Price</th>
                                <th>Date Approved</th>
                                <th>Current Rider</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php                            // Get all approved orders that need to be assigned to riders
                            $query = "SELECT 
                                        items_sold.tracking, 
                                        COUNT(DISTINCT items_sold.fk_item_id) as item_count,
                                        GROUP_CONCAT(DISTINCT items.item_category) as categories,
                                        GROUP_CONCAT(DISTINCT items.item_brand) as brands,
                                        GROUP_CONCAT(DISTINCT items.item_number SEPARATOR ', ') as product_names,
                                        buyer.buyer_name,
                                        buyer.buyer_id,
                                        SUM(items_sold.sell_price) as total_price,
                                        MIN(items_sold.sell_date) as order_date,
                                        r.rider_name,
                                        ra.fk_rider_id
                                    FROM items_sold
                                    INNER JOIN items ON items_sold.fk_item_id = items.item_id
                                    INNER JOIN buyer ON items_sold.fk_buyer_id = buyer.buyer_id
                                    LEFT JOIN order_rider_assignment ra ON items_sold.tracking = ra.tracking_id
                                    LEFT JOIN rider r ON ra.fk_rider_id = r.rider_id
                                    WHERE items_sold.sell_status = 'approved'
                                    GROUP BY items_sold.tracking
                                    ORDER BY order_date DESC";
                                    
                            $result = mysqli_query($conn, $query);                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Get order items details
                                    $tracking_id = $row['tracking'];
                                    $items_query = "SELECT 
                                        items.item_category,
                                        items.item_brand,
                                        items.item_number,
                                        items.item_image,
                                        items_sold.sell_quantity,
                                        items_sold.sell_price
                                    FROM items_sold 
                                    INNER JOIN items ON items_sold.fk_item_id = items.item_id
                                    WHERE items_sold.tracking = '$tracking_id'";
                                    
                                    $items_result = mysqli_query($conn, $items_query);
                                    $items_data = [];
                                    while($item = mysqli_fetch_assoc($items_result)) {
                                        $items_data[] = $item;
                                    }
                            ?>
                                    <tr>                                        <td><span class="badge bg-primary"><?php echo $row['tracking']; ?></span></td>
                                        <td>
                                            <div class="position-relative">
                                                <a href="#" class="item-details-badge badge bg-info text-dark" data-tracking="<?php echo $row['tracking']; ?>">
                                                    <?php echo $row['item_count']; ?> 
                                                    <?php echo ($row['item_count'] > 1) ? 'items' : 'item'; ?>
                                                    <i class="fa fa-caret-down ms-1"></i>
                                                </a>
                                                
                                                <!-- Dropdown for item details -->
                                                <div id="itemDetails_<?php echo $row['tracking']; ?>" class="item-details-dropdown" style="display:none;">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-header bg-dark text-white py-2">
                                                            <small>Order Items</small>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <div class="list-group list-group-flush">
                                                                <?php foreach($items_data as $index => $item): ?>                                                                <div class="list-group-item py-2 px-3 <?php echo ($index % 2 == 0) ? 'bg-light' : 'bg-white'; ?>">
                                                                    <div class="d-flex justify-content-between">
                                                                        <div>
                                                                            <strong class="text-dark"><?php echo $item['item_number']; ?></strong>
                                                                            <small class="d-block text-dark"><?php echo $item['item_brand']; ?> - <?php echo $item['item_category']; ?></small>
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <span class="badge bg-secondary"><?php echo $item['sell_quantity']; ?> qty</span>
                                                                            <div class="small text-dark">Rs. <?php echo number_format($item['sell_price']); ?></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $row['buyer_name']; ?></td>
                                        <td>Rs. <?php echo number_format($row['total_price']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($row['order_date'])); ?></td>
                                        <td>
                                            <?php if($row['rider_name']): ?>
                                                <span class="badge bg-success"><?php echo $row['rider_name']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Not assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#assignRider<?php echo str_replace(' ', '_', $row['tracking']); ?>">
                                                <i class="fa fa-user-plus me-1"></i> Assign
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal for Rider Assignment -->
                                    <div class="modal fade" id="assignRider<?php echo str_replace(' ', '_', $row['tracking']); ?>" tabindex="-1" role="dialog" aria-labelledby="assignRiderModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-secondary">
                                                    <h5 class="modal-title text-light" id="assignRiderModalLabel">Assign Rider to Order #<?php echo $row['tracking']; ?></h5>
                                                    <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="" method="post">
                                                    <div class="modal-body bg-light p-4">
                                                        <div class="mb-3">
                                                            <h6 class="text-dark">Order Details</h6>
                                                            <p class="text-white">
                                                                <strong class="text-dark fw-bold">Products:</strong>
                                                                <?php echo $row['product_names']; ?>
                                                            </p>
                                                            <p class="text-white">
                                                                <strong class="text-dark fw-bold">Buyer:</strong>
                                                                <?php echo $row['buyer_name']; ?>
                                                            </p>
                                                            <p class="text-white">
                                                                <strong class="text-dark fw-bold">Total Price:</strong>
                                                                Rs. <?php echo $row['total_price']; ?>
                                                            </p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="rider_id" class="form-label text-dark">Select Rider</label>
                                                            <select class="form-select" id="rider_id" name="rider_id" required>
                                                                <option value="">Select a rider</option>
                                                                <?php
                                                                // Get all active riders
                                                                $riders_query = "SELECT rider_id, rider_name FROM rider WHERE rider_status = 'approved' ORDER BY rider_name";
                                                                $riders_result = mysqli_query($conn, $riders_query);
                                                                
                                                                while ($rider = mysqli_fetch_assoc($riders_result)) {
                                                                    $selected = ($row['fk_rider_id'] == $rider['rider_id']) ? 'selected' : '';
                                                                    echo "<option value='{$rider['rider_id']}' {$selected}>{$rider['rider_name']}</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="notes" class="form-label text-dark">Notes</label>
                                                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any special instructions for the rider"></textarea>
                                                        </div>
                                                        
                                                        <input type="hidden" name="tracking_id" value="<?php echo $row['tracking']; ?>">
                                                    </div>
                                                    <div class="modal-footer bg-secondary">
                                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="assign_rider" class="btn btn-outline-success">Assign Rider</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="alert alert-success">
                                            <strong>No orders waiting for rider assignment at this time.</strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>                </div>
            </div>
        </div>
    </div>
</div>

<?php
// including the footer
require_once("../../includes/footer.php");
?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#approved_orders').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "columnDefs": [
            { "orderable": false, "targets": 6 }
        ],
        "order": [[4, 'desc']], // Sort by date approved column desc
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ orders",
            "infoEmpty": "Showing 0 to 0 of 0 orders",
            "infoFiltered": "(filtered from _MAX_ total orders)"
        },
        "drawCallback": function() {
            // Initialize dropdowns after table is redrawn (for search results)
            initializeItemDropdowns();
        }
    });
    
    // Initialize item dropdowns
    function initializeItemDropdowns() {
        $('.item-details-badge').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const trackingId = $(this).data('tracking');
            const $dropdown = $('#itemDetails_' + trackingId);
            
            $('.item-details-dropdown').not($dropdown).slideUp(200);
            $dropdown.slideToggle(200);
        });
        
        // Close dropdowns when clicking elsewhere
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.item-details-badge, .item-details-dropdown').length) {
                $('.item-details-dropdown').slideUp(200);
            }
        });
    }
    
    // Initial initialization
    initializeItemDropdowns();
});
</script>
