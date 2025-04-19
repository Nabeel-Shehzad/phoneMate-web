<?php // all the necessary files needed for our project

// session start
session_start();
// output buffering start
ob_start();

// path variable
const LOC = "http://localhost/phonemate/admin/pages";
// const LOC = "https://codesmine.net/phonemate/admin/pages";

// the connection configuration file
require_once(__DIR__ . "/../connection/configs.php");
// the database connection file
// require_once(__DIR__ . "/../connection/connection.php");
// the database connection class
require_once(__DIR__ . "/classes/db.php");
// the functions file
require_once(__DIR__ . "/functions.php");

// including all the classes
require_once(__DIR__ . "/classes/primary.php");
require_once(__DIR__ . "/classes/item_tracking.php");
require_once(__DIR__ . "/classes/item.php");
require_once(__DIR__ . "/classes/wholesaler.php");
require_once(__DIR__ . "/classes/item_rejection.php");
require_once(__DIR__ . "/classes/item_adjustment.php");
require_once(__DIR__ . "/classes/buyer.php");
require_once(__DIR__ . "/classes/items_sold.php");
require_once(__DIR__ . "/classes/delivery.php");
require_once(__DIR__ . "/classes/buyer_notification.php");
require_once(__DIR__ . "/classes/ws_pending_payments.php");
require_once(__DIR__ . "/classes/company_earnings.php");
require_once(__DIR__ . "/classes/ws_notification.php");
require_once(__DIR__ . "/classes/ws_payment_records.php");
require_once(__DIR__ . "/classes/ws_payment_details.php");

?>