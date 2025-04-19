<?php // the header/navbar/sidebar
require_once("../../includes/nav-side.php");

?>


<?php
// verifying the login details
if (isset($_POST['login'])) {
    $password = $_POST['admin_password'];
    $password = escape($password);
    $email = $_POST['admin_email'];
    $email = escape($email);

    $password = md5($password);

    $query = "SELECT * FROM admin WHERE admin_email='$email' AND admin_password='$password'";
    $result = query($query);
    if (mysqli_num_rows($result) != 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['logged_in_admin'] = true;
        $_SESSION['admin_name'] = $row['admin_name'];
        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['admin_email'] = $row['admin_email'];
        $_SESSION['wh_id'] = $row['fk_wh_id'];
        $_SESSION['admin_role'] = $row['admin_role'];
        redirect("../index");
    } else {
        redirect("./login.php?m=Your email and password does not match!");
    }
}
?>

<div class="container-fluid position-relative d-flex p-0">
    <!-- Spinner Start -->
    <!-- <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div> -->
    <!-- Spinner End -->


    <!-- Log In Start -->
    <div class="container-fluid">
        <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
            <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="bg-secondary rounded p-4 p-sm-5 my-4 mx-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <a href="index.html" class="">
                            <h3 class="text-primary"><i class="fa fa-user me-2"></i>Phone Mate</h3>
                        </a>
                        <h3>Log In</h3>
                    </div>
                    <?php
                    if (isset($_GET['m'])) {
                    ?>
                        <div class="col-sm-12">
                            <div class="bg-secondary rounded h-100 p-0">
                                <!-- <h6 class="mb-4">Icons & Dismissing Alerts</h6>  -->
                                <div class="alert alert-light alert-dismissible fade show" role="alert">
                                    <i class="fa fa-exclamation-circle me-2"></i>

                                    <?php echo $_GET['m']; ?>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    <?php }
                    ?>
                    <form action="" method="post">
                        <div class="form-floating mb-3">
                            <input type="email" name="admin_email" class="form-control" id="floatingInput" placeholder="name@example.com">
                            <label for="floatingInput">Email address</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="password" name="admin_password" class="form-control" id="floatingPassword" placeholder="Password">
                            <label for="floatingPassword">Password</label>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <!-- <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                <label class="form-check-label" for="exampleCheck1">Check me out</label>
                            </div> -->
                            <!-- <a href="">Forgot Password</a> -->
                        </div>
                        <button type="submit" name="login" class="btn btn-primary py-3 w-100 mb-4">Log In</button>
                        <!-- <p class="text-center mb-0">Don't have an Account? <a href="">Sign Up</a></p> -->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Log In End -->
</div>

<?php // including the footer
require_once("../../includes/footer.php");
?>