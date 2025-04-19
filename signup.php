<?php require_once("includes/header.php"); ?>
<?php

// Check if all fields are set
if (isset($_FILES["ws_image"]) && isset($_POST["ws_name"]) && isset($_POST["ws_company_name"]) && isset($_POST["ws_home_address"])  && isset($_POST["ws_personal_contact"])  && isset($_POST["ws_office_contact"])  && isset($_POST["ws_cnic"])  && isset($_POST["ws_email"])  && isset($_POST["ws_password"])) {

  // fetching the form data
  $ws = new Wholesaler();
  $ws->ws_name = escape($_POST["ws_name"]);
  $ws->ws_company_name = escape($_POST["ws_company_name"]);
  $ws->ws_home_address = escape($_POST["ws_home_address"]);
  $ws->ws_office_address = escape($_POST["ws_office_address"]);
  $ws->ws_personal_contact = escape($_POST["ws_personal_contact"]);
  $ws->ws_office_contact = escape($_POST["ws_office_contact"]);
  $ws->ws_cnic = escape($_POST["ws_cnic"]);
  $ws->ws_email = escape($_POST["ws_email"]);
  $ws->ws_password = escape($_POST["ws_password"]);
  $ws_password = $_POST["ws_password"];
  $ws_repassword = $_POST["ws_repassword"];

  // // Check if image file is an actual image or a fake image
  // $check = getimagesize($_FILES["ws_image"]["tmp_name"]);
  // if ($check !== false) {
  //   // Allow certain file formats
  //   if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
  //     header("Location: ./signup.php?m=Sorry, there was an error uploading your file.");
  //     $uploadOk = 0;
  //   }
  // } else {
  //   header("Location: ./signup.php?m=Sorry, there was an error uploading your file.");
  //   $uploadOk = 0;
  // }
  $uploadOk = 1;
  if ($uploadOk == 0) {
    redirect("../school-profile.php?m=Sorry, your file was not uploaded.");
  } else {
    // same password check
    if ($ws_password === $ws_repassword) {
      // hashing the password
      $ws_password = md5($ws_password);
      $ws->ws_password = $ws_password;
      // file upload handling
      $target_dir = "./uploads/ws-profile/";
      $target_file = $target_dir . basename($_FILES["ws_image"]["name"]);
      $ws_image = basename($_FILES["ws_image"]["name"]);
      $uploadOk = 1;
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
      // change the structure of the code
      //  
      //  
      //  
      if (move_uploaded_file($_FILES["ws_image"]["tmp_name"], $target_file)) {
        // Insert data of wholesaler
        if ($ws->insert()) {
          // rename the image
          $rplc = $db->last_id();
          rename("./uploads/ws-profile/" . $ws_image . "", "./uploads/ws-profile/" . $rplc . $ws_image . "");
          $ws_image = $rplc . $ws_image;
          $ws->ws_image = escape($ws_image);
          $ws->ws_id = escape($rplc);
        }
        // update after renaming picture
        if ($ws->update()) {
          header("Location: ./login.php");
        } else {
          header("Location: ./signup.php?m=Error: " . mysqli_error($conn));
          echo "Error: " . mysqli_error($conn);
        }
      } else {
        header("Location: ./signup.php?m=Sorry, there was an error uploading your file.");
      }
    } else {
      header("Location: ./signup.php?m=Your password does not match");
    }
  }
}

if (isset($_GET['m'])) {
  echo $_GET['m'];
}

?>

