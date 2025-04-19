<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>


<!-- OpenStreetMap -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />


<?php
// checking if the approve post request is set
if (!isset($_GET['buyer_id'])) {
    redirect("./shopkeeper-approvals.php");
}
?>
<?php

// approve the buyer
if (isset($_POST['added'])) {
    $buyer = new Buyer();
    $buyer->buyer_id = $_GET['buyer_id'];
    $buyer->buyer_status = 'approved';

    if ($buyer->update()) {
        redirect("./shopkeeper-approvals.php");
    }
}
?>

<?php
// select buyer data from the database
$buyer_id = $_GET['buyer_id'];
$query = "SELECT * FROM buyer WHERE buyer_id='$buyer_id'";
$result = query($query);
$row = mysqli_fetch_assoc($result);

?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Approve Shopkeeper</h3>

        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4">Shopkeeper Details</h4>
                <div class="testimonial-item text-center">
                    <img id="thumbnail" class="img-fluid rounded mx-auto mb-4"
                        src="../../../uploads/buyer-profile/<?php
                                                            if (empty($row['buyer_image'])) {
                                                                echo 'placeholder-pic.jpg';
                                                            } else {
                                                                echo $row['buyer_image'];
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
                    <p><strong class="text-white">Name:</strong> <?php echo $row['buyer_name']; ?></p>
                    <p><strong class="text-white">Address:</strong> <?php echo $row['buyer_address']; ?></p>
                    <p><strong class="text-white">Contact#</strong> <?php echo $row['buyer_contact']; ?></p>
                    <p><strong class="text-white">CNIC:</strong> <?php echo $row['buyer_cnic']; ?></p>
                    <p><strong class="text-white">Email:</strong> <?php echo $row['buyer_email']; ?></p>
                    <hr>
                    <div class="col-sm-12 col-xl-12">
                        <div class="bg-secondary rounded h-100 p-0">
                            <!-- <h6 class="mb-4">Horizontal Form</h6> -->
                            <form action="" method="post">
                                <div class="d-flex justify-content-end">
                                    <a href="./shopkeeper-approvals.php" class="btn btn-sm btn-outline-danger me-2">Go Back</a>
                                    <button type="submit" name="added" id="" class="btn btn-sm btn-success">Approve</button>
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
                <h4 class="mb-4">Shopkeeper's Shop</h4>
                <div class="testimonial-item text-center">
                    <img id="thumbnail1" class="img-fluid rounded mx-auto mb-4"
                        src="../../../uploads/buyer-profile/<?php
                                                            if (empty($row['shop_img'])) {
                                                                echo 'placeholder-pic.jpg';
                                                            } else {
                                                                echo $row['shop_img'];
                                                            }
                                                            ?>" style="cursor: pointer; height: 150px; width: 150px;" onclick="showImage1()">
                </div>
                <div class="testimonial-item text-start">
                </div>
                <hr>
                <div class="col-sm-12 col-xl-12">
                    <div class="bg-secondary rounded h-100 p-0" onload="initMap()">
                        <?php $location = $row['location']; ?>
                        <?php if ($location != '') { ?>
                            <div id="map" style="height: 400px; width: 100%;"></div>
                        <?php } else { ?>
                            <p><strong class="text-primary">Location:</strong> Sorry! There was no location specified.</p>
                        <?php } ?>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Google maps, need API -->
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=OUR_API_KEY&callback=initMap" async defer></script> -->

<!-- OpenStreetMap -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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

    function showImage1() {
        var imageSrc = document.getElementById("thumbnail1").src;
        document.getElementById("fullscreenImage").src = imageSrc;
        document.getElementById("fullscreenOverlay").style.display = "block";
    }

    // Google Maps
    // function initMap() {
    //     var locationString = "<?php echo $location; ?>";

    //     var coords = locationString.split(',');
    //     var lat = parseFloat(coords[0]);
    //     var lng = parseFloat(coords[1]);

    //     var location = {
    //         lat: lat,
    //         lng: lng
    //     };
    //     var map = new google.maps.Map(document.getElementById('map'), {
    //         zoom: 15,
    //         center: location
    //     });
    //     var marker = new google.maps.Marker({
    //         position: location,
    //         map: map
    //     });
    // }
    // initMap();

    // OpenStreetMap
    function initMap() {
        var locationString = "<?php echo $location; ?>";

        var coords = locationString.split(',');
        var lat = parseFloat(coords[0]);
        var lng = parseFloat(coords[1]);

        var map = L.map('map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup('Location')
            .openPopup();
    }

    document.addEventListener("DOMContentLoaded", function() {
        initMap();
    });



    // setInterval(periodicFunction, 2000);
</script>





<?php // including the footer
require_once("../../includes/footer.php");
?>