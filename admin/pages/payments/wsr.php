<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Paid Records</h3>

        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Suppliers</h4>
                <div class="table-responsive">
                    <span class="text-custom">Search:</span>
                    <table id="wsr" style="background-color: #e74c3c;" class="table table-light table-bordered table-hover">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col">Receipt</th>
                                <th scope="col">Name</th>
                                <th scope="col">Company</th>
                                <th scope="col">Paid Amount</th>
                                <th scope="col">Date</th>
                                <th scope="col">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // selecting all the values from the database which do not have a agreement date
                            $query = "SELECT * FROM wholesaler as ws INNER JOIN ws_payment_records as wsr
                                    ON ws.ws_id=wsr.fk_ws_id";
                            $result = mysqli_query($db->conn, $query);

                            $arr = [];
                            $checks = [];
                            $wspp = 0;
                            while ($rows = mysqli_fetch_assoc($result)) {
                                $arr[] = $rows;
                            }


                            if (mysqli_num_rows($result) > 0) {
                                foreach ($arr as $row) {
                            ?>
                                    <tr>
                                        <td>
                                            <a href="#">
                                                <img class="rounded-circle" src="../../../uploads/ws-profile/<?php echo $row['wsr_image']; ?>" width="60px" height="60px" alt="err...">
                                            </a>
                                        </td>
                                        <td><?php echo $row['ws_name']; ?></td>
                                        <td><?php echo $row['ws_company_name']; ?></td>
                                        <td>Rs. <?php echo $row['wsr_paid']; ?></td>
                                        <td><?php echo $row['date']; ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-square btn-outline-info m-2"
                                                data-bs-toggle="modal" data-bs-target="#paidRec<?php echo $row['wsr_id']; ?>">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- dynamic modal -->
                                    <div class="modal fade" id="paidRec<?php echo $row['wsr_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-secondary">
                                                    <h5 class="modal-title text-light" id="exampleModalLabel">Payment Record</h5>
                                                    <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body bg-light p-4">
                                                    <p class="text-white text-center">
                                                        <!-- <strong class="text-dark fw-bold text-start">Receipt:</strong> -->
                                                        <img class="rounded" src="../../../uploads/ws-profile/<?php echo $row['wsr_image']; ?>" width="200px" height="200px" alt="err...">
                                                    </p>
                                                    <hr class="hr text-white">
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Supplier:</strong>
                                                        <?php echo $row['ws_name']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Company:</strong>
                                                        <?php echo $row['ws_company_name']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Paid Amount:</strong>
                                                        Rs. <?php echo $row['wsr_paid']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Payment Date:</strong>
                                                        <?php echo $row['date']; ?>
                                                    </p>

                                                </div>
                                                <div class="modal-footer bg-secondary">
                                                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                <?php
                                    // end of the loop to fetch data
                                }
                                // end of if statement if the returned result is > 0
                            } else {
                                // show the message if no records
                                ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="row text-center">
                                            <div class="col-md-6 offset-md-3">
                                                <div class="alert alert-success"><strong>
                                                        No payment records at this moment <?php echo $_SESSION['admin_name']; ?>, enjoy your day! &#x1F603;
                                                    </strong></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                // end of else to show empty table message
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    // code to upload and view the image
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
</script>


<?php // including the footer
require_once("../../includes/footer.php");
?>