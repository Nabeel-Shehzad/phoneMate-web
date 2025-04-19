<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <h3 class="text-white-50">New Registered Buyers</h3>

        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary rounded h-100 w-100 p-4">
                <h4 class="mb-4">Approve Buyers</h4>
                <div class="table-responsive">
                    <span class="text-custom">Search:</span>
                    <table id="shopkeeper_approvals" style="background-color: #e74c3c;" class="table table-light table-bordered table-hover">
                        <thead>
                            <tr class="text-dark">
                                <!-- <th scope="col">#</th> -->
                                <th scope="col">Image</th>
                                <th scope="col">Name</th>
                                <th scope="col">Address</th>
                                <th scope="col">Email</th>
                                <th scope="col">Approve</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // selecting all the values from the database which do not have a agreement date
                            $query = "SELECT * FROM buyer ";
                            $query .= "Where buyer_status='pending' ORDER BY buyer_id DESC";
                            $result = mysqli_query($db->conn, $query);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {

                            ?>
                                    <tr>
                                        <td>
                                            <a href="#">
                                                <img class="rounded-circle" src="../../../uploads/buyer-profile/<?php
                                                                                                                if ($row['buyer_image'] == '') {
                                                                                                                    echo 'placeholder-pic.jpg';
                                                                                                                } else {
                                                                                                                    echo $row['buyer_image'];
                                                                                                                }
                                                                                                                ?>" width="60px" height="60px" alt="err...">
                                            </a>
                                        </td>
                                        <td><?php echo $row['buyer_name']; ?></td>
                                        <td><?php echo substr($row['buyer_address'], 0, 20); ?>...</td>
                                        <td><?php echo $row['buyer_email']; ?></td>
                                        <td>
                                            <a href="./approve-shopkeeper.php?buyer_id=<?php echo $row['buyer_id']; ?>" class="btn btn-square btn-outline-info m-2"><i class="fa fa-check" aria-hidden="true"></i></a>
                                            <!-- </form> -->
                                        </td>
                                    </tr>
                                <?php
                                    // end of the loop to fetch data
                                }
                                // end of if statement if the returned result is > 0
                            } else {
                                // show the message if no records
                                ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="row text-center">
                                            <div class="col-md-6 offset-md-3">
                                                <div class="alert alert-success"><strong>
                                                        No new Buyers for approval <?php echo $_SESSION['admin_name']; ?>, enjoy your day! &#x1F603;
                                                    </strong></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                // end of else to show empty table message
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<?php // including the footer
require_once("../../includes/footer.php");
?>