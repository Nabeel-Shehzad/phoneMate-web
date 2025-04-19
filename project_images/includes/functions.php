<?php
// all general functions for easier use

// escape the string
function escape($string) {
    global $db;
    return mysqli_real_escape_string($db->conn, $string);
}

// pass the query
function query($query) {
    global $db;
    return mysqli_query($db->conn, $query);
}

// redirecting the user to other page
function redirect($location) {
    header("Location: " . $location . "");
}

// getting the last interaction id with database
function last_id () {
    global $db;
    return mysqli_insert_id($db->conn);
}

?>