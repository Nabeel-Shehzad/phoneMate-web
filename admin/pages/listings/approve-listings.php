<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<?php
// checking if the approve post request is set
if (!isset($_GET['item_id'])) {
    redirect("./");
}
?>
<?php
// add warning for the seller
// if (isset($_POST['warning'])) {
//     $ws_id = escape($_GET['ws_id']);
//     $item_id = escape($_GET['item_id']);
//     $reason = escape($_POST['w_reason']);

//     $query = "INSERT INTO ws_warnings(warning_reason, fk_item_id, fk_ws_id) ";
//     $query .= "VALUES('$reason', '$item_id', '$ws_id')";
//     $result = query($query);
//     echo "<script>
//             document.addEventListener('DOMContentLoaded', function() {
//                 // Clear form data to prevent resubmission on refresh
//                 document.getElementById('warn-form').reset();
//             });
//           </script>";
// }

// approve the item of the seller
if (isset($_POST['added'])) {
    $item = new Item();
    $item->item_id = escape($_GET['item_id']);
    $item->item_profit = escape($_POST['profit']);
    $item->agreement_date = escape($_POST['agreement']);
    $item->item_status = 'approved';

    if ($item->update()) {
        redirect("./");
    }
}
// reject the item of the seller
if (isset($_POST['rejected'])) {
    $item = new Item();
    $item->item_id = escape($_GET['item_id']);
    $item->item_status = 'rejected';

    if ($item->update()) {
        $item_rejection = new ItemRejection();
        $item_rejection->rejection_reason = escape($_POST['r_reason']);
        $item_rejection->fk_item_id = escape($_GET['item_id']);

        if ($item_rejection->insert()) {
            redirect("./");
        }
    }
}
?>

<?php
// select the item data from the database
$ws_id = $_GET['ws_id'];
$item_id = $_GET['item_id'];
$query = "SELECT * FROM items WHERE item_id='$item_id'";
$result = query($query);
$row = mysqli_fetch_assoc($result);

