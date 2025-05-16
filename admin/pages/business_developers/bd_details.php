<?php include_once('../../../includes/init.php'); ?>
<?php include_once('../../includes/nav-side.php'); ?>
<?php
//show errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
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

// Get BD commission history
$commissionSql = "SELECT * FROM bd_monthly_commission 
                 WHERE fk_bd_id = '$bd_id' 
                 ORDER BY commission_month DESC";
$commissionResult = $con->query($commissionSql);

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

<!-- Commission Statistics -->
<div class="row g-4 mt-1 px-4">
    <div class="col-12">
        <div class="bg-secondary rounded p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Monthly Commissions</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover text-start align-middle">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Sales</th>
                            <th>Commission (1%)</th>
                            <th>Status</th>
                            <th>Payment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($commissionResult && $commissionResult->num_rows > 0): ?>
                            <?php while ($commission = $commissionResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('F Y', strtotime($commission['commission_month'])) ?></td>
                                    <td>PKR <?= number_format($commission['total_sales'], 2) ?></td>
                                    <td>PKR <?= number_format($commission['commission_amount'], 2) ?></td>
                                    <td>
                                        <span class="badge <?= $commission['is_paid'] ? 'bg-success' : 'bg-warning' ?>">
                                            <?= $commission['is_paid'] ? 'Paid' : 'Pending' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $commission['payment_date'] ?
                                            date('d M Y H:i', strtotime($commission['payment_date'])) :
                                            'Not paid' ?>
                                    </td>
                                    <td>
                                        <?php if (!$commission['is_paid'] && $commission['commission_amount'] > 0): ?>
                                            <button type="button"
                                                class="btn btn-sm btn-success process-commission"
                                                data-id="<?= $commission['commission_id'] ?>"
                                                data-amount="<?= $commission['commission_amount'] ?>"
                                                data-month="<?= date('F Y', strtotime($commission['commission_month'])) ?>"
                                                data-bd-name="<?= htmlspecialchars($bd['bd_name']) ?>">
                                                Process Payment
                                            </button>
                                        <?php elseif ($commission['is_paid']): ?>
                                            <span class="text-muted">Paid</span>
                                        <?php else: ?>
                                            <span class="text-muted">No Payment Required</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No commission records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
            <table id="linkedBuyersTable" class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-white">
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Address</th>
                        <th scope="col">Orders</th>
                        <th scope="col">Order Value</th>
                        <th scope="col">Amount Collected</th>
                        <th scope="col">Amount Due</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($buyersResult && $buyersResult->num_rows > 0) {
                        while ($buyer = $buyersResult->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><?= $buyer['buyer_id'] ?></td>
                                <td><?= htmlspecialchars($buyer['buyer_name']) ?></td>
                                <td><?= htmlspecialchars($buyer['buyer_contact']) ?></td>
                                <td><?= htmlspecialchars($buyer['buyer_address']) ?></td>
                                <td><?= $buyer['order_count'] ?></td>
                                <td data-order="<?= $buyer['total_order_value'] ?: 0 ?>">PKR <?= number_format($buyer['total_order_value'] ?: 0, 2) ?></td>
                                <td data-order="<?= $buyer['amount_collected'] ?: 0 ?>">PKR <?= number_format($buyer['amount_collected'] ?: 0, 2) ?></td>
                                <td data-order="<?= $buyer['amount_due'] ?: 0 ?>">PKR <?= number_format($buyer['amount_due'] ?: 0, 2) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary view-buyer-orders" data-id="<?= $buyer['buyer_id'] ?>">
                                        <i class="fa fa-eye"></i> Details
                                    </button>
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
            <table id="referralBonusTable" class="table text-start align-middle table-bordered table-hover mb-0">
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
                                <td data-order="<?= $bonus['bonus_amount'] ?>">PKR <?= number_format($bonus['bonus_amount'], 2) ?></td>
                                <td data-order="<?= strtotime($bonus['date_earned']) ?>"><?= date('d M Y H:i', strtotime($bonus['date_earned'])) ?></td>
                                <td data-order="<?= $bonus['is_paid'] ?>">
                                    <span class="badge <?= $bonus['is_paid'] ? 'bg-success' : 'bg-warning' ?>">
                                        <?= $bonus['is_paid'] ? 'Paid' : 'Pending' ?>
                                    </span>
                                </td>
                                <td data-order="<?= $bonus['payment_date'] ? strtotime($bonus['payment_date']) : 0 ?>">
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
                            <td colspan="8" class="text-center">No referral bonuses found</td>
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

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

<!-- Custom DataTables Styling -->
<style>
    div.dataTables_wrapper div.dataTables_length {
        text-align: left !important;
        float: left !important;
    }

    div.dataTables_wrapper div.dataTables_filter {
        text-align: right !important;
        float: right !important;
    }

    div.dataTables_wrapper div.dataTables_info {
        text-align: left !important;
        float: left !important;
    }

    div.dataTables_wrapper div.dataTables_paginate {
        text-align: right !important;
        float: right !important;
    }

    div.dataTables_wrapper div.dataTables_length label,
    div.dataTables_wrapper div.dataTables_filter label {
        margin-bottom: 0;
        white-space: nowrap;
        text-align: left;
    }

    div.dataTables_wrapper .row:after {
        content: "";
        display: table;
        clear: both;
    }

    /* Enhanced Pagination Styling */
    .dataTables_paginate .paginate_button {
        padding: 0.3em 0.8em !important;
        margin: 0 0.2em !important;
        border: 1px solid #6c757d !important;
        border-radius: 0.25rem !important;
        background-color: #2B2B2B !important;
        color: #fff !important;
    }

    .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
        background-color: #3d3d3d !important;
        color: #fff !important;
        border-color: #6c757d !important;
    }

    .dataTables_paginate .paginate_button.current {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #fff !important;
        font-weight: bold !important;
    }

    .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }
</style>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable for Linked Buyers
        $('#linkedBuyersTable').DataTable({
            "responsive": true,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "order": [
                [0, "desc"]
            ], // Sort by Buyer ID (descending) by default
            "columnDefs": [{
                    "orderable": false,
                    "targets": 8
                } // Disable sorting on Actions column
            ],
            "language": {
                "search": "Search buyers:",
                "lengthMenu": "Show _MENU_ buyers per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ buyers",
                "infoEmpty": "Showing 0 to 0 of 0 buyers",
                "infoFiltered": "(filtered from _MAX_ total buyers)",
                "zeroRecords": "No matching buyers found"
            },
            "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        });

        // Initialize DataTable for Referral Bonuses
        $('#referralBonusTable').DataTable({
            "responsive": true,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "order": [
                [4, "desc"]
            ], // Sort by Date Earned (descending) by default
            "columnDefs": [{
                    "orderable": false,
                    "targets": 7
                } // Disable sorting on Actions column
            ],
            "language": {
                "search": "Search bonuses:",
                "lengthMenu": "Show _MENU_ bonuses per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ bonuses",
                "infoEmpty": "Showing 0 to 0 of 0 bonuses",
                "infoFiltered": "(filtered from _MAX_ total bonuses)",
                "zeroRecords": "No matching bonuses found"
            },
            "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        });
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
        $(document).on('click', '.pay-bonus-btn', function() {
            if (confirm('Are you sure you want to mark this referral bonus as paid?')) {
                const bonusId = $(this).data('id');
                const bdId = $(this).data('bd-id');
                const button = this;
                const $row = $(button).closest('tr');

                // Disable the button and show loading state
                $(button).prop('disabled', true);
                $(button).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

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

                            // Get the current date for display
                            const currentDate = new Date();
                            const formattedDate = currentDate.toLocaleString('en-US', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            // Update the status cell
                            $row.find('td:eq(5)').html('<span class="badge bg-success">Paid</span>');
                            $row.find('td:eq(5)').attr('data-order', '1');

                            // Update the payment date cell
                            $row.find('td:eq(6)').html(formattedDate);
                            $row.find('td:eq(6)').attr('data-order', Math.floor(currentDate.getTime() / 1000));

                            // Update the action cell
                            $row.find('td:eq(7)').html('<span class="text-muted">Paid</span>');

                            // Just update the cells directly without reloading
                            // No need to reload the entire table

                            // Update the statistics
                            const totalBonusAmount = parseFloat($('.total-bonus-amount').text().replace('PKR ', '').replace(/,/g, ''));
                            const paidBonusAmount = parseFloat($('.paid-bonus-amount').text().replace('PKR ', '').replace(/,/g, ''));
                            const unpaidBonusAmount = parseFloat($('.unpaid-bonus-amount').text().replace('PKR ', '').replace(/,/g, ''));
                            const bonusAmount = parseFloat($row.find('td:eq(3)').text().replace('PKR ', '').replace(/,/g, ''));

                            $('.paid-bonus-amount').text('PKR ' + (paidBonusAmount + bonusAmount).toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                            $('.unpaid-bonus-amount').text('PKR ' + (unpaidBonusAmount - bonusAmount).toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                        } else {
                            // Show error message
                            alert('Error: ' + data.message);
                            $(button).prop('disabled', false);
                            $(button).html('<i class="fa fa-money-bill"></i> Pay');
                        }
                    })
                    .catch(error => {
                        alert('Error processing payment: ' + error);
                        $(button).prop('disabled', false);
                        $(button).html('<i class="fa fa-money-bill"></i> Pay');
                    });
            }
        });

        // Commission Processing
        $('.calculate-commission').on('click', function() {
            const bdId = $(this).data('bd-id');
            if (confirm('Are you sure you want to calculate commission for this BD?')) {
                $.ajax({
                    url: 'calculate_bd_commission.php',
                    method: 'POST',
                    data: {
                        bd_id: bdId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Commission calculated successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while calculating commission.');
                    }
                });
            }
        });

        $('.process-commission').on('click', function() {
            const commissionId = $(this).data('id');
            const amount = $(this).data('amount');
            const month = $(this).data('month');
            const bdName = $(this).data('bd-name');

            if (confirm(`Are you sure you want to process payment of PKR ${amount} to ${bdName} for ${month}?`)) {
                const button = $(this);
                button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

                $.ajax({
                    url: 'process_bd_commission.php',
                    method: 'POST',
                    data: {
                        commission_id: commissionId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                            button.prop('disabled', false).html('Process Payment');
                        }
                    },
                    error: function() {
                        alert('An error occurred while processing the payment.');
                        button.prop('disabled', false).html('Process Payment');
                    }
                });
            }
        });
    });
</script>