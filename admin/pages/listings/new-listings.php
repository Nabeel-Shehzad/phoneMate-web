<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<?php
// PHP processing
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['value'])) {
//     $receivedValue = htmlspecialchars($_POST['value']);
//     // Return the response to be displayed in the modal
//     echo "Received value: " . $receivedValue;
// }
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">New Listed Items</h3>

        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Approve items</h4>
                <div class="table-responsive">
                    <span class="text-custom">Search:</span>
                    <table id="new_listings" style="background-color: #e74c3c;" class="table table-light table-bordered table-hover">
                        <thead>
                            <tr class="text-dark">
                                <!-- <th scope="col">#</th> -->
                                <th scope="col">Image</th>
                                <th scope="col">Category</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Price(pp)</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Description</th>
                                <th scope="col">Approve</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // selecting all the values from the database which do not have a agreement date
                            $query = "SELECT * FROM items INNER JOIN item_tracking ON items.fk_item_tracking_id=item_tracking.item_tracking_id ";
                            $query .= "Where item_status='review' ORDER BY item_id DESC";
                            $result = mysqli_query($db->conn, $query);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {

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
                                        <td>Rs. <?php echo $row['item_price']; ?></td>
                                        <td><?php echo $row['item_quantity']; ?></td>
                                        <td><?php echo substr($row['item_description'], 0, 20); ?>...</td>
                                        <td>
                                            <!-- This code was used as a post request, not used anymore -->
                                            <!-- <form action="./approve-listings.php" method="post"> -->
                                            <!-- <input type="hidden" name="item_id" value="<?php //echo $row['item_id']; 
                                                                                            ?>"> -->
                                            <!-- <input type="hidden" name="ws_id" value="<?php //echo $row['fk_ws_id']; 
                                                                                            ?>"> -->
                                            <!-- <button type="submit" name="approve" class="btn btn-square btn-outline-info m-2"><i class="fa fa-check" aria-hidden="true"></i></button> -->
                                            <a href="./approve-listings.php?ws_id=<?php echo $row['fk_ws_id']; ?>&item_id=<?php echo $row['item_id']; ?>" class="btn btn-square btn-outline-info m-2"><i class="fa fa-check" aria-hidden="true"></i></a>
                                            <!-- </form> -->
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
                                                        No new items for approval <?php echo $_SESSION['admin_name']; ?>, enjoy your day! &#x1F603;
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

        <button type="submit" style="display: none;" class="btn btn-square btn-outline-info m-2" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-check" aria-hidden="true"></i></button>

        <!-- the modal code -->
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-secondary">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                        <button type="button" class="btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalContent">
                        <!-- Dynamic content will be inserted here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-outline-success">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php // including the footer
require_once("../../includes/footer.php");
?>