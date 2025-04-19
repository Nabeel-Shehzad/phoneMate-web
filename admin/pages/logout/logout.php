<?php
require_once("../../../includes/init.php");

if (!isset($_GET['logout'])) {
    redirect("../");
}

if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    logout('yes');
    redirect("../");
}
