<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<?php
// payment to the wholseller
if (isset($_POST['paid_ok'])) {
    $paid = escape($_POST['paid']);
    $paid_rec = $paid;
    $ws_id = escape($_POST['ws_id']);

    $query = "SELECT * FROM ws_pending_payments WHERE NOT wspp_amount='0' AND fk_ws_id='$ws_id'";
    $pass = query($query);
    while ($row = mysqli_fetch_assoc($pass)) {
        if ($paid != '0' && $paid > $row['wspp_amount']) {
            $paid = (int) $paid - (int) $row['wspp_amount'];

            $wspp = new WsPendingPayments();
            $wspp->wspp_id = $row['wspp_id'];
            $wspp->wspp_amount = '0';
            $wspp->update();
        } 
        elseif ($paid != '0' && $paid <= $row['wspp_amount']) {
            $wspp = new WsPendingPayments();
            $wspp->wspp_id = (int) $row['wspp_id'];
            $wspp->wspp_amount = (int) $row['wspp_amount'] - (int) $paid;
            $wspp->update();

            $paid = 0;
        }
    }

    $target_dir = "../../../uploads/ws-profile/";
    $target_file = $target_dir . basename($_FILES["pic"]["name"]);
    $pic = basename($_FILES["pic"]["name"]);

    if (move_uploaded_file($_FILES["pic"]["tmp_name"], $target_file)) {
        $wsr = new WsPaymentRecords();
        $wsr->wsr_image = $pic;
        $wsr->wsr_paid = $paid_rec;
        $wsr->fk_ws_id = $ws_id;
        $wsr->date = date('Y-m-d', time());

        if ($wsr->insert()) {
            $last_id = $db->last_id();
            $rplc = "rec_pay" . $last_id;
            rename($target_dir . $pic . "", $target_dir . $rplc . $pic . "");

            $wsr = new WsPaymentRecords();
            $wsr->wsr_id = $last_id;
            $wsr->wsr_image = $rplc . $pic;

            if ($wsr->update()) {
                redirect("./wspp.php");
            }
        }
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Pending Payments</h3>

        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Suppliers</h4>
                <div class="table-responsive">
                    <span class="text-custom">Search:</span>
                    <table id="wspp" style="background-color: #e74c3c;" class="table table-light table-bordered table-hover">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col">Name</th>
                                <th scope="col">Company</th>
                                <th scope="col">Pending Payment</th>
                                <th scope="col">Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM wholesaler as ws INNER JOIN ws_pending_payments as wspp
                                    ON ws.ws_id=wspp.fk_ws_id WHERE NOT wspp_amount='0'";
                            $result = mysqli_query($db->conn, $query);

                            $arr = [];
                            $checks = [];
                            $wspp = 0;
                            while ($rows = mysqli_fetch_assoc($result)) {
                                if (!isset($checks[$rows['ws_id']])) {
                                    $checks[$rows['ws_id']] = $rows['ws_id'];
                                    $arr[$rows['ws_id']] = $rows;
                                } else {
                                    $arr[$rows['ws_id']]['wspp_amount'] += $rows['wspp_amount'];
                                }
                            }

                            if (mysqli_num_rows($result) > 0) {
                                foreach ($arr as $row) {
                            ?>
                                    <tr>
                                        <td><?php echo $row['ws_name']; ?></td>
                                        <td><?php echo $row['ws_company_name']; ?></td>
                                        <td>Rs. <?php echo $row['wspp_amount']; ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-square btn-outline-info m-2 pay-button"
                                                data-ws-id="<?php echo $row['ws_id']; ?>"
                                                data-ws-name="<?php echo htmlspecialchars($row['ws_name']); ?>"
                                                data-ws-company="<?php echo htmlspecialchars($row['ws_company_name']); ?>"
                                                data-ws-amount="<?php echo $row['wspp_amount']; ?>">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="row text-center">
                                            <div class="col-md-6 offset-md-3">
                                                <div class="alert alert-success"><strong>
                                                        No pending payments at this moment <?php echo $_SESSION['admin_name']; ?>, enjoy your day! &#x1F603;
                                                    </strong></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Single Modal for all payments -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title text-light" id="paymentModalLabel">Supplier Payment</h5>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body bg-light p-4">
                    <p class="text-white bg-primary mx-5 p-2 rounded">
                        <strong class="text-white fw-bold">Alert!</strong>
                        Upload reciept and enter correct paid amount!
                    </p>
                    <p class="text-white">
                        <strong class="text-dark fw-bold">Supplier:</strong>
                        <span id="modalWsName"></span>
                    </p>
                    <p class="text-white">
                        <strong class="text-dark fw-bold">Company:</strong>
                        <span id="modalWsCompany"></span>
                    </p>
                    <p class="text-white">
                        <strong class="text-dark fw-bold">Pending Payment:</strong>
                        Rs. <span id="modalWsAmount"></span>
                    </p>

                    <hr class="hr text-white">

                    <div class="row mb-3">
                        <label for="paid" class="col-md-5 col-lg-5 col-form-label text-dark">
                            <strong>Paid Amount <code>*</code></strong>
                        </label>
                        <div class="col-md-7 col-lg-7">
                            <div class="form-floating">
                                <input type="number" name="paid" min="1" class="form-control" placeholder="Enter amount" required>
                                <label for="paid">Enter amount</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="pic" class="col-md-12 col-lg-12 col-form-label text-dark">
                            <strong>Receipt Image <code>*</code></strong>
                        </label>
                        <div class="col-md-12 col-lg-12">
                            <div class="text-center">
                                <img id="imagePreview" src="https://fakeimg.pl/100x100?text=Receipt" alt="Image Preview" class="rounded mb-2" style="max-width: 200px;">
                            </div>
                            <div class="d-flex justify-content-center">
                                <input type="file" name="pic" class="form-control d-none" id="fileInput" onchange="previewImage(event)" required>
                                <button class="btn btn-sm btn-secondary" type="button" id="uploadButton">
                                    <i class="bi bi-upload"></i>
                                </button>&nbsp;
                                <button class="btn btn-sm btn-danger" type="button" id="deleteButton">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-secondary">
                    <input type="hidden" name="ws_id" id="modalWsId">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="paid_ok" class="btn btn-outline-success">Mark Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Image upload handling
    document.getElementById('uploadButton').addEventListener('click', function() {
        document.getElementById('fileInput').click();
    });
    
    document.getElementById('deleteButton').addEventListener('click', function() {
        document.getElementById('fileInput').value = '';
        document.getElementById('imagePreview').src = 'https://fakeimg.pl/100x100?text=Receipt';
    });

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('imagePreview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    // Modal handling
    document.addEventListener('DOMContentLoaded', function() {
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        
        document.querySelectorAll('.pay-button').forEach(button => {
            button.addEventListener('click', function() {
                const wsId = this.dataset.wsId;
                const wsName = this.dataset.wsName;
                const wsCompany = this.dataset.wsCompany;
                const wsAmount = this.dataset.wsAmount;

                // Populate modal with data
                document.getElementById('modalWsId').value = wsId;
                document.getElementById('modalWsName').textContent = wsName;
                document.getElementById('modalWsCompany').textContent = wsCompany;
                document.getElementById('modalWsAmount').textContent = wsAmount;

                // Reset form
                document.getElementById('fileInput').value = '';
                document.getElementById('imagePreview').src = 'https://fakeimg.pl/100x100?text=Receipt';

                // Show modal
                paymentModal.show();
            });
        });
    });
</script>

<?php // including the footer
require_once("../../includes/footer.php");
?>