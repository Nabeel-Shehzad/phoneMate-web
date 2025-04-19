<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<?php
// submitting the form
if (isset($_POST['tagged'])) {
    $item_adj = new ItemAdjustment();
    $item_adj->item_tag = $db->escape($_POST['radios']);
    $item_adj->item_adj_id = $db->escape($_POST['item_adj_id']);

    if ($item_adj->update()) {
        redirect("./processed-listings.php");
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Processed Items</h3>

        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Details</h4>
                <div class="table-responsive">
                    <span class="text-custom">Search:</span>
                    <table id="processed_listings" style="background-color: #e74c3c;" class="table table-light table-bordered table-hover">
                        <thead>
                            <tr class="text-dark">
                                <!-- <th scope="col">#</th> -->
                                <th scope="col">Image</th>
                                <th scope="col">Supplier</th>
                                <th scope="col">Category</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Tagged</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Buying Price</th>
                                <th scope="col">Selling Price</th>
                                <th scope="col">Pieces/Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // selecting all the values from the database which do not have a agreement date
                            $query = "SELECT * FROM items INNER JOIN item_tracking ON items.fk_item_tracking_id=item_tracking.item_tracking_id ";
                            // $query .= "INNER JOIN item_tracking ON items.fk_item_tracking_id=item_tracking.item_tracking_id ";
                            $query .= "INNER JOIN wholesaler ON item_tracking.fk_ws_id=wholesaler.ws_id ";
                            $query .= "INNER JOIN item_adjustment ON items.item_id=item_adjustment.fk_item_id ";
                            $query .= "Where item_status='processed' ORDER BY item_adj_id DESC";
                            $result = mysqli_query($db->conn, $query);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {

                                    $buying_price = (int) $row['item_price'] + (((int) $row['item_price'] / 100) * $row['item_profit']);

                            ?>
                                    <tr>
                                        <!-- <th scope="row">1</th> -->
                                        <td>
                                            <a href="#">
                                                <img class="rounded-circle" src="../../../uploads/item-images/<?php echo $row['item_image']; ?>" width="60px" height="60px" alt="err...">
                                            </a>
                                        </td>
                                        <td><?php echo $row['ws_name']; ?></td>
                                        <td><?php echo $row['item_category']; ?></td>
                                        <td><?php echo $row['item_brand']; ?></td>
                                        <td><?php echo $row['item_number']; ?></td>
                                        <td class="text-center">
                                            <div class="badge rounded-pill bg-primary">
                                                <?php echo $row['item_tag']; ?>
                                            </div>
                                            <button type="button" onclick="setItemId('<?php echo $row['item_adj_id']; ?>', '<?php echo $row['item_tag']; ?>')" class="btn btn-square btn-outline-info m-2" data-bs-toggle="modal" data-bs-target="#changeTag">
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                        <td><?php echo $row['item_quantity']-$row['item_sold']; ?></td>
                                        <td>Rs. <?php echo $buying_price; ?></td>
                                        <td>Rs. <?php echo $row['item_adj_price']; ?></td>
                                        <td><?php echo $row['pieces_pu']; ?></td>
                                    </tr>
                                <?php
                                    // end of the loop to fetch data
                                }
                                // end of if statement if the returned result is > 0
                            } else {
                                // show the message if no records
                                ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="row text-center">
                                            <div class="col-md-6 offset-md-3">
                                                <div class="alert alert-success"><strong>
                                                        No processed item available <?php echo $_SESSION['admin_name']; ?>! &#x1F603;
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

<!-- Modal -->
<div class="modal fade" id="changeTag" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Change Tag</h5>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
                <div class="modal-body bg-light p-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="radios"
                            id="hot_selling" value="hot_selling" required>
                        <label class="form-check-label" for="Radios">
                            <div class="badge rounded-pill bg-primary ms-auto">Hot Selling</div>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="radios"
                            id="top_selling" value="top_selling">
                        <label class="form-check-label" for="Radios">
                            <div class="badge rounded-pill bg-primary ms-auto">Top Selling</div>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="radios"
                            id="normal_selling" value="normal_selling">
                        <label class="form-check-label" for="Radios">
                            <div class="badge rounded-pill bg-primary ms-auto">Normal Selling</div>
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-secondary">
                    <input type="hidden" name="item_adj_id" id="item_adj_id" value="">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="tagged" class="btn btn-outline-success">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function setItemId(itemAdjId, tag) {
        document.getElementById('item_adj_id').value = itemAdjId;
        document.getElementById(tag).disabled = true;
    }
</script>

<?php // including the footer
require_once("../../includes/footer.php");
?>