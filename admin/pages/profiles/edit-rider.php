<?php
// the header/navbar/sidebar
require_once("../../includes/nav-side.php");

// Create database connection
$db = new DB();
$conn = $db->conn;

// Check if rider ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Rider ID not provided!'); window.location.href='riders.php';</script>";
    exit;
}

$rider_id = intval($_GET['id']);

// Process form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $rider_name = mysqli_real_escape_string($conn, $_POST['rider_name']);
    $rider_address = mysqli_real_escape_string($conn, $_POST['rider_address']);
    $rider_contact = mysqli_real_escape_string($conn, $_POST['rider_contact']);
    $rider_cnic = mysqli_real_escape_string($conn, $_POST['rider_cnic']);
    $rider_email = mysqli_real_escape_string($conn, $_POST['rider_email']);
    $rider_status = mysqli_real_escape_string($conn, $_POST['rider_status']);
    
    // Update SQL query parts
    $sql_parts = array(
        "rider_name = ?",
        "rider_address = ?",
        "rider_contact = ?",
        "rider_cnic = ?",
        "rider_email = ?",
        "rider_status = ?"
    );
    
    // Parameters for prepared statement
    $param_types = "ssssss";
    $param_values = array($rider_name, $rider_address, $rider_contact, $rider_cnic, $rider_email, $rider_status);
    
    // Handle password update only if provided
    if (!empty($_POST['rider_password'])) {
        $rider_password = md5($_POST['rider_password']);
        $sql_parts[] = "rider_password = ?";
        $param_types .= "s";
        $param_values[] = $rider_password;
    }
    
    // Handle image upload
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
            $sql_parts[] = "rider_image = ?";
            $param_types .= "s";
            $param_values[] = $new_file_name;
        }
    }
    
    // Build the SQL query
    $sql = "UPDATE rider SET " . implode(", ", $sql_parts) . " WHERE rider_id = ?";
    $param_types .= "i";
    $param_values[] = $rider_id;
    
    // Prepare and execute statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($param_types, ...$param_values);
    
    if ($stmt->execute()) {
        // Add notification for admin
        $notification_message = "Rider updated: " . $rider_name;
        $today = date("Y-m-d");
        $admin_role = "admin";
        $wh_id = 1; // Default warehouse ID
        
        $notification_stmt = $conn->prepare("INSERT INTO admin_notification (message, date, admin_role, fk_wh_id) VALUES (?, ?, ?, ?)");
        $notification_stmt->bind_param("sssi", $notification_message, $today, $admin_role, $wh_id);
        $notification_stmt->execute();
        
        echo "<script>alert('Rider updated successfully!'); window.location.href='riders.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating rider: " . $stmt->error . "');</script>";
    }
}

// Fetch rider data
$stmt = $conn->prepare("SELECT * FROM rider WHERE rider_id = ?");
$stmt->bind_param("i", $rider_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Rider not found!'); window.location.href='riders.php';</script>";
    exit;
}

$rider = $result->fetch_assoc();
?>

<!-- Edit Rider Form -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">Edit Rider</h6>
                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rider_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="rider_name" name="rider_name" 
                                value="<?php echo htmlspecialchars($rider['rider_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="rider_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="rider_email" name="rider_email" 
                                value="<?php echo htmlspecialchars($rider['rider_email']); ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rider_contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="rider_contact" name="rider_contact" 
                                value="<?php echo htmlspecialchars($rider['rider_contact']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="rider_cnic" class="form-label">CNIC</label>
                            <input type="text" class="form-control" id="rider_cnic" name="rider_cnic" 
                                value="<?php echo htmlspecialchars($rider['rider_cnic']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="rider_address" class="form-label">Address</label>
                        <textarea class="form-control" id="rider_address" name="rider_address" rows="3" required><?php echo htmlspecialchars($rider['rider_address']); ?></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rider_password" class="form-label">Password (Leave blank to keep current password)</label>
                            <input type="password" class="form-control" id="rider_password" name="rider_password">
                            <small class="text-info">Leave blank to keep current password</small>
                        </div>
                        <div class="col-md-6">
                            <label for="rider_image" class="form-label">Profile Image</label>
                            <input class="form-control bg-dark" type="file" id="rider_image" name="rider_image">
                            <?php if (!empty($rider['rider_image']) && $rider['rider_image'] != 'default.jpg'): ?>
                                <div class="mt-2">
                                    <img src="../../../uploads/rider-profile/<?php echo htmlspecialchars($rider['rider_image']); ?>" 
                                         alt="Rider profile" style="max-width: 100px; max-height: 100px;" class="img-thumbnail">
                                    <small class="text-info">Current image</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="rider_status" class="form-label">Status</label>
                        <select class="form-select" id="rider_status" name="rider_status" required>
                            <option value="approved" <?php echo ($rider['rider_status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="pending" <?php echo ($rider['rider_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="inactive" <?php echo ($rider['rider_status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Rider</button>
                    <a href="riders.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Rider Form End -->

<?php 
// including the footer
require_once("../../includes/footer.php");
?>
