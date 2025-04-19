<?php
// Include database configuration and connection
require_once '../db/config.php';
require_once '../db/connection.php';

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only POST method is allowed'
    ]);
    exit;
}

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['buyer_id']) || !isset($data['items']) || empty($data['items'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields'
    ]);
    exit;
}

// Extract data
$buyer_id = mysqli_real_escape_string($conn, $data['buyer_id']);
$items = $data['items'];

// Begin transaction
mysqli_begin_transaction($conn);

// Generate a unique tracking ID for this order
$tracking_id = '';
$is_unique = false;

// Keep generating tracking IDs until we find a unique one
while (!$is_unique) {
    // Generate a random string of lowercase letters and numbers (10 characters)
    $tracking_id = '';
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $length = 6;
    
    for ($i = 0; $i < $length; $i++) {
        $tracking_id .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    // Check if this tracking ID already exists
    $check_tracking = "SELECT COUNT(*) as count FROM items_sold WHERE tracking = '$tracking_id'";
    $tracking_result = mysqli_query($conn, $check_tracking);
    
    if ($tracking_result) {
        $tracking_data = mysqli_fetch_assoc($tracking_result);
        if ($tracking_data['count'] == 0) {
            $is_unique = true; // This tracking ID is unique
        }
    }
}

try {
    $success = true;
    $errors = [];
    $inserted_ids = [];
    
    // Process each item
    foreach ($items as $item) {
        // Validate item data
        if (!isset($item['item_id']) || !isset($item['quantity']) || !isset($item['price'])) {
            $errors[] = "Invalid item data";
            $success = false;
            continue;
        }
        
        $item_id = mysqli_real_escape_string($conn, $item['item_id']);
        $quantity = (int)mysqli_real_escape_string($conn, $item['quantity']);
        $price = (int)mysqli_real_escape_string($conn, $item['price']);
        $current_date = date('Y-m-d');
        
        // Get pieces_per_unit from the request or default to 1
        $pieces_per_unit = isset($item['pieces_per_unit']) ? (int)$item['pieces_per_unit'] : 1;
        
        // Calculate total pieces needed
        $total_pieces_needed = $quantity * $pieces_per_unit;
        
        // Note: The price sent from the app already includes the price per piece * pieces_per_unit
        // The total price is price * quantity (since price already accounts for pieces_per_unit)
        $total_price = $price * $quantity;
        
        // Check if item exists and has enough quantity
        $check_query = "SELECT item_quantity FROM items WHERE item_id = '$item_id'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (!$check_result || mysqli_num_rows($check_result) == 0) {
            $errors[] = "Item with ID $item_id not found";
            $success = false;
            continue;
        }
        
        $item_data = mysqli_fetch_assoc($check_result);
        $available_quantity = $item_data['item_quantity'];
        
        // Check if enough total pieces are available
        if ($available_quantity < $total_pieces_needed) {
            $errors[] = "Not enough quantity available for item ID $item_id. Available: $available_quantity, Requested: $total_pieces_needed (Quantity: $quantity × Pieces per unit: $pieces_per_unit)";
            $success = false;
            continue;
        }
        
        // Insert into items_sold table with the tracking ID
        $insert_query = "INSERT INTO items_sold (fk_buyer_id, fk_item_id, sell_quantity, sell_price, sell_date, sell_status, tracking) 
                         VALUES ('$buyer_id', '$item_id', '$quantity', '$total_price', '$current_date', 'pending', '$tracking_id')";
        
        $insert_result = mysqli_query($conn, $insert_query);
        
        if (!$insert_result) {
            $errors[] = "Failed to insert item ID $item_id: " . mysqli_error($conn);
            $success = false;
            continue;
        }
        
        $inserted_id = mysqli_insert_id($conn);
        $inserted_ids[] = $inserted_id;
    
    }
    
    // If successful, update the item quantities
    if ($success) {
        // Update quantities for each item
        foreach ($items as $item) {
            $item_id = mysqli_real_escape_string($conn, $item['item_id']);
            $quantity = (int)mysqli_real_escape_string($conn, $item['quantity']);
            $pieces_per_unit = isset($item['pieces_per_unit']) ? (int)$item['pieces_per_unit'] : 1;
            $total_pieces = $quantity * $pieces_per_unit;
            
            // Update the item quantity in the database
            $update_query = "UPDATE items SET item_quantity = item_quantity - $total_pieces WHERE item_id = '$item_id'";
            $update_result = mysqli_query($conn, $update_query);
            
            if (!$update_result) {
                // Log the error but don't fail the transaction at this point
                error_log("Failed to update quantity for item ID $item_id: " . mysqli_error($conn));
            }
        }
        
        mysqli_commit($conn);
        echo json_encode([
            'status' => 'success',
            'message' => 'Checkout completed successfully',
            'order_ids' => $inserted_ids,
            'tracking_id' => $tracking_id
        ]);
    } else {
        mysqli_rollback($conn);
        
        // Create a more detailed error message from the errors array
        $detailed_message = 'Checkout failed: ';
        if (!empty($errors)) {
            // Use the first error as the main message
            $detailed_message .= $errors[0];
            
            // If there are multiple errors, add them as a list
            if (count($errors) > 1) {
                $detailed_message .= '. Additional issues: ';
                $additional_errors = array_slice($errors, 1);
                $detailed_message .= implode('; ', $additional_errors);
            }
        } else {
            $detailed_message .= 'Unknown error occurred';
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => $detailed_message,
            'errors' => $errors
        ]);
    }
    
} catch (Exception $e) {
    // Rollback transaction on exception
    mysqli_rollback($conn);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    // Close the database connection
    mysqli_close($conn);
}
?>
