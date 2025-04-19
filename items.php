<?php // requiring the header of page
require_once("includes/header.php");
?>

<style>
.btn-danger {
    background-color: #eb1616 !important;
    border-color: #eb1616 !important;
}
.btn-danger:hover {
    background-color: #d41414 !important;
    border-color: #d41414 !important;
}
.btn-outline-danger {
    color: #eb1616 !important;
    border-color: #eb1616 !important;
}
.btn-outline-danger:hover {
    background-color: #eb1616 !important;
    border-color: #eb1616 !important;
    color: white !important;
}
.bg-red {
    background-color: #eb1616 !important;
}
</style>

<div class="container mt-4"  style="padding-top: 70px;">
    <div class="row mb-4">
        <div class="col-12 text-end">
            <a href="list-product.php" class="btn btn-outline-danger">
                <i class="fa fa-plus-circle me-2"></i>List New Item
            </a>
        </div>
    </div>
</div>

<?php
// add more quantity
if (isset($_POST['added_more'])) {
  $item = new Item();
  $item->item_id = escape($_POST['item_id']);
  $item->more_quantity = ((int) escape($_POST['add_more']) + (int) escape($_POST['prev_more']));

  if ($item->update()) {
    redirect("./");
  }
}
?>

<!-- Section for Supplier to view listed items -->
<section>

  <div class="padding-ws-index">
    <div class="row">
      <div class="col-auto">
        <div id="h3-ws-index">
          <h3 class="anton-regular">My listed items</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="padding-ws-index">
    <div class="row">
      <div class="container-fluid">
        <div class="col-auto">
          <div class="table-responsive">
            <span class="text-custom">Search:</span>
            <table id="my_listed_items" class="table table-info table-hover table-bordered border-info">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">Brand</th>
                  <th scope="col">Category</th>
                  <th scope="col">Product Name</th>
                  <th scope="col">Quantity</th>
                  <th scope="col">Sold</th>
                  <th scope="col">Price (pp)</th>
                  <th scope="col">Profit (pp)</th>
                  <th scope="col">Cost</th>
                  <th scope="col">Agreement date</th>
                  <th scope="col">More quantity</th>
                  <th scope="col">Details</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // selecting all the listed items of this seller
                $id = escape($_SESSION['ws_id']);
                $query = "SELECT * FROM items INNER JOIN item_tracking ON ";
                $query .= "items.fk_item_tracking_id=item_tracking.item_tracking_id WHERE ";
                $query .= "fk_ws_id='$id' AND NOT item_status='rejected' ORDER BY item_id DESC";

                $result = query($query);

                if (mysqli_num_rows($result) == 0) {
                  // code to show if the table is empty
                ?>
                  <tr>
                    <td colspan="10">
                      <div class="row text-center">
                        <div class="col-md-6 offset-md-3 col-8 offset-2">
                          <div class="alert alert-success"><strong>
                              Getting started? We are here to help you, list your first item <a href="./list-product.php">here</a> &#x1F448;
                            </strong></div>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <?php
                } else { // if there is sell record

                  // to fetch sell records of the listed items
                  while ($row = mysqli_fetch_assoc($result)) {
                    $quantity = $row['item_quantity']; // quantity
                    $price = $row['item_price']; // price
                    $total = $quantity * $price; // total amount for unsold quantity

                    $profit = $row['item_profit']; // profit per piece
                    $profit_rs = ($profit / 100) * $price; // profit in rupees
                  ?>
                    <tr>
                      <td><?php echo $row['item_brand']; ?></td>
                      <td><?php echo $row['item_category']; ?></td>
                      <td><?php echo $row['item_number']; ?></td>
                      <td><?php echo $row['item_quantity'] - $row['item_sold']; ?></td>
                      <td><?php echo $row['item_sold']; ?></td>
                      <td>Rs.<?php echo $row['item_price']; ?></td>
                      <td>Rs.<?php echo $profit_rs; ?> (<?php echo $row['item_profit']; ?>%)</td>
                      <td>Rs.<?php echo $total; ?></td>
                      <td>
                        <?php
                        if ($row['item_status'] == 'review') {
                          echo "<code>under review</code>";
                        } else {
                          echo $row['agreement_date'];
                        }
                        ?>
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm btn-danger bg-red"
                          data-bs-toggle="modal" data-bs-target="#addMore"
                          onclick="setData('<?php echo $row['item_image']; ?>', '<?php echo $row['item_brand']; ?>', 
                          '<?php echo $row['item_category']; ?>', '<?php echo $row['item_number']; ?>', 
                          '<?php echo $row['item_quantity']; ?>', '<?php echo $row['item_sold']; ?>', 
                          '<?php echo $row['item_id']; ?>', '<?php echo $row['more_quantity']; ?>')">
                          Add
                        </button>
                      </td>
                      <td>
                        <a href="productview.php?id=<?php echo $row['item_id']; ?>">
                          <button class="btn btn-sm btn-danger">View</button>
                        </a>
                      </td>
                    </tr>

                <?php
                    // end of loop to show listed items
                  }
                  // end of else {if there is record}
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- End section to view listed items -->

