<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Orders</h3>

        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Completed Orders</h4>
                <div class="table-responsive">
                    <span class="text-custom">Search:</span>
                    <table id="completed_orders" style="background-color: #e74c3c;" class="table table-light table-bordered table-hover">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col">Category</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Buying Price</th>
                                <th scope="col">Selling Price</th>
                                <th scope="col">Ordered Amount</th>
                                <th scope="col">Received Cash</th>
                                <th scope="col">Delivery Date</th>
                                <th scope="col">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // selecting all the values from the database which do not have a agreement date
                            $query = "SELECT * FROM items INNER JOIN item_tracking ON 
                                    items.fk_item_tracking_id=item_tracking.item_tracking_id 
                                    INNER JOIN wholesaler ON item_tracking.fk_ws_id=wholesaler.ws_id 
                                    INNER JOIN item_adjustment ON items.item_id=item_adjustment.fk_item_id
                                    INNER JOIN (SELECT * FROM delivery WHERE delivery_status='delivered') 
                                    delivery ON item_adjustment.item_adj_id=delivery.fk_item_adj_id
                                    INNER JOIN (SELECT * FROM items_sold WHERE sell_status='completed') 
                                    items_sold ON items.item_id=items_sold.fk_item_id
                                    INNER JOIN buyer ON items_sold.fk_buyer_id=buyer.buyer_id
                                    Where delivery_status='delivered' ORDER BY delivery_id DESC";
                            $result = mysqli_query($db->conn, $query);

                            $arr = [];
                            $checks = [];
                            while ($rows = mysqli_fetch_assoc($result)) {
                                if (!isset($checks[$rows['delivery_id']])) {
                                    $checks[$rows['delivery_id']] = $rows['delivery_id'];
                                    $arr[] = $rows;
                                }
                            }


                            if (mysqli_num_rows($result) > 0) {
                                foreach ($arr as $row) {
                                    $buying_price = (($row['item_price'] / 100) * $row['item_profit']) + $row['item_price'];
                                    $order_amount = $row['sell_quantity'] * $row['pieces_pu'];
                            ?>
                                    <tr>
                                        <td><?php echo $row['item_category']; ?></td>
                                        <td><?php echo $row['item_brand']; ?></td>
                                        <td><?php echo $row['item_number']; ?></td>
                                        <td>Rs. <?php echo $buying_price; ?></td>
                                        <td>Rs. <?php echo $row['item_adj_price']; ?></td>
                                        <td><?php echo $order_amount; ?></td>
                                        <td>Rs. <?php echo $row['total_cash']; ?></td>
                                        <td><?php echo $row['delivery_date']; ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-square btn-outline-info m-2"
                                                data-bs-toggle="modal" data-bs-target="#completedOrder<?php echo $row['sell_id']; ?>">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- dynamic modal -->
                                    <div class="modal fade" id="completedOrder<?php echo $row['sell_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-secondary">
                                                    <h5 class="modal-title text-light" id="exampleModalLabel">Complete Order</h5>
                                                    <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body bg-light p-4">
                                                    <p class="text-white bg-success mx-5 p-2 rounded">
                                                        <strong class="text-white fw-bold">Note!</strong>
                                                        Order was successfully delivered and amount was received!
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Category:</strong>
                                                        <?php echo $row['item_category']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Brand:</strong>
                                                        <?php echo $row['item_brand']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Product Name:</strong>
                                                        <?php echo $row['item_number']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Seller:</strong>
                                                        <?php echo $row['ws_name']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Buyer:</strong>
                                                        <?php echo $row['buyer_name']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Buying Price:</strong>
                                                        Rs. <?php echo $buying_price; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Selling Price:</strong>
                                                        Rs. <?php echo $row['item_adj_price']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Ordered Amount:</strong>
                                                        <?php echo $order_amount; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Received Cash:</strong>
                                                        Rs. <?php echo $row['total_cash']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Delivered On:</strong>
                                                        <?php echo $row['delivery_date']; ?>
                                                    </p>
                                                </div>
                                                <div class="modal-footer bg-secondary">
                                                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                                    <!-- <button type="submit" name="completed" class="btn btn-outline-success">Mark Completed</button> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>



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
                                                        No pending orders at this moment <?php echo $_SESSION['admin_name']; ?>, enjoy your day! &#x1F603;
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


<?php // including the footer
require_once("../../includes/footer.php");
?>