<?php
require_once("includes/header.php");
?>


<!-- <div class="container mt-4">
    <ul class="list-unstyled">
        <li class="alert alert-success payment-li-item">Payment Method 1</li>
        <li class="alert alert-success payment-li-item">Payment Method 2</li>
        <li class="alert alert-success payment-li-item">Payment Method 3</li>
        <li class="alert alert-success payment-li-item">Payment Method 4</li>
    </ul>
</div> -->

<div id="fullscreenOverlay" class="fullscreen-overlay">
    <img id="fullscreenImage" src="" alt="Full Image">
    <button id="closeButton" class="btn btn-outline-danger m-2" onclick="closeImage()"><i class="fa fa-times" aria-hidden="true"></i></button>
</div>

<!-- Payment Record Overlay -->
<div id="paymentRecordOverlay" class="payment-record-overlay">
    <div class="payment-record-modal">
        <button class="btn btn-outline-danger close-overlay" onclick="closePaymentRecord()">
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
        <div class="payment-record-content">
            <div class="payment-record-header">
                <h3>Payment Details</h3>
            </div>
            <div class="payment-record-body">
                <div class="payment-record-image">
                    <img id="paymentRecordImage" src="" alt="Payment Receipt">
                </div>
                <div class="payment-record-info">
                    <p><strong>Amount:</strong> <span id="paymentAmount"></span></p>
                    <p><strong>Date:</strong> <span id="paymentDate"></span></p>
                    <p><strong>Status:</strong> <span id="paymentStatus"></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section for Supplier to list new items -->
