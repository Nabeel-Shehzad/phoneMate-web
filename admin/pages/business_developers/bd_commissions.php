<?php
include_once('../../../includes/init.php');
include_once('../../includes/nav-side.php');

$db = new DB();
$con = $db->conn;

// Get all commission records with BD details
$commissionsQuery = "SELECT 
    bmc.*,
    bd.bd_name,
    bd.bd_contact,
    bd.bd_email
FROM bd_monthly_commission bmc
JOIN business_developer bd ON bmc.fk_bd_id = bd.bd_id
ORDER BY bmc.commission_month DESC, bd.bd_name ASC";

$commissionsResult = $con->query($commissionsQuery);
?>

<!-- Commission Records Table -->
<div class="container-fluid pt-4 px-4">
  <div class="bg-secondary rounded p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h5 class="mb-0">BD Monthly Commissions</h5>
      <button class="btn btn-primary" onclick="calculateCommissions()">
        Calculate This Month
      </button>
    </div>

    <div class="table-responsive">
      <table id="commissionsTable" class="table text-start align-middle table-bordered table-hover mb-0">
        <thead>
          <tr class="text-white">
            <th>BD Name</th>
            <th>Month</th>
            <th>Total Sales</th>
            <th>Commission (1%)</th>
            <th>Status</th>
            <th>Payment Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($commission = $commissionsResult->fetch_assoc()): ?>
            <tr>
              <td>
                <?= htmlspecialchars($commission['bd_name']) ?>
                <br>
                <small class="text-muted">
                  <?= htmlspecialchars($commission['bd_contact']) ?>
                </small>
              </td>
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
                <?php if (!$commission['is_paid']): ?>
                  <button type="button"
                    class="btn btn-sm btn-success process-payment"
                    data-id="<?= $commission['commission_id'] ?>"
                    data-amount="<?= $commission['commission_amount'] ?>"
                    data-month="<?= date('F Y', strtotime($commission['commission_month'])) ?>"
                    data-bd-name="<?= htmlspecialchars($commission['bd_name']) ?>">
                    Process Payment
                  </button>
                <?php else: ?>
                  <span class="text-muted">Paid</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include_once('../../includes/footer.php'); ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#commissionsTable').DataTable({
      "order": [
        [1, "desc"]
      ], // Sort by month descending
      "pageLength": 25
    });

    // Process Payment Button Click
    $('.process-payment').on('click', function() {
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

  function calculateCommissions() {
    if (confirm('Are you sure you want to calculate commissions for the current month?')) {
      $.ajax({
        url: 'calculate_bd_commission.php',
        method: 'GET',
        success: function(response) {
          alert('Commissions calculated successfully!');
          location.reload();
        },
        error: function() {
          alert('An error occurred while calculating commissions.');
        }
      });
    }
  }
</script>