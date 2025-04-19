<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<?php
// getting the item id from the url
$item_id = isset($_GET['id']) ? $db->escape($_GET['id']) : 0;

// selecting all the values from the database which are not processed
$query = "SELECT * FROM items INNER JOIN item_tracking ON items.fk_item_tracking_id=item_tracking.item_tracking_id ";
$query .= "Where item_id='$item_id'";
$result = mysqli_query($db->conn, $query);
$row = mysqli_fetch_assoc($result);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Item Details</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="bg-dark rounded p-4">
                            <h5 class="text-white mb-4">Item Information</h5>
                            <div class="d-flex justify-content-center mb-4">
                                <div class="image-container" style="position: relative; width: 300px; height: 300px; overflow: hidden; border-radius: 15px; box-shadow: 0 8px 16px rgba(0,0,0,0.3);">
                                    <img class="product-image" src="../../../uploads/item-images/<?php echo $row['item_image']; ?>" alt="err..." style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.3s;">
                                    <div class="image-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;">
                                        <button class="btn btn-light btn-lg" onclick="showFullImage('../../../uploads/item-images/<?php echo $row['item_image']; ?>')">
                                            <i class="fa fa-search-plus me-2"></i> View Full Image
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mb-4">
                                <button class="btn btn-light btn-sm" onclick="document.getElementById('imageUploadForm').style.display = 'block'">
                                    <i class="fa fa-camera me-2"></i> Change Image
                                </button>
                            </div>
                            <div id="imageUploadForm" style="display: none;" class="text-center mb-4">
                                <form action="" method="post" enctype="multipart/form-data" class="d-inline-block">
                                    <div class="mb-3">
                                        <input type="file" name="new_image" class="form-control" accept="image/*" required>
                                    </div>
                                    <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                    <input type="hidden" name="current_image" value="<?php echo $row['item_image']; ?>">
                                    <button type="submit" name="change_image" class="btn btn-success me-2">
                                        <i class="fa fa-check me-2"></i> Confirm
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="document.getElementById('imageUploadForm').style.display = 'none'">
                                        <i class="fa fa-times me-2"></i> Cancel
                                    </button>
                                </form>
                            </div>
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
                                    <p><strong class="text-white">Quantity:</strong> <?php echo $row['item_quantity']; ?></p>
                                </div>
                                <div class="info-item">
                                    <p><strong class="text-white">Buying Price:</strong> Rs. <?php 
                                        $buying_price = $row['item_price'] + ($row['item_price'] * ($row['item_profit'] / 100));
                                        echo number_format($buying_price, 2);
                                    ?></p>
                                </div>
                                <div class="info-item">
                                    <p><strong class="text-white">Price per piece:</strong> Rs. <?php echo $row['item_price']; ?></p>
                                </div>
                                <div class="info-item">
                                    <p><strong class="text-white">Profit Percentage:</strong> <?php echo $row['item_profit']; ?>%</p>
                                </div>
                                <div class="info-item">
                                    <p><strong class="text-white">Agreement Date:</strong> <?php echo date('F j, Y', strtotime($row['agreement_date'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-dark rounded p-4">
                            <h5 class="text-white mb-4">Make Adjustment</h5>
                            <form action="" method="post">
                                <div class="form-floating mb-3">
                                    <input type="number" name="adj_price" min="1" class="form-control" id="floatingInput" placeholder="134" required>
                                    <label for="adj_price">Selling Price <code>*</code></label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="number" name="ppu" min="1" class="form-control" id="floatingPassword" placeholder="6" required>
                                    <label for="ppu">Pieces per unit <code>*</code></label>
                                </div>
                                <input type="hidden" name="fk_item_id" value="<?php echo $row['item_id']; ?>">
                                <button type="submit" name="adjusted" class="btn btn-outline-success w-100 btn-lg">Confirm Adjustment</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full Image Modal -->
<div id="fullscreenOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.98); z-index: 9999; align-items: center; justify-content: center;">
    <img id="fullscreenImage" src="" alt="Full Image" style="max-width: 90%; max-height: 90vh; object-fit: contain;">
    <button id="closeButton" class="btn btn-outline-danger" style="position: absolute; top: 20px; right: 20px; z-index: 10000; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;" onclick="closeImage()">
        <i class="fa fa-times"></i>
    </button>
</div>



<script>
function showFullImage(src) {
    const overlay = document.getElementById('fullscreenOverlay');
    const image = document.getElementById('fullscreenImage');
    
    // Set the image source directly
    image.src = src;
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

<?php
// submitting the form
if (isset($_POST['adjusted'])) {
    $item_adj = new ItemAdjustment();
    $item_adj->item_adj_price = $db->escape($_POST['adj_price']);
    $item_adj->pieces_pu = $db->escape($_POST['ppu']);
    $item_adj->fk_item_id = $db->escape($_POST['fk_item_id']);

    if ($item_adj->insert()) {
        $item = new Item();
        $item->item_id = $db->escape($_POST['fk_item_id']);
        $item->item_status = 'processed';

        if ($item->update()) {
            redirect("./listing-adjustment.php");
        }
    }
}

// Handle image change
if (isset($_POST['change_image'])) {
    $item_id = $db->escape($_POST['item_id']);
    $current_image = $db->escape($_POST['current_image']);
    
    // Handle file upload
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == 0) {
        $file = $_FILES['new_image'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        
        // Get file extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed file types
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($file_ext, $allowed)) {
            if ($file_error === 0) {
                if ($file_size <= 5242880) { // 5MB max
                    // Generate unique filename
                    $file_new_name = uniqid('item_') . '.' . $file_ext;
                    
                    // Set file destination
                    $file_destination = '../../../uploads/item-images/' . $file_new_name;
                    
                    // Move uploaded file
                    if (move_uploaded_file($file_tmp, $file_destination)) {
                        // Update database
                        $query = "UPDATE items SET item_image = '$file_new_name' WHERE item_id = '$item_id'";
                        if (mysqli_query($db->conn, $query)) {
                            // Delete old image
                            $old_image_path = '../../../uploads/item-images/' . $current_image;
                            if (file_exists($old_image_path)) {
                                unlink($old_image_path);
                            }
                            // Refresh the page to show new image
                            redirect("./adjust-item-details.php?id=" . $item_id);
                        }
                    }
                }
            }
        }
    }
}
?>

<?php // including the footer
require_once("../../includes/footer.php");
?> 