<?php include_once('../../../includes/init.php'); ?>
<?php include_once('../../includes/nav-side.php'); ?>

<?php
// Get database connection properly initialized
$db = new DB();
$con = $db->conn;

// Check if BD ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<script>alert("Invalid request: Missing BD ID"); window.location.href = "list_bds.php";</script>';
    exit;
}

$bd_id = mysqli_real_escape_string($con, $_GET['id']);

// Get BD information
$bdSql = "SELECT * FROM business_developer WHERE bd_id = '$bd_id'";
$bdResult = $con->query($bdSql);

if (!$bdResult || $bdResult->num_rows == 0) {
    echo '<script>alert("Business Developer not found"); window.location.href = "list_bds.php";</script>';
    exit;
}

$bd = $bdResult->fetch_assoc();

// Get BD statistics
$statsSql = "SELECT 
                COUNT(DISTINCT b.buyer_id) as total_buyers,
                COUNT(DISTINCT iso.tracking) as total_orders,
                SUM(iso.sell_price * iso.sell_quantity) as total_order_value,
                SUM(ods.amount_collected) as total_collected,
                SUM(ods.amount_due) as total_due,
                (SELECT COUNT(*) FROM bd_referral_bonus WHERE fk_bd_id = '$bd_id') as total_referrals,
                (SELECT SUM(bonus_amount) FROM bd_referral_bonus WHERE fk_bd_id = '$bd_id') as total_bonus_amount,
                (SELECT SUM(bonus_amount) FROM bd_referral_bonus WHERE fk_bd_id = '$bd_id' AND is_paid = 1) as paid_bonus_amount,
                (SELECT SUM(bonus_amount) FROM bd_referral_bonus WHERE fk_bd_id = '$bd_id' AND is_paid = 0) as unpaid_bonus_amount
             FROM business_developer bd
             LEFT JOIN buyer b ON bd.bd_id = b.fk_bd_id
             LEFT JOIN items_sold iso ON b.buyer_id = iso.fk_buyer_id
             LEFT JOIN order_delivery_status ods ON iso.tracking = ods.tracking_id
             WHERE bd.bd_id = '$bd_id'";

$statsResult = $con->query($statsSql);
$stats = $statsResult->fetch_assoc();

// Get buyers linked to this BD
$buyersSql = "SELECT b.*,
                COUNT(DISTINCT iso.tracking) as order_count,
                SUM(iso.sell_price * iso.sell_quantity) as total_order_value,
                SUM(ods.amount_collected) as amount_collected,
                SUM(ods.amount_due) as amount_due
              FROM buyer b
              LEFT JOIN items_sold iso ON b.buyer_id = iso.fk_buyer_id
              LEFT JOIN order_delivery_status ods ON iso.tracking = ods.tracking_id
              WHERE b.fk_bd_id = '$bd_id'
              GROUP BY b.buyer_id
              ORDER BY total_order_value DESC";

$buyersResult = $con->query($buyersSql);

// Get monthly sales data for chart
$monthlySalesSql = "SELECT 
                      DATE_FORMAT(iso.sell_date, '%Y-%m') as month,
                      SUM(iso.sell_price * iso.sell_quantity) as monthly_sales
                    FROM buyer b
                    JOIN items_sold iso ON b.buyer_id = iso.fk_buyer_id
                    WHERE b.fk_bd_id = '$bd_id'
                    GROUP BY DATE_FORMAT(iso.sell_date, '%Y-%m')
                    ORDER BY month ASC
                    LIMIT 12";

$monthlySalesResult = $con->query($monthlySalesSql);
$monthLabels = [];
$salesData = [];

// Get referral bonus details
$referralBonusSql = "SELECT brb.*, b.buyer_name, b.buyer_contact
                     FROM bd_referral_bonus brb
                     JOIN buyer b ON brb.fk_buyer_id = b.buyer_id
                     WHERE brb.fk_bd_id = '$bd_id'
                     ORDER BY brb.date_earned DESC";

