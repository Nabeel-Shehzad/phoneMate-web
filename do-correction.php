<?php // requiring the header of page
require_once("includes/header.php");
?>

<?php
// if id is not set
if (!isset($_GET['id'])) {
    redirect("./");
}
?>

<?php
// upading data for the entered item
if (isset($_POST['correct'])) {
    // fetching the form data
    $item = new Item();
    $item->item_id = escape($_GET['id']);
    $item->item_category = escape($_POST['item_category']);
    $item->item_brand = escape($_POST['item_brand']);
    $item->item_price = escape($_POST['item_price']);
    $item->item_quantity = escape($_POST['item_quantity']);
    $item->item_description = escape($_POST['item_description']);
    $item->item_number = escape($_POST['item_number']);
    $item->item_status = 'review';
    $item->item_sold = 0;

    // inserting if not exists else fetching item_tracking_id
    $item_tracking = new ItemTracking();
    $wh_id = escape($_POST['wh_name']);
    $item_tracking->fk_wh_id = escape($wh_id);
    $ws_id  = escape($_SESSION['ws_id']);
    $item_tracking->fk_ws_id = escape($ws_id);
    $query = "SELECT * FROM item_tracking WHERE fk_wh_id='$wh_id' AND fk_ws_id='$ws_id'";
    $result = query($query);
    if (mysqli_num_rows($result) != 0) { // fetching
        $row = mysqli_fetch_assoc($result);
        $tracking_id = $row['item_tracking_id'];
        $item->fk_item_tracking_id = $tracking_id;
    } else { // inserting
        if ($item_tracking->insert()) {
            $tracking_id = $db->last_id();
            $item->fk_item_tracking_id = $tracking_id;
        }
    }

    // inserting the new item
    if ($item->update()) {
        $last_item_id = $db->last_id();
        // moving and renaming image
        $tmp = $_FILES['item_image']['tmp_name'];
        $image = $_FILES['item_image']['name'];
        if (!empty($tmp)) {
            $image = escape($image);
            unlink(__DIR__ . "/uploads/item-images/" . $_POST['prev_pic']);
            move_uploaded_file($tmp, __DIR__ . "/uploads/item-images/" . $image);
            $rplc = escape($_GET['id']);
            rename("./uploads/item-images/" . $image . "", "./uploads/item-images/" . $rplc . $image . "");

            $img = $rplc . $image;
            $item->item_image = escape($img);
            $item->item_id = $rplc;

            if ($item->update()) {
                redirect("./");
            }
        } else {
            redirect("./");
        }
    }
}
?>