// select the wholesaler profile data from the database
$query = "SELECT * FROM wholesaler WHERE ws_id='$ws_id'";
$result = query($query);
$row1 = mysqli_fetch_assoc($result);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Approve Item</h3>

        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4">Item Details</h4>
                <div class="testimonial-item text-center">
                    <img id="thumbnail" class="img-fluid rounded mx-auto mb-4" src="../../../uploads/item-images/<?php echo $row['item_image']; ?>" style="cursor: pointer; height: 150px; width; 100%;" onclick="showImage()">
                </div>

                <!-- Fullscreen Image Container -->
                <div id="fullscreenOverlay" class="fullscreen-overlay">
                    <img id="fullscreenImage" src="" alt="Full Image">
                    <button id="closeButton" class="btn btn-outline-primary m-2" onclick="closeImage()"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
                <div class="testimonial-item text-start">
                    <!-- <h5 class="mb-1">Client Name</h5> -->
                    <p><strong class="text-white">Category:</strong> <?php echo $row['item_category']; ?></p>
                    <p><strong class="text-white">Brand:</strong> <?php echo $row['item_brand']; ?></p>
                    <p><strong class="text-white">Product Name:</strong> <?php echo $row['item_number']; ?></p>
                    <p><strong class="text-white">Quantity:</strong> <?php echo $row['item_quantity']; ?></p>
                    <p><strong class="text-white">Price per piece:</strong> Rs. <?php echo $row['item_price']; ?></p>
                    <hr>
                    <div class="col-sm-12 col-xl-12">
                        <div class="bg-secondary rounded h-100 p-0">
                            <!-- <h6 class="mb-4">Horizontal Form</h6> -->
                            <form action="" method="post">
                                <div class="row mb-3 ot" id="prf">
                                    <label for="profit" class="col-sm-6 col-form-label text-info">Promised profit %</label>
                                    <div class="col-sm-6">
                                        <input type="number" name="profit" class="form-control" id="profit" required>
                                    </div>
                                </div>
                                <div class="row mb-3 ot" id="dt">
                                    <label for="agreement" class="col-sm-6 col-form-label text-info">Agreement date</label>
                                    <div class="col-sm-6">
                                        <input type="date" name="agreement" class="form-control" id="agreement" required>
                                    </div>
                                </div>
                                <div class="row mb-3 rj" id="rj" style="display: none;">
                                    <label for="inputPassword3" class="col-sm-6 col-form-label text-danger">Rejection Reason</label>
                                    <div class="col-sm-6">
                                        <textarea name="r_reason" class="form-control" placeholder="Add reason"
                                            id="r_reason" style="height: 70px;"></textarea>
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input onclick="call()" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                    <label class="form-check-label text-danger" for="flexCheckDefault">
                                        Reject Item
                                    </label>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <input type="hidden" name="ws_id" value="<?php echo $ws_id; ?>">
                                    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                    <input type="hidden" name="approve" value="true">
                                    <button type="submit" name="added" id="ap-bt" class="btn btn-sm btn-success">Approve</button>
                                    <button type="submit" name="rejected" id="rj-bt" style="display: none;" class="btn btn-sm btn-danger">Reject</button>
                                </div>
                                <!-- <div class="d-flex justify-content-end" style="display: none!important;"></div> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4">Seller Details</h4>
                <div class="testimonial-item text-center">
                    <img id="thumbnail1" class="img-fluid rounded-circle mx-auto mb-4" src="../../../uploads/ws-profile/<?php echo $row1['ws_image']; ?>" style="cursor: pointer; height: 150px; width: 150px;" onclick="showImage1()">
                </div>
                <div class="testimonial-item text-start">
                    <!-- <h5 class="mb-1">Client Name</h5> -->
                    <p><strong class="text-white">Name:</strong> <?php echo $row1['ws_name']; ?></p>
                    <p><strong class="text-white">Email:</strong> <?php echo $row1['ws_email']; ?></p>
                    <p><strong class="text-white">Company Name:</strong> <?php echo $row1['ws_company_name']; ?></p>
                </div>
                <hr>
                <div class="col-sm-12 col-xl-12">
                    <div class="bg-secondary rounded h-100 p-0">
                        <!-- <h6 class="mb-4">Horizontal Form</h6> -->
                        <div id="warns" style="display: none;">
                            <?php
                            // warnings issued to this wholeslaer
                            $query = "SELECT * FROM ws_warnings WHERE fk_ws_id='$ws_id' ORDER BY fk_ws_id DESC LIMIT 3";
                            //  $query .= " INNER JOIN item_tracking ON ws_warnings.fk_ws_id=item_tracking.fk_ws_id INNER JOIN ";
                            $result = query($query);
                            if (mysqli_num_rows($result) != 0) {
                                while ($rows = mysqli_fetch_assoc($result)) {
                            ?>
                                    <div id="warning">
                                        <div class="alert alert-warning" role="alert">
                                            <?php echo $rows['warning_reason']; ?>
                                        </div>
                                    </div>
                                <?php
                                }
                            } else {
                                ?>
                                <div id="warning">
                                    <div class="alert alert-warning" role="alert">
                                        No warnings issued to this Seller!
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                        <!-- <div id="give-warns" style="display: none;">
                            <form id="warn-form" action="" method="post">
                                <div class="row mb-3 rj" id="rj">
                                    <label for="inputPassword3" class="col-sm-6 col-form-label text-danger">Warning Reason</label>
                                    <div class="col-sm-6">
                                        <textarea name="w_reason" class="form-control" placeholder="Add reason"
                                            id="floatingTextarea" style="height: 70px;" required></textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <input type="hidden" name="ws_id" value="<?php //echo $ws_id; 
                                                                                ?>">
                                    <input type="hidden" name="item_id" value="<?php //echo $item_id; 
                                                                                ?>">
                                    <input type="hidden" name="approve" value="true">
                                    <button type="submit" name="warning" class="btn btn-sm btn-danger">Submit</button>
                                </div>
                            </form>
                        </div> -->
                        <div class="form-check">
                            <input onclick="call1()" class="form-check-input" type="checkbox" value="" id="warn">
                            <label class="form-check-label text-danger" for="flexCheckDefault">
                                See Issued Warnings
                            </label>
                        </div>
                        <!-- <div class="form-check">
                            <input onclick="call2()" class="form-check-input" type="checkbox" value="" id="give-warn">
                            <label class="form-check-label text-danger" for="flexCheckDefault">
                                Give Warning
                            </label>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // code to see the rejection reason input field
    function call() {
        var one = document.getElementById('rj').style.display;

        if (one == "none") {
            // alert("this is me");
            document.getElementById('rj').style.display = "flex";
            document.getElementById('rj-bt').style.display = "inline-block";
            document.getElementById('r_reason').required = true;
            document.getElementById('prf').style.display = "none";
            document.getElementById('dt').style.display = "none";
            document.getElementById('ap-bt').style.display = "none";
            document.getElementById('profit').removeAttribute("required");
            document.getElementById('agreement').removeAttribute("required");
        } else {
            document.getElementById('rj').style.display = "none";
            document.getElementById('rj-bt').style.display = "none";
            document.getElementById('prf').style.display = "flex";
            document.getElementById('dt').style.display = "flex";
            document.getElementById('ap-bt').style.display = "inline-block";
            // document.getElementById('agreement').required = true;
            // document.getElementById('profit').required = true;
        }
    }
    // code to see the image in full screen
    function showImage() {
        var imageSrc = document.getElementById("thumbnail").src;
        document.getElementById("fullscreenImage").src = imageSrc;
        document.getElementById("fullscreenOverlay").style.display = "block";
    }

    function closeImage() {
        document.getElementById("fullscreenOverlay").style.display = "none";
    }

    function showImage1() {
        var imageSrc = document.getElementById("thumbnail1").src;
        document.getElementById("fullscreenImage").src = imageSrc;
        document.getElementById("fullscreenOverlay").style.display = "block";
    }

    // code to run if admin wants to see the warnings issued to the seller
    function call1() {
        var disp = document.getElementById("warns").style.display;
        if (disp == "none") {
            document.getElementById("warns").style.display = "block";
        } else {
            document.getElementById("warns").style.display = "none";
        }
    }
    // code to give warning to the user (not yet functional)
    function call2() {
        var disp = document.getElementById("give-warns").style.display;
        if (disp == "none") {
            document.getElementById("give-warns").style.display = "block";
        } else {
            document.getElementById("give-warns").style.display = "none";
        }
    }
    // setInterval(periodicFunction, 2000);
</script>



<?php // including the footer
require_once("../../includes/footer.php");
?>