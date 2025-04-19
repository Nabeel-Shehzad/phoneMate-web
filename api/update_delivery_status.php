<?php
// Database connection
require_once('../db/config.php');
require_once('../db/connection.php');

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, X-Requested-With');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Only POST requests are allowed']);
    exit;
}

// Get JSON data from request body
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is valid
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request data']);
    exit;
}

// Required fields validation
$required_fields = ['tracking_id', 'rider_id', 'status_type'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode(['status' => 'error', 'message' => "$field is required"]);
        exit;
    }
}

// Extract data from request
$tracking_id = mysqli_real_escape_string($conn, $data['tracking_id']);
$rider_id = (int)$data['rider_id'];
$status_type = mysqli_real_escape_string($conn, $data['status_type']);
$amount_collected = isset($data['amount_collected']) ? (float)$data['amount_collected'] : 0;
$amount_due = isset($data['amount_due']) ? (float)$data['amount_due'] : 0;
$returned_items = isset($data['returned_items']) ? json_encode($data['returned_items']) : null;
$rejection_reason = isset($data['rejection_reason']) ? mysqli_real_escape_string($conn, $data['rejection_reason']) : null;
$notes = isset($data['notes']) ? mysqli_real_escape_string($conn, $data['notes']) : null;
$item_statuses = isset($data['item_statuses']) ? $data['item_statuses'] : [];

// Validate status type
$valid_status_types = ['delivered_full', 'delivered_partial', 'rejected', 'payment_due'];
if (!in_array($status_type, $valid_status_types)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status type']);
    exit;
}

// Start a transaction
mysqli_begin_transaction($conn);

