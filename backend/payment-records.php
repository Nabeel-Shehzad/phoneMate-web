<?php

error_reporting(0);
ini_set('display_errors', 0);

$id = escape($_SESSION['ws_id']);
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

$query = "SELECT * FROM ws_payment_records WHERE fk_ws_id='$id' ORDER BY wsr_id DESC LIMIT $offset, $limit";
$result = query($query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <li class="alert alert-success payment-li-item" onclick="showPaymentRecord(<?php echo htmlspecialchars(json_encode([
            'id' => $row['wsr_id'],
            'amount' => $row['wsr_paid'],
            'date' => $row['date'],
            'image' => 'uploads/ws-profile/' . $row['wsr_image']
        ])); ?>)">
            <div class="payment-li-left">
                <img class="payment-li-img" src="uploads/ws-profile/<?php echo $row['wsr_image']; ?>" alt="Receipt">
                <span class="payment-li-amount">Rs.<?php echo $row['wsr_paid']; ?></span>
            </div>
            <span class="payment-li-date">Paid on: <?php echo date('F j, Y', strtotime($row['date'])); ?></span>
        </li>
        <?php
    }
} 
else {
    echo "<li class='alert alert-warning payment-li-item'>No payments</li>";
}
// $db->close();
