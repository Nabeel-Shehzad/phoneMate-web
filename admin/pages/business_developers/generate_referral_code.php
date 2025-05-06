<?php
include_once('../../../includes/init.php');

// Function to generate a unique referral code
function generateReferralCode($length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return 'BD' . $code;
}

// Initialize database connection
$db = new DB();
$con = $db->conn;

// Generate a new code
$newCode = generateReferralCode();

// Check if the generated code already exists
$checkSql = "SELECT bd_id FROM business_developer WHERE bd_referal_code = '$newCode'";
$checkResult = $con->query($checkSql);

// If code exists, generate a new one until we find a unique code
while ($checkResult && $checkResult->num_rows > 0) {
    $newCode = generateReferralCode();
    $checkSql = "SELECT bd_id FROM business_developer WHERE bd_referal_code = '$newCode'";
    $checkResult = $con->query($checkSql);
}

// Return the unique code
echo json_encode(['success' => true, 'code' => $newCode]);
?>
