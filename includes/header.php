<?php // requiring the init file
require("includes/init.php");
?>
<?php
// checking session if the user is logged in
$uri = "" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "";
if (!str_contains($uri, 'signup.php') && !str_contains($uri, 'login.php')) {
  if (!$_SESSION['logged_in']) {
    header("Location: ./login.php");
  }
} elseif (str_contains($uri, 'signup.php') || str_contains($uri, 'login.php')) {
  if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in']) {
      header("Location: ./");
    }
  }
}

// logout
if (isset($_GET['req']) && $_GET['req'] == '1') {
  logout_main("yes");
  redirect("./");
}
?>
<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <link rel="icon" href="images/photos/logo-phone-mate-simple.png" type="image/gif" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>Phone Mate</title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
  <!-- google font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">

  <!-- bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> -->

  <!-- font awesome style -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

</head>

<body>

  <div class="hero_area">
    <!-- header section strats -->
    <header class="header_section">
      <div class="navbar1">
        <div class="container-fluid">

          <nav class="navbar navbar-expand-sm navbar-dark custom_nav-container pt-1 pb-0">
            <a class="navbar-brand" href="./">
              <!-- <span> -->
              <h4 class="project-heading">
                <img src="./images/photos/logo-phone-mate-simple.png" width="30px" height="30px" alt="phone-mate-logo"> Phone Mate
              </h4>
              <!-- <h1 class="anton-regulr">Wire</h1> -->
              <!-- </span> -->
            </a>
            <?php
            // getting the url of the page
            $uri = "" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "";

            // if the url is not signup.php page then show nav links
            if (!str_contains($uri, 'signup.php') && !str_contains($uri, 'login.php')) {
            ?>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>

              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                  <li class="nav-item">
                    <a class="nav-link " href="./index.php">Home <span class="visually-hidden">(current)</span></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " href="./items.php">Product Details</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " href="./payments.php">Payments</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="./my-profile.php"> Profile </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="./?req=1"> Logout <span class="anton-regular"><i class="fa fa-sign-out" aria-hidden="true"></i></span></a>
                  </li>
                </ul>
              </div>
            <?php
            }
            ?>
          </nav>

        </div>
      </div>
    </header>
    <!-- end header section -->