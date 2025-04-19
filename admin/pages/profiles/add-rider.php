<?php
// the header/navbar/sidebar
require_once("../../includes/nav-side.php");

// Create database connection using the DB class from init.php
$db = new DB();
$conn = $db->conn;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $rider_name = mysqli_real_escape_string($conn, $_POST['rider_name']);
    $rider_address = mysqli_real_escape_string($conn, $_POST['rider_address']);
    $rider_contact = mysqli_real_escape_string($conn, $_POST['rider_contact']);
    $rider_cnic = mysqli_real_escape_string($conn, $_POST['rider_cnic']);
    $rider_email = mysqli_real_escape_string($conn, $_POST['rider_email']);
    $rider_password = md5($_POST['rider_password']); // Basic MD5 hashing
    $rider_status = mysqli_real_escape_string($conn, $_POST['rider_status']);
    
    // Handle image upload
    $rider_image = 'default.jpg'; // Default image
    
    if (isset($_FILES['rider_image']) && $_FILES['rider_image']['error'] == 0) {
        $target_dir = "../../../uploads/rider-profile/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["rider_image"]["name"], PATHINFO_EXTENSION);
        $new_file_name = "rider_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_file_name;
        
        // Upload file
        if (move_uploaded_file($_FILES["rider_image"]["tmp_name"], $target_file)) {
            $rider_image = $new_file_name;
        }
    }
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO rider (rider_name, rider_address, rider_contact, rider_cnic, rider_image, rider_email, rider_password, rider_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $rider_name, $rider_address, $rider_contact, $rider_cnic, $rider_image, $rider_email, $rider_password, $rider_status);
    
    if ($stmt->execute()) {
        // Add notification for admin
        $notification_message = "New rider registration: " . $rider_name;
        $today = date("Y-m-d");
        $admin_role = "admin";
        $wh_id = 1; // Default warehouse ID
        
        $notification_stmt = $conn->prepare("INSERT INTO admin_notification (message, date, admin_role, fk_wh_id) VALUES (?, ?, ?, ?)");
        $notification_stmt->bind_param("sssi", $notification_message, $today, $admin_role, $wh_id);
        $notification_stmt->execute();
        
        echo "<script>alert('Rider added successfully!'); window.location.href='riders.php';</script>";
    } else {
        echo "<script>alert('Error adding rider: " . $stmt->error . "');</script>";
    }
}
?>

<!-- Add Rider Form -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">Add New Rider</h6>
                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rider_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="rider_name" name="rider_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="rider_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="rider_email" name="rider_email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rider_contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="rider_contact" name="rider_contact" required>
                        </div>
                        <div class="col-md-6">
                            <label for="rider_cnic" class="form-label">CNIC</label>
                            <input type="text" class="form-control" id="rider_cnic" name="rider_cnic" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="rider_address" class="form-label">Address</label>
                        <textarea class="form-control" id="rider_address" name="rider_address" rows="3" required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rider_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="rider_password" name="rider_password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="rider_image" class="form-label">Profile Image</label>
                            <input class="form-control bg-dark" type="file" id="rider_image" name="rider_image">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="rider_status" class="form-label">Status</label>
                        <select class="form-select" id="rider_status" name="rider_status" required>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Rider</button>
                    <a href="riders.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Add Rider Form End -->

<?php 
// including the footer
require_once("../../includes/footer.php");
?>
