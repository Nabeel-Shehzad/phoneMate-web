<?php
// Include database configuration and connection
require_once '../db/config.php';
require_once '../db/connection.php';

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

try {
    // Check if category parameter is provided
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    
    // Log the category for debugging
    error_log("Category filter requested: " . ($category ? $category : "None"));
    
    // Get processed items from the items table with adjustment details
    $items_query = "SELECT i.*, ia.item_tag, ia.item_adj_price, ia.pieces_pu 
                   FROM items i 
                   LEFT JOIN item_adjustment ia ON i.item_id = ia.fk_item_id 
                   WHERE i.item_status = 'processed'";
    
    // Add category filter if provided and not 'All'
    if ($category && $category !== 'All') {
        $category = mysqli_real_escape_string($conn, $category);
        $items_query .= " AND i.item_category LIKE '%$category%'";
        error_log("SQL query with category filter: " . $items_query);
    } else {
        error_log("SQL query without category filter: " . $items_query);
    }
    
    $result = mysqli_query($conn, $items_query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }
    
    $normal_products = [];
    $featured_products = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Use item_adj_price if available, otherwise use item_price
        $selling_price = !empty($row['item_adj_price']) ? $row['item_adj_price'] : $row['item_price'];
        
        // Format the product data
        $product = [
            'id' => $row['item_id'],
            'name' => $row['item_brand'] . ' ' . $row['item_number'],
            'price' => $selling_price,
            'original_price' => $row['item_price'],
            'description' => $row['item_description'],
            'image' => 'https://codesmine.net/phonemate/uploads/item-images/' . $row['item_image'],
            'category' => $row['item_category'],
            'status' => $row['item_status'],
            'tag' => $row['item_tag'] ?? 'normal_selling', // Default to normal if no tag
            'pieces_per_unit' => $row['pieces_pu'] ?? 1,  // Default to 1 if not specified
            'quantity_available' => $row['item_quantity'] ?? 0  // Available quantity from items table
        ];
        
        // Categorize products based on their tag
        if (empty($row['item_tag']) || $row['item_tag'] == 'normal_selling') {
            $normal_products[] = $product;
        } else if ($row['item_tag'] == 'top_selling' || $row['item_tag'] == 'hot_selling') {
            $featured_products[] = $product;
        }
    }
    
    // Return the products
    echo json_encode([
        'status' => 'success',
        'normal_products' => $normal_products,
        'featured_products' => $featured_products
    ]);
    
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
