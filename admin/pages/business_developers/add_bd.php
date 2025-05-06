<?php include_once('../../../includes/init.php'); ?>
<?php include_once('../../includes/nav-side.php'); ?>

<?php
// Get database connection properly initialized
$db = new DB();
$con = $db->conn;

// Function to generate a unique referral code
function generateReferralCode($length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return 'BD' . $code;
}

// Process form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $bd_name = mysqli_real_escape_string($con, $_POST['bd_name']);
    $bd_address = mysqli_real_escape_string($con, $_POST['bd_address']);
    $bd_contact = mysqli_real_escape_string($con, $_POST['bd_contact']);
    $bd_cnic = mysqli_real_escape_string($con, $_POST['bd_cnic']);
    $bd_email = mysqli_real_escape_string($con, $_POST['bd_email']);
    $bd_password = mysqli_real_escape_string($con, $_POST['bd_password']);
    $bd_status = mysqli_real_escape_string($con, $_POST['bd_status']);
    
    // Generate or use provided referral code
    $bd_referal_code = !empty($_POST['bd_referal_code']) ? 
                        mysqli_real_escape_string($con, $_POST['bd_referal_code']) : 
                        generateReferralCode();
    
    // Check if email already exists
    $checkEmailSql = "SELECT bd_id FROM business_developer WHERE bd_email = '$bd_email'";
    $checkEmailResult = $con->query($checkEmailSql);
    
    if ($checkEmailResult && $checkEmailResult->num_rows > 0) {
        $message = "Email address already in use by another business developer.";
        $messageType = "danger";
    } else {
        // Check if referral code already exists
        $checkCodeSql = "SELECT bd_id FROM business_developer WHERE bd_referal_code = '$bd_referal_code'";
        $checkCodeResult = $con->query($checkCodeSql);
        
        if ($checkCodeResult && $checkCodeResult->num_rows > 0) {
            $message = "Referral code already in use. Please try another code.";
            $messageType = "danger";
        } else {
            // Handle image upload
            $bd_image = 'default.jpg'; // Default image
            
            if (isset($_FILES['bd_image']) && $_FILES['bd_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../../../uploads/bd-images/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = basename($_FILES['bd_image']['name']);
                $targetFilePath = $uploadDir . $fileName;
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                
                // Allow certain file formats
                $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
                if (in_array(strtolower($fileType), $allowTypes)) {
                    // Upload file to the server
                    if (move_uploaded_file($_FILES['bd_image']['tmp_name'], $targetFilePath)) {
                        $bd_image = $fileName;
                    } else {
                        $message = "Sorry, there was an error uploading your file.";
                        $messageType = "danger";
                    }
                } else {
                    $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $messageType = "danger";
                }
            }
            
            // If no errors, insert into database
            if (empty($message)) {
                // Hash the password
                $hashed_password = password_hash($bd_password, PASSWORD_DEFAULT);
                
                $insertSql = "INSERT INTO business_developer (bd_name, bd_address, bd_contact, bd_cnic, bd_image, bd_email, bd_password, bd_status, bd_referal_code) 
                              VALUES ('$bd_name', '$bd_address', '$bd_contact', '$bd_cnic', '$bd_image', '$bd_email', '$hashed_password', '$bd_status', '$bd_referal_code')";
                
                if ($con->query($insertSql)) {
                    $message = "Business Developer registered successfully with referral code: $bd_referal_code";
                    $messageType = "success";
                    
                    // Create notification for the new BD
                    $bd_id = $con->insert_id;
                    $notificationMessage = "Welcome to PhoneMate! Your account has been created successfully.";
                    $date = date('Y-m-d');
                    
                    $notificationSql = "INSERT INTO bd_notification (message, date, is_read, fk_bd_id) 
                                       VALUES ('$notificationMessage', '$date', 'unread', '$bd_id')";
                    $con->query($notificationSql);
                    
                    // Create admin notification
                    $adminNotificationMessage = "New Business Developer registered: $bd_name (Referral Code: $bd_referal_code)";
                    $warehouseId = isset($_SESSION['fk_wh_id']) ? $_SESSION['fk_wh_id'] : 1;
                    
                    $adminNotificationSql = "INSERT INTO admin_notification (message, date, is_read, admin_role, fk_wh_id) 
                                           VALUES ('$adminNotificationMessage', '$date', 'unread', 'super_admin', '$warehouseId')";
                    $con->query($adminNotificationSql);
                    
                    // Reset form
                    $_POST = array();
                } else {
                    $message = "Error: " . $con->error;
                    $messageType = "danger";
                }
            }
        }
    }
}

// Generate a new referral code for the form
$suggestedReferralCode = generateReferralCode();
?>

<!-- Add Business Developer Form -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">Register New Business Developer</h6>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bd_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="bd_name" name="bd_name" required
                                   value="<?= isset($_POST['bd_name']) ? htmlspecialchars($_POST['bd_name']) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="bd_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="bd_email" name="bd_email" required
                                   value="<?= isset($_POST['bd_email']) ? htmlspecialchars($_POST['bd_email']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bd_contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="bd_contact" name="bd_contact" required
                                   value="<?= isset($_POST['bd_contact']) ? htmlspecialchars($_POST['bd_contact']) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="bd_cnic" class="form-label">CNIC</label>
                            <input type="text" class="form-control" id="bd_cnic" name="bd_cnic" required
                                   value="<?= isset($_POST['bd_cnic']) ? htmlspecialchars($_POST['bd_cnic']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bd_address" class="form-label">Address</label>
                        <textarea class="form-control" id="bd_address" name="bd_address" rows="3" required><?= isset($_POST['bd_address']) ? htmlspecialchars($_POST['bd_address']) : '' ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bd_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="bd_password" name="bd_password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bd_confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="bd_confirm_password" name="bd_confirm_password" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bd_referal_code" class="form-label">Referral Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="bd_referal_code" name="bd_referal_code" 
                                       value="<?= isset($_POST['bd_referal_code']) ? htmlspecialchars($_POST['bd_referal_code']) : $suggestedReferralCode ?>">
                                <button class="btn btn-primary" type="button" id="generateCode">Generate New</button>
                            </div>
                            <small class="form-text text-muted">Leave as is for auto-generated code or enter a custom code.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="bd_status" class="form-label">Status</label>
                            <select class="form-select" id="bd_status" name="bd_status" required>
                                <option value="active" <?= (isset($_POST['bd_status']) && $_POST['bd_status'] == 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (isset($_POST['bd_status']) && $_POST['bd_status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bd_image" class="form-label">Profile Image</label>
                        <input class="form-control bg-dark" type="file" id="bd_image" name="bd_image">
                        <small class="form-text text-muted">Upload a profile image (optional). JPG, PNG, GIF formats only.</small>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Register Business Developer</button>
                            <a href="list_bds.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('../../includes/footer.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const form = document.querySelector('form');
    const passwordField = document.getElementById('bd_password');
    const confirmPasswordField = document.getElementById('bd_confirm_password');
    
    form.addEventListener('submit', function(event) {
        if (passwordField.value !== confirmPasswordField.value) {
            event.preventDefault();
            alert('Passwords do not match!');
            confirmPasswordField.focus();
        }
    });
    
    // Generate new referral code
    document.getElementById('generateCode').addEventListener('click', function() {
        fetch('generate_referral_code.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('bd_referal_code').value = data.code;
                }
            })
            .catch(error => console.error('Error:', error));
    });
});
</script>