<section class="h-100 bg-light">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col">
        <!-- <div class="card card-registration bg-dark my-4"> -->
        <div class="row g-0">
          <div class="col-md-5 offset-md-1 d-none d-md-block px-0">
            <img src="images/photos/signup-photo5.jpg" alt="Sample photo" class="img-fluid"
              style="width: 100%; height: 100%; object-fit: cover; border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;" />
          </div>
          <div class="col-md-5 rounded-right p-4 bg-dark">
            <div class="card-body p-md-2 text-black">
              <h3 class="mb-5 text-capitalize text-center"><strong>Supplier Registration</strong></h3>

              <form action="" method="POST" enctype="multipart/form-data">

                <div data-mdb-input-init class="form-outline mb-2">
                  <input type="file" id="form3Example90" name="ws_image" class="form-control form-control-sm" />
                  <label class="form-label" for="form3Example90">Supplier Image</label>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-2">
                    <div data-mdb-input-init class="form-outline">
                      <input type="text" id="form3Example1m" name="ws_name" class="form-control form-control-sm" placeholder="Enter Name" required />
                      <label class="form-label" for="form3Example1m">Name</label>
                    </div>
                  </div>
                  <div class="col-md-6 mb-2">
                    <div data-mdb-input-init class="form-outline">
                      <input type="text" id="form3Example1n" name="ws_company_name" class="form-control form-control-sm" placeholder="company name(optional)" />
                      <label class="form-label" for="form3Example1n">Company Name</label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-2">
                    <div data-mdb-input-init class="form-outline">
                      <input type="text" id="form3Example1m1" name="ws_home_address" class="form-control form-control-sm" placeholder="Enter Home address" required />
                      <label class="form-label" for="form3Example1m1">Home Address</label>
                    </div>
                  </div>
                  <div class="col-md-6 mb-2">
                    <div data-mdb-input-init class="form-outline">
                      <input type="text" id="form3Example1n1" name="ws_office_address" class="form-control form-control-sm" placeholder="Enter Office address " required />
                      <label class="form-label" for="form3Example1n1">Office Address</label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-2">
                    <div data-mdb-input-init class="form-outline">
                      <input type="tel" id="form3Example1m1" name="ws_personal_contact" placeholder="+92XXXXXXXXXX"
                        pattern="\+92\d{10}" maxlength="13" class="form-control form-control-sm" required />
                      <label class="form-label" for="form3Example1m1">Personal Contact</label>
                    </div>
                  </div>
                  <div class="col-md-6 mb-2">
                    <div data-mdb-input-init class="form-outline">
                      <input type="tel" id="form3Example1n1" name="ws_office_contact" placeholder="+92XXXXXXXXXX"
                        pattern="\+92\d{10}" maxlength="13" class="form-control form-control-sm" required />
                      <label class="form-label" for="form3Example1n1">Office Contact</label>
                    </div>
                  </div>
                </div>

                <div data-mdb-input-init class="form-outline mb-2">
                  <input type="text" id="cnic" name="ws_cnic" placeholder="XXXXX-XXXXXXX-X"
                    pattern="\d{5}-\d{7}-\d{1}" maxlength="15" class="form-control form-control-sm" required />
                  <label class="form-label" for="form3example99">Supplier CNIC</label>
                </div>

                <div data-mdb-input-init class="form-outline mb-2">
                  <input type="email" id="form3Example99" name="ws_email" class="form-control form-control-sm" placeholder="e.g Wire@gmail.com" required />
                  <label class="form-label" for="form3Example99">Email Address</label>
                </div>

                <div data-mdb-input-init class="form-outline mb-2">
                  <input type="password" id="form3Example97" name="ws_password" class="form-control form-control-sm" placeholder="Enter your password" required />
                  <label class="form-label" for="form3Example97">Password</label>
                </div>

                <div data-mdb-input-init class="form-outline mb-2">
                  <input type="password" id="form3Example98" name="ws_repassword" class="form-control form-control-sm" placeholder="Enter your password" required />
                  <label class="form-label" for="form3Example98">Re-enter Password</label>
                </div>

                <div class="d-flex justify-content-end pt-2">
                  <button type="reset" data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-light btn-sm" style="margin-right: 8px;">Reset</button>
                  <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-danger btn-sm bg-red">Submit</button>
                </div>
                <p class="text-red">Already Registered? <a href="./login.php" class="text-primary">Login</a></p>
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

<?php // requiring the footer of page
require_once("includes/footer.php");
?>