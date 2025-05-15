<?php // requiring the init file
require_once("../../../includes/init.php");
?>
<?php
// checking session if the user is logged in
$uri = "" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "";
if (!str_contains($uri, 'signup.php') && !str_contains($uri, 'login.php')) {
    if (!$_SESSION['logged_in_admin']) {
        header("Location: ../login");
    }
} elseif (str_contains($uri, 'signup.php') || str_contains($uri, 'login.php')) {
    if (isset($_SESSION['logged_in_admin'])) {
        if ($_SESSION['logged_in_admin']) {
            header("Location: ../");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Phone Mate</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="../../../images/photos/logo-phone-mate-simple.png" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../../css/style.css" rel="stylesheet">
</head>

<body>
    <?php
    // getting the url of the page
    $uri = "" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "";
    if (!str_contains($uri, 'login.php')) {
    ?>
        <div class="container-fluid position-relative d-flex p-0">
        <?php
    }
        ?>
        <!-- Spinner Start -->
        <!-- <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div> -->
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <?php
        // getting the url of the page
        $uri = "" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "";
        if (!str_contains($uri, 'login.php')) {
        ?>
            <div class="sidebar pe-4 pb-3">
                <nav class="navbar bg-secondary navbar-dark">
                    <a href="../index" class="navbar-brand mx-4 mb-3">
                        <h3 class="text-primary"><i class="fa fa-user me-2"></i>Phone Mate</h3>
                    </a>
                    <div class="d-flex align-items-center ms-4 mb-4">
                        <div class="position-relative">
                            <img class="rounded-circle" src="../../img/default-admin-image.jpg" alt="" style="width: 40px; height: 40px;">
                            <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0"><?php echo $_SESSION['admin_name']; ?></h6>
                            <span>Admin</span>
                        </div>
                    </div>
                    <div class="navbar-nav w-100">
                        <a href="../index" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                        <!-- <a href="../new-listings" class="nav-item nav-link"><i class="fa fa-list-alt me-2"></i>New Listings</a>
                        <a href="../new-listings" class="nav-item nav-link"><i class="fa fa-list-alt me-2"></i>Approved Items</a> -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-laptop me-2"></i>Products</a>
                            <div class="dropdown-menu bg-transparent border-0">
                                <a href="../listings/new-listings.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> New Listings</a>
                                <a href="../listings/listing-adjustment.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Listings Adjustment</a>
                                <a href="../listings/processed-listings.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Listed Items</a>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-user me-2"></i>Profiles</a>
                            <div class="dropdown-menu bg-transparent border-0">
                                <a href="../profiles/wholesalers.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Suppliers</a>
                                <a href="../profiles/shopkeepers.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Buyers</a>
                                <a href="../profiles/riders.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Riders</a>
                                <a href="../business_developers/list_bds.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Business Developers</a>
                                <a href="../business_developers/bd_commissions.php" class="dropdown-item"><i class="fa fa-money-bill" aria-hidden="true"></i> BD Commissions</a>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-user me-2"></i>Approvals</a>
                            <div class="dropdown-menu bg-transparent border-0">

                                <a href="../approvals/ws-approvals.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Suppliers</a>
                                <a href="../approvals/shopkeeper-approvals.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Buyers</a>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-box me-2"></i>Orders</a>
                            <div class="dropdown-menu bg-transparent border-0">
                                <a href="../orders/pending-orders.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Pending</a>
                                <a href="../orders/assign-riders.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Assign Riders</a>
                                <a href="../orders/in-delivery-orders.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Shipping</a>
                                <a href="../orders/rider-updated-orders.php" class="dropdown-item"><i class="fa fa-motorcycle" aria-hidden="true"></i> Rider Updates</a>
                                <a href="../orders/completed-orders.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Completed</a>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-money-bill-wave me-2"></i>Payments</a>
                            <div class="dropdown-menu bg-transparent border-0">
                                <a href="../payments/wspp.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Suppliers</a>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-money-bill-wave me-2"></i>Paid Records</a>
                            <div class="dropdown-menu bg-transparent border-0">
                                <a href="../payments/wsr.php" class="dropdown-item"><i class="fa fa-arrow-right" aria-hidden="true"></i> Suppliers</a>
                            </div>
                        </div>
                        <a href="../earnings/c-earnings.php" class="nav-item nav-link"><i class="fa fa-money-bill-wave me-2"></i>Company Profit</a>
                        <!-- <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-laptop me-2"></i>Elements</a>
                            <div class="dropdown-menu bg-transparent border-0">
                                <a href="../../button.html" class="dropdown-item">Buttons</a>
                                <a href="../../typography.html" class="dropdown-item">Typography</a>
                                <a href="../../element.html" class="dropdown-item">Other Elements</a>
                            </div>
                        </div> -->
                        <!-- <a href="../../widget.html" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Widgets</a>
                        <a href="../../form.html" class="nav-item nav-link"><i class="fa fa-keyboard me-2"></i>Forms</a>
                        <a href="../../table.html" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Tables</a>
                        <a href="chart.html" class="nav-item nav-link"><i class="fa fa-chart-bar me-2"></i>Charts</a> -->
                        <!-- <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>Pages</a>
                            <div class="dropdown-menu bg-transparent border-0">
                                <a href="../../signin.html" class="dropdown-item">Sign In</a>
                                <a href="../../signup.html" class="dropdown-item">Sign Up</a>
                                <a href="../../404.html" class="dropdown-item">404 Error</a>
                                <a href="../../blank.html" class="dropdown-item">Blank Page</a>
                            </div>
                        </div> -->
                    </div>
                </nav>
            </div>
            <!-- Sidebar End -->
        <?php // ending the if statement
        }
        ?>
        <!-- Content Start -->

        <!-- Navbar Start -->
        <?php
        // getting the url of the page
        $uri = "" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "";
        if (!str_contains($uri, 'login.php')) {
        ?>
            <div class="content">
                <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                    <a href="../" class="navbar-brand d-flex d-lg-none me-4">
                        <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
                    </a>
                    <a href="#" class="sidebar-toggler flex-shrink-0">
                        <i class="fa fa-bars"></i>
                    </a>
                    <!-- <form class="d-none d-md-flex ms-4">
                        <input class="form-control bg-dark border-0" type="search" placeholder="Search">
                    </form> -->
                    <div class="navbar-nav align-items-center ms-auto">
                        <!-- <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fa fa-envelope me-lg-2"></i>
                                <span class="d-none d-lg-inline-flex">Message</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                                <a href="#" class="dropdown-item">
                                    <div class="d-flex align-items-center">
                                        <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                        <div class="ms-2">
                                            <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                            <small>15 minutes ago</small>
                                        </div>
                                    </div>
                                </a>
                                <hr class="dropdown-divider">
                                <a href="#" class="dropdown-item">
                                    <div class="d-flex align-items-center">
                                        <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                        <div class="ms-2">
                                            <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                            <small>15 minutes ago</small>
                                        </div>
                                    </div>
                                </a>
                                <hr class="dropdown-divider">
                                <a href="#" class="dropdown-item">
                                    <div class="d-flex align-items-center">
                                        <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                        <div class="ms-2">
                                            <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                            <small>15 minutes ago</small>
                                        </div>
                                    </div>
                                </a>
                                <hr class="dropdown-divider">
                                <a href="#" class="dropdown-item text-center">See all message</a>
                            </div>
                        </div> -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fa fa-bell me-lg-2"></i>
                                <span class="d-none d-lg-inline-flex">Notificatin</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                                <a href="#" class="dropdown-item">
                                    <h6 class="fw-normal mb-0">Profile updated</h6>
                                    <small>15 minutes ago</small>
                                </a>
                                <hr class="dropdown-divider">
                                <a href="#" class="dropdown-item">
                                    <h6 class="fw-normal mb-0">New user added</h6>
                                    <small>15 minutes ago</small>
                                </a>
                                <hr class="dropdown-divider">
                                <a href="#" class="dropdown-item">
                                    <h6 class="fw-normal mb-0">Password changed</h6>
                                    <small>15 minutes ago</small>
                                </a>
                                <hr class="dropdown-divider">
                                <a href="#" class="dropdown-item text-center">See all notifications</a>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <img class="rounded-circle me-lg-2" src="../../img/default-admin-image.jpg" alt="" style="width: 40px; height: 40px;">
                                <span class="d-none d-lg-inline-flex"><?php echo $_SESSION['admin_name']; ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                                <a href="#" class="dropdown-item">My Profile</a>
                                <!-- <a href="#" class="dropdown-item">Settings</a> -->
                                <a href="../logout/logout.php?logout=1" class="dropdown-item">Log Out</a>
                            </div>
                        </div>
                    </div>
                </nav>
            <?php
            // end of if statement
        }

            ?>
            <!-- Navbar End -->

            <?php
            // getting the url of the page
            $uri = "" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "";
            if (str_contains($uri, 'login.php')) {
            ?>
                <!-- <div class="sidebar pe-4 pb-3"> -->
                <!-- <nav class="navbar bg-secondary navbar-dark"> -->
                <style>
                    body {
                        height: 100% !important;
                    }
                </style>
                <!-- <div class="">
                    <div class="conent">
                        <nav style="width: 100%;" class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                            <a style="padding-top: 2%;" href="../index" class="navbar-brand mx-4 mb-3">
                                <h3 class="text-primary"><i class="fa fa-user me-2"></i>Phone Mate</h3>
                            </a>
                        </nav> -->
                <!-- </div> -->
                <!-- <h1>This is form</h1> -->
            <?php
                // end of if statement
            }
            ?>