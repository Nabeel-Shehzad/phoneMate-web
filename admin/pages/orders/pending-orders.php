<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<?php
// approve the order
if (isset($_POST['approved'])) {
    $items_sold = new ItemsSold();
    $items_sold->sell_id = escape($_POST['sell_id']);
    $items_sold->sell_status = 'approved';

    if ($items_sold->update()) {
        $delivery = new Delivery();
        $delivery->delivery_quantity = escape($_POST['delivery_quantity']);
        $delivery->total_cash = escape($_POST['sell_price']);
        $delivery->delivery_status = 'in_delivery';
        $delivery->delivery_date = date('Y-m-d', time());
        $delivery->fk_item_adj_id = escape($_POST['item_adj_id']);
        $delivery->fk_buyer_id = escape($_POST['buyer_id']);
        
        if ($delivery->insert()) {
            $buyer_ntf = new BuyerNotification();
            $buyer_ntf->message = "Your order has been approved and is in delivery!";
            $buyer_ntf->date = date('Y-m-d', time());
            $buyer_ntf->fk_buyer_id = escape($_POST['buyer_id']);

            if ($buyer_ntf->insert()) {
                redirect("./pending-orders.php");
            }
        }
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">Orders</h3>

        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Approve Orders</h4>
                <div class="table-responsive">
                    <span class="text-custom">Search:</span>
                    <table id="pending_orders" style="background-color: #e74c3c;" class="table table-light table-bordered table-hover">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col">Category</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Seller</th>
                                <th scope="col">Buyer</th>
                                <th scope="col">Buying Price</th>
                                <th scope="col">Selling Price</th>
                                <th scope="col">Ordered Amount</th>
                                <th scope="col">Approve</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // selecting all the values from the database which do not have a agreement date
                            $query = "SELECT * FROM items INNER JOIN item_tracking ON items.fk_item_tracking_id=item_tracking.item_tracking_id ";
                            $query .= "INNER JOIN wholesaler ON item_tracking.fk_ws_id=wholesaler.ws_id ";
                            $query .= "INNER JOIN item_adjustment ON items.item_id=item_adjustment.fk_item_id ";
                            $query .= "INNER JOIN items_sold ON items.item_id=items_sold.fk_item_id ";
                            $query .= "INNER JOIN buyer ON items_sold.fk_buyer_id=buyer.buyer_id ";
                            $query .= "Where sell_status='pending' ORDER BY sell_id DESC";
                            $result = mysqli_query($db->conn, $query);

                            $arr = [];
                            $checks = [];
                            while ($rows = mysqli_fetch_assoc($result)) {
                                if (!isset($checks[$rows['sell_id']])) {
                                    $checks[$rows['sell_id']] = $rows['sell_id'];
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
                                        <td><?php echo $row['ws_name'] ?></td>
                                        <td><?php echo $row['buyer_name']; ?></td>
                                        <td>Rs. <?php echo $buying_price; ?></td>
                                        <td>Rs. <?php echo $row['item_adj_price']; ?></td>
                                        <td><?php echo $order_amount; ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-square btn-outline-info m-2"
                                                data-bs-toggle="modal" data-bs-target="#pendingOrder<?php echo $row['sell_id']; ?>">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- dynamic modal -->
                                    <div class="modal fade" id="pendingOrder<?php echo $row['sell_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-secondary">
                                                    <h5 class="modal-title text-light" id="exampleModalLabel">Approve Order</h5>
                                                    <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body bg-light p-4">
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
                                                </div>
                                                <form action="" method="post">
                                                    <div class="modal-footer bg-secondary">
                                                        <input type="hidden" name="buyer_id" value="<?php echo $row['buyer_id']; ?>">
                                                        <input type="hidden" name="item_adj_id" value="<?php echo $row['item_adj_id']; ?>">
                                                        <input type="hidden" name="sell_id" value="<?php echo $row['sell_id']; ?>">
                                                        <input type="hidden" name="sell_price" value="<?php echo $row['sell_price']; ?>">
                                                        <input type="hidden" name="delivery_quantity" value="<?php echo $order_amount; ?>">
                                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="approved" class="btn btn-outline-success">Approve</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="9">
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