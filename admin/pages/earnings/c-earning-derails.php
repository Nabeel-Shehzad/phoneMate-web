<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Company Earnings Details</h4>
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
                            <h5 class="text-white mb-4">Earnings Information</h5>
                            <div class="info-grid">
                                <div class="info-item">
                                    <p><strong class="text-white">Total Earnings:</strong> Rs. <?php echo number_format($row['total_earnings'], 2); ?></p>
                                </div>
                                <div class="info-item">
                                    <p><strong class="text-white">Earnings Date:</strong> <?php echo date('F j, Y', strtotime($row['earnings_date'])); ?></p>
                                </div>
                                <div class="info-item">
                                    <p><strong class="text-white">Status:</strong> <?php echo ucfirst($row['status']); ?></p>
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