$referralBonusResult = $con->query($referralBonusSql);

if ($monthlySalesResult && $monthlySalesResult->num_rows > 0) {
    while ($row = $monthlySalesResult->fetch_assoc()) {
        $monthLabels[] = date('M Y', strtotime($row['month'] . '-01'));
        $salesData[] = $row['monthly_sales'];
    }
}
?>

<!-- BD Profile Header -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded p-4">
        <div class="row">
            <div class="col-md-2 text-center">
                <img src="../../../uploads/bd-images/<?= htmlspecialchars($bd['bd_image']) ?>"
                    alt="<?= htmlspecialchars($bd['bd_name']) ?>"
                    class="rounded-circle img-fluid" style="max-width: 150px; height: auto;">
            </div>
            <div class="col-md-6">
                <h4><?= htmlspecialchars($bd['bd_name']) ?></h4>
                <p><i class="fa fa-envelope me-2"></i> <?= htmlspecialchars($bd['bd_email']) ?></p>
                <p><i class="fa fa-phone me-2"></i> <?= htmlspecialchars($bd['bd_contact']) ?></p>
                <p><i class="fa fa-map-marker-alt me-2"></i> <?= htmlspecialchars($bd['bd_address']) ?></p>
                <p><i class="fa fa-id-card me-2"></i> <?= htmlspecialchars($bd['bd_cnic']) ?></p>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge <?= $bd['bd_status'] == 'active' ? 'bg-success' : 'bg-danger' ?> mb-2 p-2">
                    <?= ucfirst($bd['bd_status']) ?>
                </span>
                <h5 class="mt-2">Referral Code</h5>
                <div class="bg-dark p-2 rounded">
                    <h3 class="text-info"><?= htmlspecialchars($bd['bd_referal_code']) ?></h3>
                </div>
                <div class="mt-3">
                    <a href="edit_bd.php?id=<?= $bd_id ?>" class="btn btn-primary">
                        <i class="fa fa-edit"></i> Edit Profile
                    </a>
                    <a href="list_bds.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BD Statistics Cards -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-users fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Buyers</p>
                    <h6 class="mb-0"><?= $stats['total_buyers'] ?: 0 ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-shopping-cart fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Orders</p>
                    <h6 class="mb-0"><?= $stats['total_orders'] ?: 0 ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-money-bill fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Order Value (All)</p>
                    <h6 class="mb-0">PKR <?= number_format($stats['total_order_value'] ?: 0, 2) ?></h6>
                </div>
            </div>
        </div>

    </div>
    <div class="row g-4 mt-1">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-wallet fa-3x text-warning"></i>
                <div class="ms-3">
                    <p class="mb-2">Amount Collected</p>
                    <h6 class="mb-0">PKR <?= number_format($stats['total_collected'] ?: 0, 2) ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-exclamation-circle fa-3x text-danger"></i>
                <div class="ms-3">
                    <p class="mb-2">Amount Due</p>
                    <h6 class="mb-0">PKR <?= number_format($stats['total_due'] ?: 0, 2) ?></h6>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Referral Bonus Statistics -->
    <div class="row g-4 mt-1">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-users fa-3x text-info"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Referrals</p>
                    <h6 class="mb-0"><?= number_format($stats['total_referrals'] ?: 0) ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-gift fa-3x text-success"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Bonus Amount</p>
                    <h6 class="mb-0 total-bonus-amount">PKR <?= number_format($stats['total_bonus_amount'] ?: 0, 2) ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-check-circle fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Paid Bonus Amount</p>
                    <h6 class="mb-0 paid-bonus-amount">PKR <?= number_format($stats['paid_bonus_amount'] ?: 0, 2) ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-clock fa-3x text-warning"></i>
                <div class="ms-3">
                    <p class="mb-2">Pending Bonus </p>
                    <h6 class="mb-0 unpaid-bonus-amount">PKR <?= number_format($stats['unpaid_bonus_amount'] ?: 0, 2) ?></h6>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Buyers Table -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Linked Buyers</h6>
            <form class="d-flex" method="GET">
                <input type="hidden" name="id" value="<?= $bd_id ?>">
                <input class="form-control bg-dark border-0 me-2" type="search" name="search" placeholder="Search buyers"
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
                        <th scope="col">Status</th>
                        <th scope="col">Orders</th>
                        <th scope="col">Total Value</th>
                        <th scope="col">Collected</th>
                        <th scope="col">Due</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    if ($buyersResult && $buyersResult->num_rows > 0) {
                        while ($buyer = $buyersResult->fetch_assoc()) {
                            // Apply search filter if provided
                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                $search = strtolower($_GET['search']);
                                if (
                                    strpos(strtolower($buyer['buyer_name']), $search) === false &&
                                    strpos(strtolower($buyer['buyer_contact']), $search) === false
                                ) {
                                    continue; // Skip this buyer if it doesn't match the search
                                }
                            }

                            // Determine status class
                            $statusClass = $buyer['buyer_status'] == 'active' ? 'bg-success' : 'bg-danger';
                    ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= htmlspecialchars($buyer['buyer_name']) ?></td>
                                <td><?= htmlspecialchars($buyer['buyer_contact']) ?></td>
                                <td><span class="badge <?= $statusClass ?>"><?= ucfirst($buyer['buyer_status']) ?></span></td>
                                <td><?= $buyer['order_count'] ?: 0 ?></td>
                                <td>PKR <?= number_format($buyer['total_order_value'] ?: 0, 2) ?></td>
                                <td>PKR <?= number_format($buyer['amount_collected'] ?: 0, 2) ?></td>
                                <td>PKR <?= number_format($buyer['amount_due'] ?: 0, 2) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary view-buyer-orders" data-id="<?= $buyer['buyer_id'] ?>">
                                        <i class="fa fa-shopping-cart"></i> Orders
                                    </button>
                                    <a href="../buyers/buyer_details.php?id=<?= $buyer['buyer_id'] ?>" class="btn btn-sm btn-info">
                                        <i class="fa fa-eye"></i> Details
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="9" class="text-center">No buyers linked to this business developer</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Referral Bonuses Table -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Referral Bonuses</h6>
        </div>
        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-white">
                        <th scope="col">Bonus ID</th>
                        <th scope="col">Buyer Name</th>
                        <th scope="col">Buyer Contact</th>
                        <th scope="col">Bonus Amount</th>
                        <th scope="col">Date Earned</th>
                        <th scope="col">Status</th>
                        <th scope="col">Payment Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($referralBonusResult && $referralBonusResult->num_rows > 0) : ?>
                        <?php while ($bonus = $referralBonusResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?= $bonus['bonus_id'] ?></td>
                                <td><?= htmlspecialchars($bonus['buyer_name']) ?></td>
                                <td><?= htmlspecialchars($bonus['buyer_contact']) ?></td>
                                <td>PKR <?= number_format($bonus['bonus_amount'], 2) ?></td>
                                <td><?= date('d M Y H:i', strtotime($bonus['date_earned'])) ?></td>
                                <td>
                                    <span class="badge <?= $bonus['is_paid'] ? 'bg-success' : 'bg-warning' ?>">
                                        <?= $bonus['is_paid'] ? 'Paid' : 'Pending' ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $bonus['payment_date'] ? date('d M Y H:i', strtotime($bonus['payment_date'])) : 'N/A' ?>
                                </td>
                                <td>
                                    <?php if (!$bonus['is_paid']): ?>
                                        <button type="button" class="btn btn-sm btn-success pay-bonus-btn" data-id="<?= $bonus['bonus_id'] ?>" data-bd-id="<?= $bd_id ?>">
                                            <i class="fa fa-money-bill"></i> Pay
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">Paid</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center">No referral bonuses found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sales Chart -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Monthly Sales</h6>
        </div>
        <canvas id="salesChart"></canvas>
    </div>
</div>

<!-- Buyer Orders Modal -->
<div class="modal fade" id="buyerOrdersModal" tabindex="-1" aria-labelledby="buyerOrdersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h5 class="modal-title" id="buyerOrdersModalLabel">Buyer Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="buyerOrdersContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include_once('../../includes/footer.php'); ?>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($monthLabels) ?>,
                datasets: [{
                    label: 'Monthly Sales (PKR)',
                    data: <?= json_encode($salesData) ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.2)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    tension: 0.3
                }]
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
                    }
                }
            }
        });

        // Buyer Orders Modal
        const viewOrderButtons = document.querySelectorAll('.view-buyer-orders');
        viewOrderButtons.forEach(button => {
            button.addEventListener('click', function() {
                const buyerId = this.getAttribute('data-id');
                const modal = new bootstrap.Modal(document.getElementById('buyerOrdersModal'));

                // Clear previous content and show loading spinner
                document.getElementById('buyerOrdersContent').innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

                // Show the modal
                modal.show();

                // Fetch buyer orders via AJAX
                fetch('get_buyer_orders.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'buyer_id=' + buyerId
                    })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('buyerOrdersContent').innerHTML = data;
                    })
                    .catch(error => {
                        document.getElementById('buyerOrdersContent').innerHTML = '<div class="alert alert-danger">Error loading buyer orders: ' + error + '</div>';
                    });
            });
        });
        
        // Pay Referral Bonus
        const payBonusButtons = document.querySelectorAll('.pay-bonus-btn');
        payBonusButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to mark this referral bonus as paid?')) {
                    const bonusId = this.getAttribute('data-id');
                    const bdId = this.getAttribute('data-bd-id');
                    const button = this;
                    
                    // Disable the button and show loading state
                    button.disabled = true;
                    button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
                    
                    // Process payment via AJAX
                    fetch('pay_referral_bonus.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'bonus_id=' + bonusId + '&bd_id=' + bdId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                alert(data.message);
                                
                                // Update the UI
                                const row = button.closest('tr');
                                const statusCell = row.querySelector('td:nth-child(6)');
                                const dateCell = row.querySelector('td:nth-child(7)');
                                const actionCell = row.querySelector('td:nth-child(8)');
                                
                                statusCell.innerHTML = '<span class="badge bg-success">Paid</span>';
                                dateCell.innerHTML = new Date().toLocaleString();
                                actionCell.innerHTML = '<span class="text-muted">Paid</span>';
                                
                                // Update the statistics
                                const totalBonusAmount = parseFloat(document.querySelector('.total-bonus-amount').innerText.replace('PKR ', '').replace(/,/g, ''));
                                const paidBonusAmount = parseFloat(document.querySelector('.paid-bonus-amount').innerText.replace('PKR ', '').replace(/,/g, ''));
                                const unpaidBonusAmount = parseFloat(document.querySelector('.unpaid-bonus-amount').innerText.replace('PKR ', '').replace(/,/g, ''));
                                const bonusAmount = parseFloat(row.querySelector('td:nth-child(4)').innerText.replace('PKR ', '').replace(/,/g, ''));
                                
                                document.querySelector('.paid-bonus-amount').innerText = 'PKR ' + (paidBonusAmount + bonusAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                document.querySelector('.unpaid-bonus-amount').innerText = 'PKR ' + (unpaidBonusAmount - bonusAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            } else {
                                // Show error message
                                alert('Error: ' + data.message);
                                button.disabled = false;
                                button.innerHTML = '<i class="fa fa-money-bill"></i> Pay';
                            }
                        })
                        .catch(error => {
                            alert('Error processing payment: ' + error);
                            button.disabled = false;
                            button.innerHTML = '<i class="fa fa-money-bill"></i> Pay';
                        });
                }
            });
        });
    });
</script>