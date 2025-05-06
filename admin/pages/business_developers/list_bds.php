<?php include_once('../../../includes/init.php'); ?>
<?php include_once('../../includes/nav-side.php'); ?>

<?php
// Get database connection properly initialized
$db = new DB();
$con = $db->conn;

// Calculate BD statistics
$bdStatsSql = "SELECT bd.bd_id, bd.bd_name, bd.bd_contact, bd.bd_email, bd.bd_status, bd.bd_referal_code,
                COUNT(DISTINCT b.buyer_id) as buyer_count,
                COUNT(DISTINCT iso.sell_id) as order_count,
                SUM(iso.sell_price * iso.sell_quantity) as total_order_value,
                SUM(ods.amount_collected) as total_collected
              FROM business_developer bd
              LEFT JOIN buyer b ON bd.bd_id = b.fk_bd_id
              LEFT JOIN items_sold iso ON b.buyer_id = iso.fk_buyer_id
              LEFT JOIN order_delivery_status ods ON iso.tracking = ods.tracking_id
              GROUP BY bd.bd_id, bd.bd_name, bd.bd_contact, bd.bd_email, bd.bd_status, bd.bd_referal_code
              ORDER BY total_order_value DESC";

$bdStatsResult = $con->query($bdStatsSql);
?>

<!-- Business Developer Summary Cards -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-users fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Business Developers</p>
                    <h6 class="mb-0">Management Dashboard</h6>
                </div>
                <a href="add_bd.php" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add New BD
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Business Developers Table -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Business Developers List</h6>
            <form class="d-flex" method="GET">
                <input class="form-control bg-dark border-0 me-2" type="search" name="search" placeholder="Search by name or code" 
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table text-start align-middle table-hover mb-0">
                <thead>
                    <tr class="text-white">
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Email</th>
                        <th scope="col">Referral Code</th>
                        <th scope="col">Buyers</th>
                        <th scope="col">Orders</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    if ($bdStatsResult && $bdStatsResult->num_rows > 0) {
                        while ($bd = $bdStatsResult->fetch_assoc()) {
                            // Apply search filter if provided
                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                $search = strtolower($_GET['search']);
                                if (strpos(strtolower($bd['bd_name']), $search) === false && 
                                    strpos(strtolower($bd['bd_referal_code']), $search) === false) {
                                    continue; // Skip this BD if it doesn't match the search
                                }
                            }
                            
                            // Determine status class
                            $statusClass = $bd['bd_status'] == 'active' ? 'bg-success' : 'bg-danger';
                    ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= htmlspecialchars($bd['bd_name']) ?></td>
                                <td><?= htmlspecialchars($bd['bd_contact']) ?></td>
                                <td><?= htmlspecialchars($bd['bd_email']) ?></td>
                                <td><span class="badge bg-info"><?= htmlspecialchars($bd['bd_referal_code']) ?></span></td>
                                <td><?= $bd['buyer_count'] ?></td>
                                <td><?= $bd['order_count'] ?></td>
                                <td><span class="badge <?= $statusClass ?>"><?= ucfirst($bd['bd_status']) ?></span></td>
                                <td>
                                    <a class="btn btn-sm btn-primary" href="bd_details.php?id=<?= $bd['bd_id'] ?>">
                                        <i class="fa fa-eye"></i> Details
                                    </a>
                                    <a class="btn btn-sm btn-info" href="edit_bd.php?id=<?= $bd['bd_id'] ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-<?= $bd['bd_status'] == 'active' ? 'danger' : 'success' ?> toggle-status-btn" 
                                            data-id="<?= $bd['bd_id'] ?>" 
                                            data-status="<?= $bd['bd_status'] ?>">
                                        <i class="fa fa-<?= $bd['bd_status'] == 'active' ? 'ban' : 'check' ?>"></i> 
                                        <?= $bd['bd_status'] == 'active' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="10" class="text-center">No business developers found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- BD Performance Chart -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">BD Performance Comparison</h6>
        </div>
        <canvas id="bdPerformanceChart"></canvas>
    </div>
</div>

<?php include_once('../../includes/footer.php'); ?>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle BD status
    const toggleButtons = document.querySelectorAll('.toggle-status-btn');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bdId = this.getAttribute('data-id');
            const currentStatus = this.getAttribute('data-status');
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            
            if (confirm(`Are you sure you want to ${currentStatus === 'active' ? 'deactivate' : 'activate'} this business developer?`)) {
                fetch('toggle_bd_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `bd_id=${bdId}&new_status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Business developer status updated successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('An error occurred while processing the request.');
                });
            }
        });
    });

    // BD Performance Chart
    <?php
    // Prepare data for chart
    $chartLabels = [];
    $chartDataAll = [];
    $chartDataCollected = [];
    $chartColors = [];
    $hasData = false;
    
    if ($bdStatsResult && $bdStatsResult->num_rows > 0) {
        // Reset the result pointer
        $bdStatsResult->data_seek(0);
        
        // Get top 5 BDs by sales
        $count = 0;
        while ($bd = $bdStatsResult->fetch_assoc()) {
            if ($count >= 5) break; // Limit to top 5
            
            $chartLabels[] = $bd['bd_name'];
            $chartDataAll[] = $bd['total_order_value'] ?: 0;
            $chartDataCollected[] = $bd['total_collected'] ?: 0;
            
            // Generate random color
            $chartColors[] = 'rgba(' . rand(100, 255) . ', ' . rand(100, 255) . ', ' . rand(100, 255) . ', 0.7)';
            
            // Check if we have any non-zero data
            if (($bd['total_order_value'] ?: 0) > 0 || ($bd['total_collected'] ?: 0) > 0) {
                $hasData = true;
            }
            
            $count++;
        }
    }
    
    // If no data or all zeros, add a placeholder
    if (empty($chartLabels) || !$hasData) {
        $chartLabels = ['No Data Available'];
        $chartDataAll = [0];
        $chartDataCollected = [0];
        $chartColors = ['rgba(200, 200, 200, 0.5)'];
    }
    ?>
    
    // Create the chart
    const ctx = document.getElementById('bdPerformanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [
                {
                    label: 'Total Order Value (PKR)',
                    data: <?= json_encode($chartDataAll) ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.7)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Amount Collected (PKR)',
                    data: <?= json_encode($chartDataCollected) ?>,
                    backgroundColor: 'rgba(28, 200, 138, 0.7)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#fff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#fff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#fff'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
