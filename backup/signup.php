<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <link rel="icon" href="images/fevicon/fevicon.png" type="image/gif" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>HandTime</title>


  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

  <!-- bs -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <!-- font awesome style -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

</head>

<body>
<?php
include ("conn.php");

  // Check if all fields are set
  if (isset($_FILES["ws_image"]) && isset($_POST["ws_name"]) && isset($_POST["ws_company_name"]) && isset($_POST["ws_home_address"])  && isset($_POST["ws_personal_contact"])  && isset($_POST["ws_office_contact"])  && isset($_POST["ws_cnic"])  && isset($_POST["ws_email"])  && isset($_POST["ws_password"])) {
      // Get form data
     
      $ws_name = $_POST["ws_name"];
      $ws_company_name = $_POST["ws_company_name"];
      $ws_home_address = $_POST["ws_home_address"];
      $ws_office_address = $_POST["ws_office_address"];
      $ws_personal_contact = $_POST["ws_personal_contact"];
      $ws_office_contact = $_POST["ws_office_contact"];
      $ws_cnic = $_POST["ws_cnic"];
      $ws_email = $_POST["ws_email"];
      $ws_password = $_POST["ws_password"];

      // File upload handling
      $target_dir = "./uploads/"; // Directory where the file will be saved
      $target_file = $target_dir . basename($_FILES["ws_image"]["name"]); // Path of the uploaded file
      $ws_image = basename($_FILES["ws_image"]["name"]);
      $uploadOk = 1; // Flag to check if file is uploaded successfully
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // File extension

      // Check if image file is an actual image or a fake image
      $check = getimagesize($_FILES["ws_image"]["tmp_name"]);
      if ($check !== false) {
          // Allow certain file formats
          if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
              header("Location: ./signup.php?m=Sorry, there was an error uploading your file.");
              $uploadOk = 0;
          }
      } else {
          header("Location: ./signup.php?m=Sorry, there was an error uploading your file.");
          $uploadOk = 0;
      }

      // Check if the file already exists
      // if (file_exists($target_file)) {
      //     redirect("../school-profile.php?m=Sorry, the file already exists.");
      //     $uploadOk = 0;
      // }

      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
          // redirect("../school-profile.php?m=Sorry, your file was not uploaded.");
      } else {
          // if everything is ok, try to upload file
          if (move_uploaded_file($_FILES["ws_image"]["tmp_name"], $target_file)) {
              // Image uploaded successfully, now insert form data into the database

              // Insert form data and image path into the database
              $query = "INSERT INTO wholesaler (ws_image, ws_name, ws_company_name, ws_home_address, ws_office_address, ws_personal_contact, ws_office_contact, 	ws_cnic, ws_email, ws_password) 
              VALUES ('$ws_image', '$ws_name', '$ws_company_name', '$ws_home_address', '$ws_office_address', '$ws_personal_contact', '$ws_office_contact', '$ws_cnic', '$ws_email', '$ws_password')";
              $result = mysqli_query($conn, $query);
              if ($result) {
                  echo "Data has been successfully inserted.";
                  // redirect("../school-profile.php?m=Data has been successfully inserted.");
              } else {
                  header("Location: ./signup.php?m=Error: " . mysqli_error($conn));
                  echo "Error: " . mysqli_error($conn);
              }
          } else {
              header("Location: ./signup.php?m=Sorry, there was an error uploading your file.");
          }
      }
  } else {
      echo "All fields are required.";
  }

?>

  <div class="hero_area">
    <!-- header section strats -->
    <header class="header_section">
    <div class="navbar1">
      <div class="container-fluid">
        
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="index.html">
            <span>
              HandTime
            </span>
          </a>
        </nav>
      </div>
      </div>
    </header>
    <!-- end header section -->

<section class="h-100 bg-light">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col">
        <div class="card card-registration bg-dark my-4">
          <div class="row g-0">
            <div class="col-xl-6 d-none d-xl-block">
              <img src="images/banner.jpeg" alt="Sample photo" class="img-fluid"
                style="width: 100%; height: 100%; object-fit: cover; border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;" />
            </div>
            <div class="col-xl-6">
              <div class="card-body p-md-2 text-black">
                <h3 class="mb-5 text-uppercase">Supplier Registration</h3>

       


    
                <form action="" method="POST" enctype="multipart/form-data">
                  
                <div data-mdb-input-init class="form-outline mb-2">
                    <input type="file" id="form3Example90" name="ws_image" class="form-control form-control-sm"/>
                    <label class="form-label" for="form3Example90">Wholesaler Image</label>
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
                     <label class="form-label" for="form3example99">Wholesaler CNIC</label>
                     </div>

                  <div data-mdb-input-init class="form-outline mb-2">
                    <input type="email" id="form3Example99" name="ws_email" class="form-control form-control-sm" placeholder="e.g Wire@gmail.com"  required />
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
    <button type="reset" data-mdb-button-init data-mdb-ripple-init class="btn btn-light btn-sm" style="margin-right: 8px;">Reset</button>
    <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-danger btn-sm">Submit</button>
</div>


                </form>
                <!-- Form ends here -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



  <!-- info section -->
  <section class="info_section layout_padding2 color_black">
    <div class="container">
      <div class="info_logo">
        <h2>
          HandTime
        </h2>
      </div>
      <div class="row">

        <div class="col-md-4">
          <div class="info_contact">
            <h5>
              About Shop
            </h5>
            <div>
              <div class="img-box">
                <img src="images/location-white.png" width="18px" alt="">
              </div>
              <p>
                Address
              </p>
            </div>
            <div>
              <div class="img-box">
                <img src="images/telephone-white.png" width="12px" alt="">
              </div>
              <p>
                +01 1234567890
              </p>
            </div>
            <div>
              <div class="img-box">
                <img src="images/envelope-white.png" width="18px" alt="">
              </div>
              <p>
                demo@gmail.com
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info_info">
            <h5>
              Informations
            </h5>
            <p>
              ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
            </p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info_form ">
            <h5>
              Newsletter
            </h5>
            <form action="">
              <input type="email" placeholder="Enter your email">
              <button>
                Subscribe
              </button>
            </form>
            <div class="social_box">
              <a href="">
                <img src="images/fb.png" alt="">
              </a>
              <a href="">
                <img src="images/twitter.png" alt="">
              </a>
              <a href="">
                <img src="images/linkedin.png" alt="">
              </a>
              <a href="">
                <img src="images/youtube.png" alt="">
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="container">
      <p>
        &copy; <span id="displayYear"></span> All Rights Reserved By
        <a href="codsmine.com">Codsmine I.T Company</a>
      </p>
    </div>
  </section>

  <!-- end info_section -->

  <!-- footer section -->
  <!-- <section class="footer_section">
    <div class="container">
      <p>
        &copy; <span id="displayYear"></span> All Rights Reserved By
        <a href="https://html.design/">Codsmine I.T Company</a>
      </p>
    </div>
  </section> -->
  <!-- footer section -->

  <!-- jQery -->
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


