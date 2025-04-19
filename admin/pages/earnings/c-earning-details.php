<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<?php
// checking if the approve post request is set
if (!isset($_GET['earning_id'])) {
    redirect("./");
}
?>

<?php
// select details from the database
$earning_id = $_GET['earning_id'];
$query = "SELECT * FROM company_earnings as ce INNER JOIN delivery as d ON
ce.fk_delivery_id=d.delivery_id INNER JOIN buyer as b ON d.fk_buyer_id=b.buyer_id 
INNER JOIN item_adjustment as ij ON d.fk_item_adj_id=ij.item_adj_id 
INNER JOIN items as i ON ij.fk_item_id=i.item_id 
INNER JOIN item_tracking as it ON i.fk_item_tracking_id=it.item_tracking_id 
INNER JOIN wholesaler as ws ON it.fk_ws_id=ws.ws_id 
WHERE earning_id='$earning_id'";


$result = query($query);
$row = mysqli_fetch_assoc($result);

// select the wholesaler profile data from the database
// $query = "SELECT * FROM wholesaler WHERE ws_id='$ws_id'";
// $result = query($query);
// $row1 = mysqli_fetch_assoc($result);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Company Earnings</h3>

        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4">Details</h4>
                <div class="d-flex justify-content-center mb-4">
                    <div class="image-container" style="position: relative; width: 300px; height: 300px; overflow: hidden; border-radius: 15px; box-shadow: 0 8px 16px rgba(0,0,0,0.3);">
                        <img class="product-image" src="../../../uploads/item-images/<?php echo $row['item_image']; ?>" alt="err..." style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.3s;">
                        <div class="image-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;">
                            <button class="btn btn-light btn-lg" onclick="showImage()">
                                <i class="fa fa-search-plus me-2"></i> View Full Image
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Fullscreen Image Container -->
                <div id="fullscreenOverlay" class="fullscreen-overlay">
                    <img id="fullscreenImage" src="" alt="Full Image">
                    <button id="closeButton" class="btn btn-outline-danger" style="position: absolute; top: 20px; right: 20px; z-index: 10000; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;" onclick="closeImage()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <?php
                $profit = ($row['item_profit'] / 100) * $row['item_price'];
                $buying_price = $profit + $row['item_price'];
                ?>
                <div class="info-grid">
                    <div class="info-item">
                        <p><strong class="text-white">Category:</strong> <?php echo $row['item_category']; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Brand:</strong> <?php echo $row['item_brand']; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Product Name:</strong> <?php echo $row['item_number']; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Listing price:</strong> Rs. <?php echo $row['item_price']; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Profit per piece:</strong> <?php echo $row['item_profit']; ?>% - Rs. <?php echo $profit; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Buying price:</strong> Rs. <?php echo $buying_price; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Selling price:</strong> Rs. <?php echo $row['item_adj_price']; ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="./c-earnings.php" class="btn btn-outline-danger">Go Back</a>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4">Payment Details</h4>
                <?php
                $ws_share = $buying_price * $row['delivery_quantity'];
                $c_share = $row['total_cash'] - $ws_share;
                ?>
                <div class="info-grid">
                    <div class="info-item">
                        <p><strong class="text-white">Buyer:</strong> <?php echo $row['ws_name']; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Seller:</strong> <?php echo $row['buyer_name']; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Quantity Sold:</strong> <?php echo $row['delivery_quantity']; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Cash Recieved:</strong> Rs. <?php echo $row['total_cash']; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Supplier Share:</strong> Rs. <?php echo $ws_share; ?></p>
                    </div>
                    <div class="info-item">
                        <p><strong class="text-white">Company Share:</strong> Rs. <?php echo $c_share; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
function showImage() {
    const overlay = document.getElementById('fullscreenOverlay');
    const image = document.getElementById('fullscreenImage');
    image.src = document.querySelector('.product-image').src;
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeImage() {
    const overlay = document.getElementById('fullscreenOverlay');
    overlay.style.display = 'none';
    document.body.style.overflow = '';
}

// Close overlay when clicking outside the image
document.getElementById('fullscreenOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImage();
    }
});

// Close overlay with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImage();
    }
});
</script>



<?php // including the footer
require_once("../../includes/footer.php");
?>