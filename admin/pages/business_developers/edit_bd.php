<?php include_once('../../../includes/init.php'); ?>
<?php include_once('../../includes/nav-side.php'); ?>

<?php
// Get database connection properly initialized
$db = new DB();
$con = $db->conn;

// Check if BD ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<script>alert("Invalid request: Missing BD ID"); window.location.href = "list_bds.php";</script>';
    exit;
}

$bd_id = mysqli_real_escape_string($con, $_GET['id']);

// Get BD information
$bdSql = "SELECT * FROM business_developer WHERE bd_id = '$bd_id'";
$bdResult = $con->query($bdSql);

if (!$bdResult || $bdResult->num_rows == 0) {
    echo '<script>alert("Business Developer not found"); window.location.href = "list_bds.php";</script>';
    exit;
}

$bd = $bdResult->fetch_assoc();

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
    $bd_status = mysqli_real_escape_string($con, $_POST['bd_status']);
    $bd_referal_code = mysqli_real_escape_string($con, $_POST['bd_referal_code']);
    
    // Check if email already exists for another BD
    $checkEmailSql = "SELECT bd_id FROM business_developer WHERE bd_email = '$bd_email' AND bd_id != '$bd_id'";
    $checkEmailResult = $con->query($checkEmailSql);
    
    if ($checkEmailResult && $checkEmailResult->num_rows > 0) {
        $message = "Email address already in use by another business developer.";
        $messageType = "danger";
    } else {
        // Check if referral code already exists for another BD
        $checkCodeSql = "SELECT bd_id FROM business_developer WHERE bd_referal_code = '$bd_referal_code' AND bd_id != '$bd_id'";
        $checkCodeResult = $con->query($checkCodeSql);
        
        if ($checkCodeResult && $checkCodeResult->num_rows > 0) {
            $message = "Referral code already in use. Please try another code.";
            $messageType = "danger";
        } else {
            // Handle image upload
            $bd_image = $bd['bd_image']; // Keep existing image by default
            
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
            
            // Handle password update
            $passwordSql = '';
            if (!empty($_POST['bd_password'])) {
                // Hash the new password
                $hashed_password = password_hash($_POST['bd_password'], PASSWORD_DEFAULT);
                $passwordSql = ", bd_password = '$hashed_password'";
            }
            
            // If no errors, update database
            if (empty($message)) {
                $updateSql = "UPDATE business_developer 
                              SET bd_name = '$bd_name', 
                                  bd_address = '$bd_address', 
                                  bd_contact = '$bd_contact', 
                                  bd_cnic = '$bd_cnic', 
                                  bd_image = '$bd_image', 
                                  bd_email = '$bd_email', 
                                  bd_status = '$bd_status', 
                                  bd_referal_code = '$bd_referal_code'
                                  $passwordSql
                              WHERE bd_id = '$bd_id'";
                
                if ($con->query($updateSql)) {
                    $message = "Business Developer updated successfully.";
                    $messageType = "success";
                    
                    // Create notification for the BD
                    $notificationMessage = "Your account information has been updated by the admin.";
                    $date = date('Y-m-d');
                    
                    $notificationSql = "INSERT INTO bd_notification (message, date, is_read, fk_bd_id) 
                                       VALUES ('$notificationMessage', '$date', 'unread', '$bd_id')";
                    $con->query($notificationSql);
                    
                    // Refresh BD data
                    $bdResult = $con->query($bdSql);
                    $bd = $bdResult->fetch_assoc();
                } else {
                    $message = "Error: " . $con->error;
                    $messageType = "danger";
                }
            }
        }
    }
}
?>

<!-- Edit Business Developer Form -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">Edit Business Developer</h6>
                
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
                                   value="<?= htmlspecialchars($bd['bd_name']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="bd_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="bd_email" name="bd_email" required
                                   value="<?= htmlspecialchars($bd['bd_email']) ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bd_contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="bd_contact" name="bd_contact" required
                                   value="<?= htmlspecialchars($bd['bd_contact']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="bd_cnic" class="form-label">CNIC</label>
                            <input type="text" class="form-control" id="bd_cnic" name="bd_cnic" required
                                   value="<?= htmlspecialchars($bd['bd_cnic']) ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bd_address" class="form-label">Address</label>
                        <textarea class="form-control" id="bd_address" name="bd_address" rows="3" required><?= htmlspecialchars($bd['bd_address']) ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bd_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="bd_password" name="bd_password">
                            <small class="form-text text-muted">Leave blank to keep current password</small>
                        </div>
                        <div class="col-md-6">
                            <label for="bd_confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="bd_confirm_password" name="bd_confirm_password">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bd_referal_code" class="form-label">Referral Code</label>
                            <input type="text" class="form-control" id="bd_referal_code" name="bd_referal_code" required
                                   value="<?= htmlspecialchars($bd['bd_referal_code']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="bd_status" class="form-label">Status</label>
                            <select class="form-select" id="bd_status" name="bd_status" required>
                                <option value="active" <?= $bd['bd_status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $bd['bd_status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bd_image" class="form-label">Profile Image</label>
                        <?php if (!empty($bd['bd_image']) && $bd['bd_image'] != 'default.jpg'): ?>
                            <div class="mb-2">
                                <img src="../../../uploads/bd-images/<?= htmlspecialchars($bd['bd_image']) ?>" 
                                     alt="Current Profile Image" style="max-width: 100px; height: auto;">
                                <p class="small">Current image: <?= htmlspecialchars($bd['bd_image']) ?></p>
                            </div>
                        <?php endif; ?>
                        <input class="form-control bg-dark" type="file" id="bd_image" name="bd_image">
                        <small class="form-text text-muted">Upload a new profile image (optional). JPG, PNG, GIF formats only.</small>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Business Developer</button>
                            <a href="bd_details.php?id=<?= $bd_id ?>" class="btn btn-secondary">Cancel</a>
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
        if (passwordField.value !== confirmPasswordField.value && passwordField.value !== '') {
            event.preventDefault();
            alert('Passwords do not match!');
            confirmPasswordField.focus();
        }
    });
});
</script>