<section class="page-top-margin">
    <!-- Summary Cards Section -->
    <div class="padding-ws-index" style="margin-top: 50px;">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="alert alert-primary text-primary">
                <!-- <div class="bg-primary text-white rounded p-4"> -->
                    <h5 class="mb-3">Total Gross & Profit</h5>
                    <?php
                    $id = escape($_SESSION['ws_id']);
                    $query = "SELECT * FROM items_sold INNER JOIN items ON ";
                    $query .= "items_sold.fk_item_id=items.item_id INNER JOIN item_tracking ";
                    $query .= "ON items.fk_item_tracking_id=item_tracking.item_tracking_id ";
                    $query .= "INNER JOIN item_adjustment ON items_sold.fk_item_id=item_adjustment.fk_item_id WHERE ";
                    $query .= "fk_ws_id='$id' AND item_status='processed' AND sell_status='completed'";
                    $result = query($query);
                    
                    $total_gross_profit = 0;
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $quantity = $row['sell_quantity'] * $row['pieces_pu'];
                            $price = $row['item_price'];
                            $profit = $row['item_profit'];
                            $profit_rs = ($profit / 100) * $price;
                            $total_gross_profit += ($quantity * $price) + ($profit_rs * $quantity);
                        }
                    }
                    ?>
                    <h3 class="mb-0">Rs. <?php echo number_format($total_gross_profit, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-success text-success">
                <!-- <div class="bg-success text-white rounded p-4"> -->
                    <h5 class="mb-3">Total Paid</h5>
                    <?php
                    $query = "SELECT SUM(wsr_paid) as total_paid FROM ws_payment_records WHERE fk_ws_id='$id'";
                    $result = query($query);
                    $total_paid = mysqli_fetch_assoc($result)['total_paid'] ?? 0;
                    ?>
                    <h3 class="mb-0">Rs. <?php echo number_format($total_paid, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-danger text-danger">
                <!-- <div class="bg-danger text-white rounded p-4"> -->
                    <h5 class="mb-3">Pending Payment</h5>
                    <?php
                    $pending_payment = $total_gross_profit - $total_paid;
                    ?>
                    <h3 class="mb-0">Rs. <?php echo number_format($pending_payment, 2); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="padding-ws-index" style="margin-top: 40px;">
        <!-- <div class="row">
            <div class="col-auto">
                <div id="h2-ws-index">
                    <h2 class="anton-regular">Profit and Gross Amount</h2>
                </div>
            </div>
        </div> -->
    </div>

    <section>
        <div class="padding-ws-index">
            <div class="row">
                <div class="container-fluid">
                    <div class="col-auto">
                        <div class="table-responsive">
                            <span class="text-custom">Search:</span>
                            <table id="payments_profit_and_gross" class="table table-info table-hover table-bordered border-info">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Items Sold</th>
                                        <th scope="col">Price (pp)</th>
                                        <th scope="col">Gross Amount</th>
                                        <th scope="col">Profit</th>
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
                                    $query .= "fk_ws_id='$id' AND item_status='processed' AND sell_status='completed' ORDER BY sell_id ASC";

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
                                        $show_total_profit = 0;
                                        $show_total_gross = 0;
                                        foreach ($data as $row) {
                                            $quantity = $row['sell_quantity'] * $row['pieces_pu']; // quantity
                                            $price = $row['item_price']; // price
                                            $total = $quantity * $price; // total amount for unsold quantity

                                            $profit = $row['item_profit']; // profit per piece
                                            $profit_rs = ($profit / 100) * $price; // profit in rupees per piece

                                            $total_profit_rs = $profit_rs * $quantity; // total profit on items sold
                                            $total_gross = $total; // total gross amount
                                            $total_profit = $total_profit_rs; // total profit
                                            $total += $total_profit_rs; // total payment

                                        ?>
                                            <tr>
                                                <td><?php echo $row['item_number']; ?></td>
                                                <td><?php echo $quantity; ?></td>
                                                <td>Rs.<?php echo $row['item_price']; ?></td>
                                                <td>Rs.<?php echo $total_gross; ?></td>
                                                <td>Rs.<?php echo $total_profit; ?></td>
                                                <td>Rs.<?php echo $total; ?></td>
                                            </tr>

                                        <?php
                                            // total earnings of the seller
                                            $show_total += $total;
                                            $show_total_profit += $total_profit;
                                            $show_total_gross += $total_gross;
                                            // end of loop to show listed items
                                        }
                                        ?>
                                        <!-- total payable to the seller -->
                                        <tr>
                                            <td colspan="7"><strong>Total</strong></td>
                                            <td>Rs.<?php echo $show_total_gross; ?></td>
                                            <td>Rs.<?php echo $show_total_profit; ?></td>
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


    <div class="padding-ws-index">
        <div class="row">
            <div class="col-auto">
                <div id="h2-ws-index">
                    <h2 class="anton-regular">Payment Records</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
        </div>
    </div>

</section>


<div class="container mt-4">
    <div class="payment-list-container" id="paymentListContainer">
        <ul class="list-unstyled" id="paymentList">
            <?php include 'backend/payment-records.php'; // Load initial records 
            ?>
        </ul>
    </div>

</div>

<script>


    document.addEventListener("DOMContentLoaded", function () {
    const paymentList = document.getElementById("paymentList");
    const container = document.getElementById("paymentListContainer");
    let offset = 10; // Start after the initial 10 items
    const itemsPerLoad = 10;
    let loading = false;

    function loadMoreItems() {
        if (loading) return;
        loading = true;

        fetch(`./backend/payment-records.php?offset=${offset}&limit=${itemsPerLoad}`)
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "") {
                    container.removeEventListener("scroll", onScroll); // Stop further requests
                } else {
                    paymentList.insertAdjacentHTML("beforeend", data);
                    offset += itemsPerLoad;
                    loading = false;
                }
            })
            .catch(error => console.error("Error loading more items:", error));
    }

    function onScroll() {
        if (container.scrollTop + container.clientHeight >= container.scrollHeight - 5) {
            loadMoreItems();
        }
    }

    container.addEventListener("scroll", onScroll);
});



    function showImage(id) {
        var imageSrc = document.getElementById("thumbnail" + id).src;
        document.getElementById("fullscreenImage").src = imageSrc;
        document.getElementById("fullscreenOverlay").style.display = "block";
    }

    function closeImage() {
        document.getElementById("fullscreenOverlay").style.display = "none";
    }
    // document.addEventListener("DOMContentLoaded", function() {
    //     let lazyCards = document.querySelectorAll(".lazy-load");

    //     let observer = new IntersectionObserver((entries, observer) => {
    //         entries.forEach(entry => {
    //             if (entry.isIntersecting) {
    //                 let card = entry.target;
    //                 card.style.backgroundImage = `url(${card.dataset.bg})`;
    //                 card.classList.add("loaded");
    //                 observer.unobserve(card);
    //             }
    //         });
    //     });

    //     lazyCards.forEach(card => observer.observe(card));
    // });

    document.addEventListener("DOMContentLoaded", function() {
        let lazyCards = document.querySelectorAll(".lazy-load");

        let observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setTimeout(() => { // Delay before loading
                        let card = entry.target;
                        card.style.backgroundImage = `url(${card.dataset.bg})`;
                        card.classList.add("loaded");
                        observer.unobserve(card);
                    }, Math.random() * 1000 + 2000); // Random delay between 2-3 seconds
                }
            });
        });

        lazyCards.forEach(card => observer.observe(card));
    });

    // Payment Record Overlay Functions
    function showPaymentRecord(data) {
        document.getElementById('paymentRecordImage').src = data.image;
        document.getElementById('paymentAmount').textContent = 'Rs.' + data.amount;
        document.getElementById('paymentDate').textContent = new Date(data.date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('paymentStatus').textContent = 'Paid';
        document.getElementById('paymentRecordOverlay').style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closePaymentRecord() {
        document.getElementById('paymentRecordOverlay').style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }

    // Close overlay when clicking outside the modal
    document.getElementById('paymentRecordOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentRecord();
        }
    });

    // Close overlay with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePaymentRecord();
        }
    });
</script>

<?php // requiring the footer of page
require_once("includes/footer.php");
?>