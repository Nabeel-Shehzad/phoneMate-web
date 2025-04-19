<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<?php
// approve the order
if (isset($_POST['completed'])) {
    $items_sold = new ItemsSold();
    $items_sold->sell_id = escape($_POST['sell_id']);
    $items_sold->sell_status = 'completed';

    if ($items_sold->update()) {
        $delivery = new Delivery();
        $delivery->delivery_id = escape($_POST['delivery_id']);
        $delivery->delivery_status = 'delivered';
        $delivery->delivery_date = date('Y-m-d', time());

        if ($delivery->update()) {
            $buyer_ntf = new BuyerNotification();
            $buyer_ntf->message = "Your order has been delivered!";
            $buyer_ntf->date = date('Y-m-d', time());
            $buyer_ntf->fk_buyer_id = escape($_POST['buyer_id']);

            if ($buyer_ntf->insert()) {
                $ws_ntf = new WsNotification();
                $ws_ntf->message = "Your listed items have been sold, check Sold Items!";
                $ws_ntf->date = date('Y-m-d', time());
                $ws_ntf->fk_ws_id = escape($_POST['ws_id']);

                if ($ws_ntf->insert()) {
                    $item = new Item();
                    $item->item_id = escape($_POST['item_id']);
                    $item->item_sold = $_POST['item_sold'] + $_POST['delivery_quantity'];

                    if ($item->update()) {
                        $wspp = new WsPendingPayments();
                        $wspp->wspp_amount = $_POST['buying_price'] * $_POST['delivery_quantity'];
                        $wspp->fk_ws_id = escape($_POST['ws_id']);

                        if ($wspp->insert()) {
                            $total_buying_price = $_POST['buying_price'] * $_POST['delivery_quantity'];
                            $total_selling_price = escape($_POST['sell_price']);

                            $company = new CompanyEarnings();
                            $company->earning_amount = $total_selling_price - $total_buying_price;
                            $company->date = date('Y-m-d', time());
                            $company->fk_delivery_id = escape($_POST['delivery_id']);

                            if ($company->insert()) {
                                redirect("./in-delivery-orders.php");
                            }
                        }
                    }
                }
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
                <h4 class="mb-4">Complete Orders</h4>
                <div class="table-responsive">
                    <span class="text-custom">Search:</span>
                    <table id="in_delivery_orders" style="background-color: #e74c3c;" class="table table-light table-bordered table-hover">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col">Category</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Buyer</th>
                                <th scope="col">Selling Price</th>
                                <th scope="col">Total Cash</th>
                                <th scope="col">Ordered Amount</th>
                                <th scope="col">Pending Since</th>
                                <th scope="col">Mark Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // selecting all the values from the database which do not have a agreement date
                            $query = "SELECT * FROM items INNER JOIN item_tracking ON 
                                    items.fk_item_tracking_id=item_tracking.item_tracking_id 
                                    INNER JOIN wholesaler ON item_tracking.fk_ws_id=wholesaler.ws_id 
                                    INNER JOIN item_adjustment ON items.item_id=item_adjustment.fk_item_id
                                    INNER JOIN 
                                    -- (SELECT * FROM delivery WHERE delivery_status='in_delivery') 
                                    delivery ON item_adjustment.item_adj_id=delivery.fk_item_adj_id
                                    INNER JOIN 
                                    -- (SELECT * FROM items_sold WHERE sell_status='approved') 
                                    items_sold ON items.item_id=items_sold.fk_item_id
                                    INNER JOIN buyer ON items_sold.fk_buyer_id=buyer.buyer_id
                                    Where delivery_status='in_delivery' ORDER BY delivery_id DESC";
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
                                        <td><?php echo $row['buyer_name']; ?></td>
                                        <td>Rs. <?php echo $row['item_adj_price']; ?></td>
                                        <td>Rs. <?php echo $row['total_cash']; ?></td>
                                        <td><?php echo $order_amount; ?></td>
                                        <td><?php echo $row['delivery_date']; ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-square btn-outline-info m-2"
                                                data-bs-toggle="modal" data-bs-target="#completeOrder<?php echo $row['sell_id']; ?>">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- dynamic modal -->
                                    <div class="modal fade" id="completeOrder<?php echo $row['sell_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-secondary">
                                                    <h5 class="modal-title text-light" id="exampleModalLabel">Complete Order</h5>
                                                    <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body bg-light p-4">
                                                    <p class="text-white bg-primary mx-5 p-2 rounded">
                                                        <strong class="text-white fw-bold">Alert!</strong>
                                                        Please confirm that order is delivered and amount is received!
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
                                                        <strong class="text-dark fw-bold">Buyer:</strong>
                                                        <?php echo $row['buyer_name']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Selling Price:</strong>
                                                        Rs. <?php echo $row['item_adj_price']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Total Cash:</strong>
                                                        Rs. <?php echo $row['total_cash']; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Ordered Amount:</strong>
                                                        <?php echo $order_amount; ?>
                                                    </p>
                                                    <p class="text-white">
                                                        <strong class="text-dark fw-bold">Pending Since:</strong>
                                                        <?php echo $row['delivery_date']; ?>
                                                    </p>
                                                </div>
                                                <form action="" method="post">
                                                    <div class="modal-footer bg-secondary">
                                                        <input type="hidden" name="buyer_id" value="<?php echo $row['buyer_id']; ?>">
                                                        <input type="hidden" name="sell_id" value="<?php echo $row['sell_id']; ?>">
                                                        <input type="hidden" name="sell_price" value="<?php echo $row['sell_price']; ?>">
                                                        <input type="hidden" name="delivery_quantity" value="<?php echo $order_amount; ?>">
                                                        <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                                        <input type="hidden" name="ws_id" value="<?php echo $row['ws_id']; ?>">
                                                        <input type="hidden" name="delivery_id" value="<?php echo $row['delivery_id']; ?>">
                                                        <input type="hidden" name="buying_price" value="<?php echo $buying_price; ?>">
                                                        <input type="hidden" name="item_sold" value="<?php echo $row['item_sold']; ?>">
                                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="completed" class="btn btn-outline-success">Mark Completed</button>
                                                    </div>
                                                </form>
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