<!-- Section for Supplier to view sold items -->
<section>

  <div class="padding-ws-index">
    <div class="row">
      <div class="col-auto">
        <div id="h3-ws-index">
          <h3 class="anton-regular">Sold Items</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="padding-ws-index">
    <div class="row">
      <div class="container-fluid">
        <div class="col-auto">
          <div class="table-responsive">
            <span class="text-custom">Search:</span>
            <table id="my_sold_items" class="table table-info table-hover table-bordered border-info">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">Brand</th>
                  <th scope="col">Category</th>
                  <th scope="col">Product Name</th>
                  <th scope="col">Items Sold</th>
                  <th scope="col">Date</th>
                  <th scope="col">Price (pp)</th>
                  <th scope="col">Profit (pp)</th>
                  <th scope="col">Profit & Gross</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // selecting all the sold items of this seller
                $id = escape($_SESSION['ws_id']);
                $query = "SELECT * FROM items_sold INNER JOIN items ON ";
                $query .= "items_sold.fk_item_id=items.item_id INNER JOIN item_tracking ";
                $query .= "ON items.fk_item_tracking_id=item_tracking.item_tracking_id ";
                $query .= "INNER JOIN item_adjustment ON items_sold.fk_item_id=item_adjustment.fk_item_id WHERE ";
                $query .= "fk_ws_id='$id' AND item_status='processed' AND sell_status='completed'";

                $result = query($query);
                if (mysqli_num_rows($result) == 0) {
                  // code to show if the table is empty
                ?>
                  <tr>
                    <td colspan="8">
                      <div class="row text-center">
                        <div class="col-md-6 offset-md-3 col-8 offset-2">
                          <div class="alert alert-success"><strong>
                              No items sold yet! Do not worry, we are here for you &#x1F4AA;
                            </strong></div>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <?php
                } else { // if there is sell record

                  // to fetch sell records of the sold items

                  $check = array();
                  $data = array();

                  while ($rows = mysqli_fetch_assoc($result)) {
                    if (!isset($check[$rows['sell_id']])) {
                      $check[$rows['sell_id']] = $rows['sell_id'];
                      $data[] = $rows;
                    }
                  }



                  $show_total = 0;
                  foreach ($data as $row) {
                    $quantity = $row['sell_quantity'] * $row['pieces_pu']; // quantity
                    $price = $row['item_price']; // price
                    $total = $quantity * $price; // total amount for unsold quantity

                    $profit = $row['item_profit']; // profit per piece
                    $profit_rs = ($profit / 100) * $price; // profit in rupees per piece

                    $total_profit_rs = $profit_rs * $quantity; // total profit on items sold
                    $total += $total_profit_rs; // total payment

                  ?>
                    <tr>
                      <td><?php echo $row['item_brand']; ?></td>
                      <td><?php echo $row['item_category']; ?></td>
                      <td><?php echo $row['item_number']; ?></td>
                      <td><?php echo $quantity; ?></td>
                      <td><?php echo $row['sell_date']; ?></td>
                      <td>Rs.<?php echo $row['item_price']; ?></td>
                      <td>Rs.<?php echo $profit_rs; ?> (<?php echo $row['item_profit']; ?>%)</td>
                      <td>Rs.<?php echo $total; ?></td>
                    </tr>

                  <?php
                    // total earnings of the seller
                    $show_total += $total;
                    // end of loop to show listed items
                  }
                  ?>
                  <!-- total payable to the seller -->
                  <tr>
                    <td colspan="7"><strong>Total</strong></td>
                    <td>Rs.<?php echo $show_total; ?></td>
                  </tr>
                  <!-- end total payable to the seller -->
                <?php
                  // end of else statement{if there is the reocord}
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end section to view sold items -->

<!-- Section for Supplier to view rejected items -->
<section>

  <div class="padding-ws-index">
    <div class="row">
      <div class="col-auto">
        <div id="h3-ws-index">
          <h3 class="anton-regular">Rejected Items</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="padding-ws-index">
    <div class="row">
      <div class="container-fluid">
        <div class="col-auto">
          <div class="table-responsive">
            <span class="text-custom">Search:</span>
            <table id="my_rejected_items" class="table table-info table-hover table-bordered border-info">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">Brand</th>
                  <th scope="col">Category</th>
                  <th scope="col">Product Name</th>
                  <th scope="col">Rejection Reason</th>
                  <th scope="col">Do Correction</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // selecting all the rejected items of this seller
                $id = escape($_SESSION['ws_id']);
                $query = "SELECT * FROM items INNER JOIN item_rejection ON ";
                $query .= "items.item_id=item_rejection.fk_item_id INNER JOIN item_tracking ";
                $query .= "ON items.fk_item_tracking_id=item_tracking.item_tracking_id WHERE ";
                $query .= "fk_ws_id='$id' AND item_status='rejected' ORDER BY rejection_id DESC";

                $result = query($query);
                if (mysqli_num_rows($result) == 0) {
                  // code to show if the table is empty
                ?>
                  <tr>
                    <td colspan="8">
                      <div class="row text-center">
                        <div class="col-md-6 offset-md-3 col-8 offset-2">
                          <div class="alert alert-success"><strong>
                              Great! you are on a roll, no rejections &#x1F920;
                            </strong></div>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <?php
                } else { // if there is sell record

                  $check = array();
                  $data3 = array();
                  while ($rows = mysqli_fetch_assoc($result)) {
                    if (!isset($check[$rows['item_id']])) {
                      $check[$rows['item_id']] = $rows['item_id'];
                      $data3[] = $rows;
                    }
                  }
                  // to fetch sell records of the rejected items
                  foreach ($data3 as $row) {
                  ?>
                    <tr>
                      <td><?php echo $row['item_brand']; ?></td>
                      <td><?php echo $row['item_category']; ?></td>
                      <td><?php echo $row['item_number']; ?></td>
                      <td><?php echo substr($row['rejection_reason'], 0, 25); ?>...</td>
                      <td>
                        <a href="./do-correction.php?id=<?php echo $row['item_id']; ?>">
                          <button class="btn btn-sm btn-danger bg-red">
                            Correct
                          </button>
                        </a>
                      </td>

                    <?php
                    // end of loop to show listed items
                  }
                    ?>
                  <?php
                  // end of else statement{if there is the reocord}
                }
                  ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end section to view rejected items -->


<script>
  function setData(img, brand, cat, iNumber, quantity, sold, itemId, more) {
    document.getElementById('modalPic').src = './uploads/item-images/' + img;
    document.getElementById('brandNcat').innerHTML = '<strong>' + brand + ' (' + cat + ')</strong>';
    document.getElementById('iNumber').innerHTML = '<strong>Product Name:</strong> ' + iNumber;
    document.getElementById('quantity').innerHTML = '<strong>Listed Quantity:</strong> ' + quantity;
    document.getElementById('sold').innerHTML = '<strong>Sold Quantity:</strong> ' + sold;
    document.getElementById('item_id').value = itemId;
    document.getElementById('prev_more').value = more;
  }
</script>

<!-- Modal -->
<div class="modal fade my-5" id="addMore" tabindex="-1" aria-labelledby="addMoreLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addMoreLabel">Add More Quantity</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="post">
        <div class="modal-body px-5">
          <div class="text-center">
            <img id="modalPic" src="" alt="" class="rounded" width="100px" height="100px">
          </div>
          <p id="brandNcat" class="m-0 mt-3 p-0"></p>
          <p id="iNumber" class="m-0 p-0"></p>
          <p id="quantity" class="m-0 p-0"></p>
          <p id="sold" class="m-0 p-0"></p>
          <div class="alert alert-danger">
            <strong>Note! </strong> More quantity will be approved on previous terms, be aware.
          </div>
          <div class="mb-3">
            <label for="add_more" class="form-label"><strong><u>Enter Quantity</u><code>*</code></strong></label>
            <input type="text" name="add_more" class="form-control" style="width: auto; max-width: 400px;" placeholder="quantity" required>
          </div>
        </div>
        <div class="modal-footer mb-5">
          <input type="hidden" id="item_id" name="item_id" value="">
          <input type="hidden" id="prev_more" name="prev_more" value="">
          <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="added_more" class="btn btn-success">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php // requiring the footer of page
require_once("includes/footer.php");
?>