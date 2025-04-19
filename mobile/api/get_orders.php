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
if (!isset($data['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User ID is required'
    ]);
    exit;
}

// Extract user ID
$user_id = mysqli_real_escape_string($conn, $data['user_id']);

try {
    // Get orders for this user - added tracking field and pieces_per_unit to the SELECT statement
    $query = "SELECT 
                s.sell_id, 
                s.sell_quantity, 
                s.sell_price, 
                s.sell_date, 
                LOWER(s.sell_status) as sell_status,
                s.tracking,
                i.item_id as product_id, 
                CONCAT(i.item_brand, ' ', i.item_number) as item_name, 
                CONCAT('https://codesmine.net/phonemate/uploads/item-images/', i.item_image) as item_image,
                i.item_category as item_category,
                COALESCE(a.pieces_pu, 1) as pieces_per_unit
            FROM 
                items_sold s
            JOIN 
                items i ON s.fk_item_id = i.item_id
            LEFT JOIN 
                item_adjustment a ON i.item_id = a.fk_item_id
            WHERE 
                s.fk_buyer_id = '$user_id'
            ORDER BY 
                s.tracking, s.sell_date DESC";

    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($result) > 0) {
        // Orders array
        $orders = [];
        
        // Retrieve table contents
        while ($row = mysqli_fetch_assoc($result)) {
            // Format the order data
            $order_item = [
                'sell_id' => $row['sell_id'],
                'sell_quantity' => $row['sell_quantity'],
                'sell_price' => $row['sell_price'],
                'sell_date' => $row['sell_date'],
                'sell_status' => $row['sell_status'],
                'tracking' => $row['tracking'], // Add tracking ID to the response
                'pieces_per_unit' => $row['pieces_per_unit'], // Get actual pieces per unit from database
                'total_pieces' => $row['sell_quantity'] * $row['pieces_per_unit'], // Calculate total pieces
                'product_id' => $row['product_id'],
                'item_name' => $row['item_name'],
                'item_image' => $row['item_image'],
                'item_category' => $row['item_category']
            ];
            
            $orders[] = $order_item;
        }
        
        // Return the orders
        echo json_encode([
            'status' => 'success',
            'message' => 'Orders found',
            'orders' => $orders
        ]);
    } else {
        // No orders found
        echo json_encode([
            'status' => 'success',
            'message' => 'No orders found',
            'orders' => []
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    // Close the database connection
    mysqli_close($conn);
}
?>
