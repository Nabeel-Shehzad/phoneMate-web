<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

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
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Approved Items</h3>

        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Adjust items</h4>
                <div class="table-responsive">
                    <span class="text-custom">Search:</span>
                    <table id="listing_adjustment" style="background-color: #e74c3c;" class="table table-light table-bordered table-hover">
                        <thead>
                            <tr class="text-dark">
                                <!-- <th scope="col">#</th> -->
                                <th scope="col">Image</th>
                                <th scope="col">Category</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Buying Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Make Adjustment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // selecting all the values from the database which are not processed
                            $query = "SELECT * FROM items INNER JOIN item_tracking ON items.fk_item_tracking_id=item_tracking.item_tracking_id ";
                            $query .= "Where item_status='approved' ORDER BY item_id DESC";
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
                                        <td><?php echo $row['item_category']; ?></td>
                                        <td><?php echo $row['item_brand']; ?></td>
                                        <td><?php echo $row['item_number']; ?></td>
                                        <td>Rs. <?php echo $buying_price; ?></td>
                                        <td><?php echo $row['item_quantity']; ?></td>
                                        <td class="text-center">
                                            <a href="adjust-item-details.php?id=<?php echo $row['item_id']; ?>" class="btn btn-square btn-outline-info m-2">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </a>
                                        </td>
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
                                                        No new items for adjustment <?php echo $_SESSION['admin_name']; ?>, enjoy your day! &#x1F603;
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


        <!-- Modal -->
        <div class="modal fade" id="listingAdjustment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-secondary">
                        <h5 class="modal-title text-light" id="exampleModalLabel">Item Adjustment</h5>
                        <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post">
                        <div class="modal-body bg-light p-4">
                            <div class="form-floating mb-3">
                                <input type="number" name="adj_price" min="1" class="form-control" id="floatingInput"
                                    placeholder="134" required>
                                <label for="adj_price">Adjusted Price <code>*</code></label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="number" name="ppu" min="1" class="form-control" id="floatingPassword"
                                    placeholder="6" required>
                                <label for="ppu">Pieces per unit <code>*</code></label>
                            </div>
                        </div>
                        <div class="modal-footer bg-secondary">
                            <input type="hidden" name="fk_item_id" id="fk_item_id" value="">
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="adjusted" class="btn btn-outline-success">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function setItemId(fkItemId) {
                document.getElementById('fk_item_id').value = fkItemId;
            }
        </script>

        <?php // including the footer
        require_once("../../includes/footer.php");
        ?>