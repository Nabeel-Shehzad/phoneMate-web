<?php
// header
require_once("includes/header.php");

?>
<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve form data
  $ws_email = $_POST["ws_email"];
  $ws_password = md5($_POST["ws_password"]);

  $ws_email = escape($ws_email);
  $ws_password = escape($ws_password);

  // Query to retrieve user from signup table
  $sql = "SELECT * FROM wholesaler WHERE ws_email = '$ws_email' AND ws_password = '$ws_password' AND ws_status='approved'";
  $result = query($sql);

  if (mysqli_num_rows($result) != 0) {
    // Set session variables
    $row = mysqli_fetch_assoc($result);
    $_SESSION["logged_in"] = true;
    $_SESSION["email"] = $ws_email;
    $_SESSION["ws_id"] = $row['ws_id'];

    // Redirect to dashboard or any other secure page
    header("Location: ./index.php");
    exit;
  } else {
    // Redirect back to the login page with error parameter
    header("Location: login.php?error=1");
    exit;
  }
}
?>

<section class="h-100 bg-light">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col">
        <!-- <div class="card card-registration bg-dark my-4"> -->
        <div class="row g-0">
          <div class="col-md-4 offset-md-2 d-none d-md-block px-0">
            <img src="images/photos/login-photo4.jpg" alt="Sample photo" class="img-fluid"
              style="width: 100%; height: 100%; object-fit: cover; border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;" />
          </div>
          <div class="col-md-4 bg-dark p-4 rounded-right">
            <div class="card-body p-md-2 text-black">
              <h3 class="mb-5 text-capitalize mt-5 text-center"><strong>Supplier Login</strong></h3>

              <?php
              // Display error message if login fails
              if (isset($_GET["error"])) {
                echo "<p class='text-danger mt-3 text-center'>Invalid email or password.</p>";
              }
              ?>

              <!-- Form starts here -->
              <form action="" method="POST" enctype="multipart/form-data">
                <br>
                <div data-mdb-input-init class="form-outline">
                  <input type="text" id="form3Example99" name="ws_email" placeholder="email" class="form-control form-control-sm" required />
                  <label class="form-label" for="form3Example99">Email Address</label>
                </div>

                <div data-mdb-input-init class="form-outline">
                  <input type="password" id="form3Example97" name="ws_password" placeholder="password" class="form-control form-control-sm" required />
                  <label class="form-label" for="form3Example97">Password</label>
                </div>

                <div class="d-flex justify-content-end pt-2">
                  <button type="reset" data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-light btn-sm" style="margin-right: 8px;">Reset</button>
                  <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-danger btn-sm bg-red">Log In</button>
                </div>
                <p class="text-red">No account? <a href="./signup.php" class="text-primary">Signup</a></p>

              </form>
              <!-- Form ends here -->
            </div>
          </div>
        </div>
        <!-- </div> -->
      </div>
    </div>
  </div>
</section>

<!-- jQuery -->
<script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
<!-- popper js -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
</script>
<!-- bootstrap js -->
<script type="text/javascript" src="js/bootstrap.js"></script>
<!-- custom js -->
<script type="text/javascript" src="js/custom.js"></script>
<!-- Google Map -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCh39n5U-4IoWpsVGUHWdqB6puEkhRLdmI&callback=myMap">
</script>
<!-- End Google Map -->

</body>

</html>
<?php // requiring the footer of page
require_once("includes/footer.php");
?>