try {
    // Insert the main order delivery status record
    $insert_status_query = "INSERT INTO order_delivery_status 
        (tracking_id, status_type, amount_collected, amount_due, returned_items, rejection_reason, notes, fk_rider_id) 
        VALUES ('$tracking_id', '$status_type', $amount_collected, $amount_due, " . 
        ($returned_items ? "'$returned_items'" : "NULL") . ", " . 
        ($rejection_reason ? "'$rejection_reason'" : "NULL") . ", " . 
        ($notes ? "'$notes'" : "NULL") . ", $rider_id)";
    
    if (!mysqli_query($conn, $insert_status_query)) {
        throw new Exception("Error inserting order status: " . mysqli_error($conn));
    }
    
    $status_id = mysqli_insert_id($conn);
    
    // Process individual item statuses if provided
    if (!empty($item_statuses)) {
        foreach ($item_statuses as $item) {
            if (!isset($item['sell_id']) || !isset($item['item_status'])) {
                continue; // Skip invalid entries
            }
            
            $sell_id = (int)$item['sell_id'];
            $item_status = mysqli_real_escape_string($conn, $item['item_status']);
            $returned_quantity = isset($item['returned_quantity']) ? (int)$item['returned_quantity'] : 0;
            $delivered_quantity = isset($item['delivered_quantity']) ? (int)$item['delivered_quantity'] : 0;
            
            $insert_item_status_query = "INSERT INTO order_item_status 
                (fk_status_id, fk_sell_id, item_status, returned_quantity, delivered_quantity) 
                VALUES ($status_id, $sell_id, '$item_status', $returned_quantity, $delivered_quantity)";
                
            if (!mysqli_query($conn, $insert_item_status_query)) {
                throw new Exception("Error inserting item status: " . mysqli_error($conn));
            }
            
            // If there are returned items, update the item quantity in inventory
            if ($returned_quantity > 0) {
                // Get the item_id for this sell_id
                $get_item_id_query = "SELECT fk_item_id, sell_quantity FROM items_sold WHERE sell_id = $sell_id";
                $item_result = mysqli_query($conn, $get_item_id_query);
                
                if ($item_result && $item_data = mysqli_fetch_assoc($item_result)) {
                    $item_id = (int)$item_data['fk_item_id'];
                    $original_quantity = (int)$item_data['sell_quantity'];
                    
                    // Cap returned quantity to what was originally ordered
                    if ($returned_quantity > $original_quantity) {
                        $returned_quantity = $original_quantity;
                    }
                    
                    // Update item quantity - increase it by the returned amount
                    $update_inventory_query = "UPDATE items SET 
                        item_quantity = item_quantity + $returned_quantity,
                        item_sold = item_sold - $returned_quantity
                        WHERE item_id = $item_id";
                        
                    if (!mysqli_query($conn, $update_inventory_query)) {
                        throw new Exception("Error updating inventory for returned items: " . mysqli_error($conn));
                    }
                }
            }
        }
    }
    
    // Update the status in the items_sold table for all items in this tracking ID
    $update_status_query = "UPDATE items_sold SET sell_status = ";
    
    switch ($status_type) {
        case 'delivered_full':
            $new_status = "'completed'"; // Changed from 'delivered' to 'completed'
            break;
        case 'delivered_partial':
            $new_status = "'partially_delivered'";
            break;
        case 'rejected':
            $new_status = "'rejected'";
            break;
        case 'payment_due':
            $new_status = "'pay_with_dues'"; // Changed from 'delivered_with_dues' to 'pay_with_dues'
            break;
        default:
            $new_status = "'pending'";
    }
    
    $update_status_query .= "$new_status WHERE tracking = '$tracking_id'";
    
    if (!mysqli_query($conn, $update_status_query)) {
        throw new Exception("Error updating item sold status: " . mysqli_error($conn));
    }
    
    // Update the order_rider_assignment table
    $update_assignment_query = "UPDATE order_rider_assignment SET 
        assignment_status = 'completed' 
        WHERE tracking_id = '$tracking_id' AND fk_rider_id = $rider_id";
        
    if (!mysqli_query($conn, $update_assignment_query)) {
        throw new Exception("Error updating assignment status: " . mysqli_error($conn));
    }
    
    // For successful deliveries (full or partial with dues), handle financial aspects
    if ($status_type == 'delivered_full' || $status_type == 'payment_due' || $status_type == 'delivered_partial') {
        // Get all the items in this order for financial calculations
        $order_query = "SELECT 
                is1.*, 
                i.item_price, 
                i.item_profit,
                b.buyer_id,
                it.fk_ws_id as ws_id
            FROM 
                items_sold is1 
            JOIN 
                items i ON is1.fk_item_id = i.item_id
            JOIN 
                buyer b ON is1.fk_buyer_id = b.buyer_id
            JOIN 
                item_tracking it ON i.fk_item_tracking_id = it.item_tracking_id
            WHERE 
                is1.tracking = '$tracking_id'";
        
        $order_result = mysqli_query($conn, $order_query);
        
        if (!$order_result) {
            throw new Exception("Error getting order details: " . mysqli_error($conn));
        }
        
        // Process each item in the order
        while ($order_item = mysqli_fetch_assoc($order_result)) {
            $item_id = $order_item['fk_item_id'];
            $buyer_id = $order_item['buyer_id'];
            $ws_id = $order_item['ws_id'];
            $sell_quantity = $order_item['sell_quantity'];
            $sell_price = $order_item['sell_price'];
            
            // Calculate buying price
            $buying_price = (($order_item['item_price'] / 100) * $order_item['item_profit']) + $order_item['item_price'];
            
            // If this is a partial delivery, adjust quantities based on what was actually delivered
            if ($status_type == 'delivered_partial') {
                // Find the specific item status record
                $find_item_status = "SELECT 
                        delivered_quantity 
                    FROM 
                        order_item_status 
                    WHERE 
                        fk_status_id = $status_id AND 
                        fk_sell_id = {$order_item['sell_id']}";
                        
                $item_status_result = mysqli_query($conn, $find_item_status);
                
                if ($item_status_result && $item_status_data = mysqli_fetch_assoc($item_status_result)) {
                    $sell_quantity = (int)$item_status_data['delivered_quantity'];
                }
            }
            
            // Skip financial processing for zero quantity
            if ($sell_quantity <= 0) {
                continue;
            }
            
            // For fully delivered items, create wholesaler pending payments
            if ($status_type == 'delivered_full' || ($status_type == 'delivered_partial' && $sell_quantity > 0)) {
                // Total amount for wholesaler
                $ws_payment_amount = $buying_price * $sell_quantity;
                
                // Insert into wholesaler pending payments
                $insert_ws_payment = "INSERT INTO ws_pending_payments 
                    (wspp_amount, fk_ws_id) 
                    VALUES ($ws_payment_amount, $ws_id)";
                    
                if (!mysqli_query($conn, $insert_ws_payment)) {
                    throw new Exception("Error creating wholesaler pending payment: " . mysqli_error($conn));
                }
                
                // Create notification for wholesaler
                $insert_ws_notification = "INSERT INTO ws_notification 
                    (message, date, fk_ws_id) 
                    VALUES ('Your listed items have been sold, check Sold Items!', CURDATE(), $ws_id)";
                    
                if (!mysqli_query($conn, $insert_ws_notification)) {
                    throw new Exception("Error creating wholesaler notification: " . mysqli_error($conn));
                }
                
                // Calculate and record company earnings
                $total_selling_price = $sell_price * $sell_quantity;
                $total_buying_price = $buying_price * $sell_quantity;
                $earnings = $total_selling_price - $total_buying_price;
                
                // Get the delivery_id for this item
                $get_delivery_query = "SELECT d.delivery_id 
                    FROM delivery d 
                    JOIN item_adjustment ia ON d.fk_item_adj_id = ia.item_adj_id
                    WHERE ia.fk_item_id = $item_id AND d.fk_buyer_id = $buyer_id
                    ORDER BY d.delivery_id DESC 
                    LIMIT 1";
                    
                $delivery_result = mysqli_query($conn, $get_delivery_query);
                
                if ($delivery_result && $delivery_data = mysqli_fetch_assoc($delivery_result)) {
                    $delivery_id = $delivery_data['delivery_id'];
                    
                    // Insert company earnings
                    $insert_earnings = "INSERT INTO company_earnings 
                        (earning_amount, date, fk_delivery_id) 
                        VALUES ($earnings, CURDATE(), $delivery_id)";
                        
                    if (!mysqli_query($conn, $insert_earnings)) {
                        throw new Exception("Error recording company earnings: " . mysqli_error($conn));
                    }
                }
            }
        }
        
        // Create notification for buyer
        $buyer_notification_message = '';
        switch ($status_type) {
            case 'delivered_full':
                $buyer_notification_message = 'Your order has been delivered successfully!';
                break;
            case 'delivered_partial':
                $buyer_notification_message = 'Your order has been partially delivered with some items returned.';
                break;
            case 'payment_due':
                $buyer_notification_message = 'Your order has been delivered with payment due. Please complete the payment.';
                break;
        }
        
        if (!empty($buyer_notification_message)) {
            // Get the buyer ID from the order
            $get_buyer_query = "SELECT DISTINCT fk_buyer_id 
                FROM items_sold 
                WHERE tracking = '$tracking_id' 
                LIMIT 1";
                
            $buyer_result = mysqli_query($conn, $get_buyer_query);
            
            if ($buyer_result && $buyer_data = mysqli_fetch_assoc($buyer_result)) {
                $buyer_id = $buyer_data['fk_buyer_id'];
                
                // Insert buyer notification
                $insert_buyer_notification = "INSERT INTO buyer_notification 
                    (message, date, fk_buyer_id) 
                    VALUES ('$buyer_notification_message', CURDATE(), $buyer_id)";
                    
                if (!mysqli_query($conn, $insert_buyer_notification)) {
                    throw new Exception("Error creating buyer notification: " . mysqli_error($conn));
                }
            }
        }
    }
    
    // For rejected orders, create a notification
    if ($status_type == 'rejected') {
        // Get the buyer ID
        $get_buyer_query = "SELECT DISTINCT fk_buyer_id 
            FROM items_sold 
            WHERE tracking = '$tracking_id' 
            LIMIT 1";
            
        $buyer_result = mysqli_query($conn, $get_buyer_query);
        
        if ($buyer_result && $buyer_data = mysqli_fetch_assoc($buyer_result)) {
            $buyer_id = $buyer_data['fk_buyer_id'];
            
            // Insert buyer notification for rejection
            $reject_reason = !empty($rejection_reason) ? " Reason: $rejection_reason" : "";
            $insert_buyer_notification = "INSERT INTO buyer_notification 
                (message, date, fk_buyer_id) 
                VALUES ('Your order was rejected.$reject_reason', CURDATE(), $buyer_id)";
                
            if (!mysqli_query($conn, $insert_buyer_notification)) {
                throw new Exception("Error creating buyer rejection notification: " . mysqli_error($conn));
            }
            
            // Also notify admin about the rejected order
            $admin_notification = "INSERT INTO admin_notification 
                (message, date, admin_role, fk_wh_id) 
                VALUES ('Order $tracking_id was rejected by customer.$reject_reason', CURDATE(), 'all', 1)";
                
            if (!mysqli_query($conn, $admin_notification)) {
                throw new Exception("Error creating admin rejection notification: " . mysqli_error($conn));
            }
        }
    }
    
    // Commit the transaction
    mysqli_commit($conn);
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Order status updated successfully',
        'status_id' => $status_id
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}

// Close the database connection
mysqli_close($conn);
?>