<!-- Section for Supplier to list new items -->
<section class="page-top-margin">

    <div class="padding-ws-index">
        <div class="row">
            <div class="col-auto">
                <div id="h2-ws-index">
                    <h2 class="anton-regular">Do Correction</h2>
                </div>
            </div>
        </div>
    </div>

    <?php
    $item_ids = escape($_GET['id']);
    // getting the data
    $id = escape($_SESSION['ws_id']);
    $query = "SELECT * FROM items INNER JOIN item_rejection ON ";
    $query .= "items.item_id=item_rejection.fk_item_id INNER JOIN item_tracking ";
    $query .= "ON items.fk_item_tracking_id=item_tracking.item_tracking_id WHERE item_id='$item_ids' AND ";
    $query .= "fk_ws_id='$id' AND item_status='rejected' ORDER BY rejection_id DESC LIMIT 1";
    $result = query($query);

    $data = mysqli_fetch_assoc($result);
    ?>

    <div class="padding-ws-index">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col">
                    <div class="row g-0">
                        <div class="col-md-5 offset-md-1 px-0">
                            <div class="card bg-light px-0 rounded-end-0">
                                <div class="card-header bs-card-header-height bg-dark d-flex align-items-center rounded-end-0">
                                    <!-- <strong>Add Details</strong> -->
                                    <h4 class="m-0 p-0"><strong>Details</strong></h4>
                                </div>
                                <div class="card-body bg-light text-dark">
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <div class="alert alert-danger"><strong>Rejection Reason: </strong>
                                                        <?php echo $data['rejection_reason']; ?>
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="wh_name" class="form-label"><u><strong>Warehouse</strong></u></label>
                                                <select name="wh_name" class="form-select form-select-sm" required>
                                                    <option value="">Select</option>
                                                    <?php
                                                    // select all categories from the database
                                                    $query = "SELECT * FROM warehouse";
                                                    $result = query($query);
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        if ($data['fk_wh_id'] == $row['wh_id']) {
                                                    ?>
                                                            <option value="<?php echo $row['wh_id']; ?>" selected><?php echo $row['wh_name']; ?></option>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <option value="<?php echo $row['wh_id']; ?>"><?php echo $row['wh_name']; ?></option>
                                                    <?php
                                                        } // if
                                                    } // loop
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="item_category" class="form-label"><strong><u>Category</u></strong></label>
                                                <select name="item_category" class="form-select form-select-sm" required>
                                                    <option value="" selected>Select</option>
                                                    <?php
                                                    // select all categories from the database
                                                    $query = "SELECT * FROM category";
                                                    $result = query($query);
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        if ($data['item_category'] == $row['category_name']) {
                                                    ?>
                                                            <option value="<?php echo $row['category_name']; ?>" selected><?php echo $row['category_name']; ?></option>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <option value="<?php echo $row['category_name']; ?>"><?php echo $row['category_name']; ?></option>
                                                    <?php
                                                        } // if
                                                    } // loop
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="item_brand" class="form-label"><strong><u>Brand</u></strong></label>
                                                <select name="item_brand" class="form-select form-select-sm" required>
                                                    <option value="" selected>Select</option>
                                                    <?php
                                                    // select all categories from the database
                                                    $query = "SELECT * FROM brand";
                                                    $result = query($query);
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        if ($data['item_brand'] == $row['brand_name']) {
                                                    ?>
                                                            <option value="<?php echo $row['brand_name']; ?>" selected><?php echo $row['brand_name']; ?></option>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <option value="<?php echo $row['brand_name']; ?>"><?php echo $row['brand_name']; ?></option>
                                                    <?php
                                                        } // if
                                                    } // else
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- File Input Section with Image Placeholder -->
                                        <div class="mb-3">
                                            <label for="item_image" class="form-label"><strong><u>Image</u></strong></label>
                                            <div class="text-center">
                                                <?php
                                                ?>
                                                <input type="hidden" id="image-holder" value="<?php echo $data['item_image']; ?>">
                                                <img id="imagePreview" src="./uploads/item-images/<?php echo $data['item_image']; ?>" alt="Image Preview" class="rounded mb-2" style="max-width: 200px;">
                                                <!-- <img id="imagePreview" src="https://via.placeholder.com/100" alt="Image Preview" class="rounded mb-2" style="max-width: 200px; display: none;"> -->
                                            </div>
                                            <div class="input-group justify-content-center">
                                            <input type="file" name="item_image" class="form-control d-none" id="fileInput" value="" onchange="previewImage(event)">
                                                <button class="btn btn-sm btn-secondary" type="button" id="uploadButton">
                                                    <i class="bi bi-upload"></i>
                                                </button>&nbsp;
                                                <button class="btn btn-sm btn-danger d-none" type="button" id="deleteButtonC">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" type="button" id="deleteButton">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="item_number" class="form-label"><strong><u>Product Name</u></strong></label>
                                            <input type="text" name="item_number" class="form-control" style="width: auto; max-width: 400px;" value="<?php echo $data['item_number']; ?>" placeholder="eg. y8 pro" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="item_quantity" class="form-label"><strong><u>Quantity</u></strong></label>
                                            <input type="number" name="item_quantity" class="form-control" style="width: auto; max-width: 400px;" value="<?php echo $data['item_quantity']; ?>" placeholder="amount" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="item_price" class="form-label"><strong><u>Price</u><code> (per peice)</code></strong></label>
                                            <input type="number" name="item_price" class="form-control" style="width: auto; max-width: 400px;" value="<?php echo $data['item_price']; ?>" placeholder="Rs." required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="item_description" class="form-label"><strong><u>Description</u></strong></label>
                                            <textarea name="item_description" class="form-control" id="" required><?php echo $data['item_description']; ?></textarea>
                                        </div>
                                        <input type="hidden" name="prev_pic" value="<?php echo $data['item_image']; ?>">
                                        <button type="submit" name="correct" class="btn btn-block btn-danger bg-red mb-3">Re-List Product</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 d-none d-md-block px-0">
                            <img src="images/photos/do-correction-page.jpg" alt="Sample photo" class="img-fluid"
                                style="width: 100%; height: 100%; object-fit: cover; border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<?php // requiring the footer of page
require_once("includes/footer.php");
?>