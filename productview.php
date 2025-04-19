<?php
require_once("includes/header.php");

// Get item ID from URL
$item_id = isset($_GET['id']) ? escape($_GET['id']) : 0;

// Fetch item details
$query = "SELECT * FROM items i 
          INNER JOIN item_tracking it ON i.fk_item_tracking_id = it.item_tracking_id 
          INNER JOIN item_adjustment ia ON i.item_id = ia.fk_item_id 
          WHERE i.item_id = '$item_id'";

$result = query($query);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    redirect("items.php");
}
?>

<div class="container mt-4" style="padding-top: 70px;">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="items.php"><i class="fa fa-arrow-left me-1"></i>Back to Items</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $item['item_brand']; ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Product Image -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm product-image-card">
                <div class="card-body p-0">
                    <div class="image-container">
                        <img src="./uploads/item-images/<?php echo $item['item_image']; ?>" class="img-fluid rounded product-image" alt="<?php echo $item['item_brand']; ?>">
                        <div class="image-overlay">
                            <button class="btn btn-light btn-sm" onclick="showFullImage('./uploads/item-images/<?php echo $item['item_image']; ?>')">
                                <i class="fa fa-search-plus"></i> View Full Image
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm product-details-card">
                <div class="card-body">
                    <div class="product-header mb-4">
                        <h2 class="card-title"><?php echo $item['item_brand']; ?></h2>
                        <span class="badge"><?php echo $item['item_category']; ?></span>
                    </div>
                    
                    <div class="product-info">
                        <div class="info-group mb-4">
                            <h5 class="text-muted">Brand</h5>
                            <p class="h4"><?php echo $item['item_brand']; ?></p>
                        </div>

                        <div class="info-group mb-4">
                            <h5 class="text-muted">Product Name</h5>
                            <p class="h4"><?php echo $item['item_number']; ?></p>
                        </div>

                        <div class="info-group mb-4">
                            <h5 class="text-muted">Description</h5>
                            <p class="lead"><?php echo $item['item_description']; ?></p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="price-card">
                                    <h5 class="text-muted">Price per Piece</h5>
                                    <p class="h4 text-success">Rs. <?php echo number_format($item['item_price'], 2); ?></p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="profit-card">
                                    <h5 class="text-muted">Profit Percentage</h5>
                                    <p class="h4 text-success"><?php echo $item['item_profit']; ?>%</p>
                                </div>
                            </div>
                        </div>

                        <div class="info-group mb-4">
                            <h5 class="text-muted">Agreement Date</h5>
                            <p class="h4"><?php echo date('F j, Y', strtotime($item['agreement_date'])); ?></p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="quantity-card">
                                    <h5 class="text-muted">Available Quantity</h5>
                                    <p class="h4"><?php echo $item['item_quantity'] - $item['item_sold']; ?></p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="quantity-card">
                                    <h5 class="text-muted">Items Sold</h5>
                                    <p class="h4"><?php echo $item['item_sold']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full Image Modal -->
<div id="fullscreenOverlay" class="fullscreen-overlay">
    <img id="fullscreenImage" src="" alt="Full Image">
    <button id="closeButton" class="btn btn-outline-danger m-2" onclick="closeImage()">
        <i class="fa fa-times" aria-hidden="true"></i>
    </button>
</div>

<script>
function showFullImage(src) {
    document.getElementById('fullscreenImage').src = src;
    document.getElementById('fullscreenOverlay').style.display = 'flex';
}

function closeImage() {
    document.getElementById('fullscreenOverlay').style.display = 'none';
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

<?php
require_once("includes/footer.php");
?>
