<?php // requiring the header of page
require_once("includes/header.php");
?>

<?php
$ws_id = escape($_SESSION['ws_id']);

// getting the wholesalaer information
$query = "SELECT * FROM wholesaler WHERE ws_id='$ws_id'";
$result = query($query);
$row = mysqli_fetch_assoc($result);

// getting wholesaler listed items
$query = "SELECT * FROM items INNER JOIN item_tracking ON
        items.fk_item_tracking_id=item_tracking.item_tracking_id 
        WHERE fk_ws_id='$ws_id'";
$result = query($query);
$items_count = mysqli_num_rows($result);

// all the sell records
$query = "SELECT * FROM items_sold INNER JOIN 
        items ON items_sold.fk_item_id=items.item_id 
        INNER JOIN item_tracking ON
        items.fk_item_tracking_id=item_tracking.item_tracking_id 
        WHERE fk_ws_id='$ws_id' AND item_status='processed' AND
        sell_status='completed'";
$result = query($query);
$sell_orders = mysqli_num_rows($result);

// rejected items
$query = "SELECT * FROM items INNER JOIN item_tracking ON
        items.fk_item_tracking_id=item_tracking.item_tracking_id 
        WHERE fk_ws_id='$ws_id' AND item_status='rejected'";
$result = query($query);
$rejected_items = mysqli_num_rows($result);
?>

<!-- Section wholesaler profile -->
<section class="page-top-margin">
    <div class="prf-main-container">
        <div class="prf-background"></div>
        <div class="prf-card-container mx-0 mx-sm-5">
            <div class="prf-icon-btn">
                <span class="btn btn-outline-light"><i class="fa fa-user"></i> Profile</span>
                <span><i class="fa fa-shopping-cart"></i> Supplier</span>
            </div>
            <img src="./uploads/ws-profile/<?php echo $row['ws_image']; ?>" alt="Profile" class="prf-profile-img">
            <div class="prf-card">
                <h4><?php echo $row['ws_name']; ?></h4>
                <p class="text-muted"><?php echo $row['ws_home_address']; ?></p>
                <p class="small"><?php echo $row['ws_company_name']; ?><br><?php echo $row['ws_office_address']; ?></p>
                <!-- <div></div> -->
                <p id="more" class="small d-none">
                    <?php echo $row['ws_cnic']; ?>
                    <strong class="fst-italic">
                        (Cnic)
                    </strong>
                    <br>
                    <?php echo $row['ws_personal_contact']; ?>
                    <strong class="fst-italic">
                        (Personal Contact)
                    </strong>
                    <br>
                    <?php echo $row['ws_office_contact']; ?>
                    <strong class="fst-italic">
                        (Office Contact)
                    </strong>
                    <br>
                    <?php echo $row['ws_email']; ?>
                    <strong class="fst-italic">
                        (E-mail)
                    </strong>
                    <br>
                </p>
                <div class="d-flex justify-content-around mt-4">
                    <div>
                        <h6><?php echo $items_count; ?></h6>
                        <p class="small text-muted">Listed Items</p>
                    </div>
                    <div>
                        <h6><?php echo $sell_orders; ?></h6>
                        <p class="small text-muted">Sold Orders</p>
                    </div>
                    <div>
                        <h6><?php echo $rejected_items; ?></h6>
                        <p class="small text-muted">Rejected Items</p>
                    </div>
                </div>
                <button id="more-btn" onclick="showMore()" class="prf-btn-custom mt-4">Show more</button>
                <button id="less-btn" onclick="showLess()" class="prf-btn-custom mt-4 d-none">Show less</button>
            </div>
        </div>
    </div>

</section>

<script>
    function showMore() {
        document.getElementById('more').classList.remove('d-none');
        document.getElementById('less-btn').classList.remove('d-none');
        document.getElementById('more-btn').classList.add('d-none');
    }
    function showLess() {
        document.getElementById('more').classList.add('d-none');
        document.getElementById('less-btn').classList.add('d-none');
        document.getElementById('more-btn').classList.remove('d-none');
    }
</script>

<?php // requiring the footer of page
require_once("includes/footer.php");
?>