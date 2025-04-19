<?php
// Include database configuration and connection
require_once '../db/config.php';
require_once '../db/connection.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    $query = "SELECT DISTINCT item_category FROM items";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Error fetching categories: " . mysqli_error($conn));
    }
    
    $categories = array();
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['item_category'])) {
            $categories[] = array('title' => $row['item_category']);
        }
    }
    
    // Add 'All' category at the beginning
    array_unshift($categories, array('title' => 'All'));
    
    echo json_encode(array(
        'status' => 'success',
        'categories' => $categories
    ));
    
} catch (Exception $e) {
    echo json_encode(array(
        'status' => 'error',
        'message' => $e->getMessage()
    ));
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>
