<?php
// the header/navbar/sidebar
require_once("../../includes/nav-side.php");

// Get the database connection using the DB class from init.php
$db = new DB();
$conn = $db->conn;
?>

<!-- Rider Management Page -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <div class="d-flex justify-content-between mb-4">
                    <h6 class="mb-0">All Riders</h6>
                    <a href="add-rider.php" class="btn btn-primary m-2">Add New Rider</a>
                </div>
                <div class="table-responsive">
                    <table class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                            <tr class="text-white">
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Contact</th>
                                <th scope="col">Address</th>
                                <th scope="col">CNIC</th>
                                <th scope="col">Email</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch all riders from database
                            $stmt = $conn->prepare("SELECT * FROM rider ORDER BY rider_id DESC");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>
                                        <td>' . $row['rider_id'] . '</td>
                                        <td>' . $row['rider_name'] . '</td>
                                        <td>' . $row['rider_contact'] . '</td>
                                        <td>' . $row['rider_address'] . '</td>
                                        <td>' . $row['rider_cnic'] . '</td>
                                        <td>' . $row['rider_email'] . '</td>
                                        <td>' . $row['rider_status'] . '</td>
                                        <td>
                                            <a href="edit-rider.php?id=' . $row['rider_id'] . '" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="delete-rider.php?id=' . $row['rider_id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this rider?\')">Delete</a>
                                        </td>
                                    </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="8" class="text-center">No riders found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Rider Management End -->

<?php 
// including the footer
require_once("../../includes/footer.php");
?>
