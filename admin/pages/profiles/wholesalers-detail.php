<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<?php
// checking if the approve post request is set
if (!isset($_GET['ws_id'])) {
    redirect("./");
}
?>

<?php
// select buyer data from the database
$ws_id = $_GET['ws_id'];
$query = "SELECT * FROM wholesaler WHERE ws_id='$ws_id'";
$result = query($query);
$row = mysqli_fetch_assoc($result);

?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Profile</h3>

        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4">Supplier Details</h4>
                <div class="testimonial-item text-center">
                    <img id="thumbnail" class="img-fluid rounded mx-auto mb-4"
                        src="../../../uploads/ws-profile/<?php
                                                            if (empty($row['ws_image'])) {
                                                                echo 'placeholder-pic.jpg';
                                                            } else {
                                                                echo $row['ws_image'];
                                                            }
                                                            ?>" style="cursor: pointer; height: 150px; width; 100%;" onclick="showImage()">
                </div>

                <!-- Fullscreen Image Container -->
                <div id="fullscreenOverlay" class="fullscreen-overlay">
                    <img id="fullscreenImage" src="" alt="Full Image">
                    <button id="closeButton" class="btn btn-outline-primary m-2" onclick="closeImage()"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
                <div class="testimonial-item text-start">
                    <!-- <h5 class="mb-1">Client Name</h5> -->
                    <p><strong class="text-white">Name:</strong> <?php echo $row['ws_name']; ?></p>
                    <p><strong class="text-white">Company:</strong> <?php echo $row['ws_company_name']; ?></p>
                    <p><strong class="text-white">CNIC:</strong> <?php echo $row['ws_cnic']; ?></p>
                    <p><strong class="text-white">Contact#</strong> <?php echo $row['ws_personal_contact']; ?></p>
                    <p><strong class="text-white">Office Contact#</strong> <?php echo $row['ws_office_contact']; ?></p>
                    <p><strong class="text-white">Email:</strong> <?php echo $row['ws_email']; ?></p>
                    <p><strong class="text-white">Address:</strong> <?php echo $row['ws_home_address']; ?></p>
                    <p><strong class="text-white">Office Address:</strong> <?php echo $row['ws_office_address']; ?></p>
                    <hr>
                    <div class="col-sm-12 col-xl-12">
                        <div class="bg-secondary rounded h-100 p-0">
                            <!-- <h6 class="mb-4">Horizontal Form</h6> -->
                            <!-- <form action="" method="post"> -->
                            <div class="d-flex justify-content-end">
                                <a href="./wholesalers.php" class="btn btn-sm btn-outline-danger me-2">Go Back</a>
                                <!-- <button type="submit" name="added" id="" class="btn btn-sm btn-success">Approve</button> -->
                            </div>
                            <!-- <div class="d-flex justify-content-end" style="display: none!important;"></div> -->
                            <!-- </form> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // code to see the image in full screen
    function showImage() {
        var imageSrc = document.getElementById("thumbnail").src;
        document.getElementById("fullscreenImage").src = imageSrc;
        document.getElementById("fullscreenOverlay").style.display = "block";
    }

    function closeImage() {
        document.getElementById("fullscreenOverlay").style.display = "none";
    }

    // setInterval(periodicFunction, 2000);
</script>



<?php // including the footer
require_once("../../includes/footer.php");
?>