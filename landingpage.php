<?php
// Start session
session_start();

// Include database connection
include('includes/db-conn.php');

// Connect to the database
$conn = mysqli_connect($sname, $uname, $password, $db_name);

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Get the user information from the database
$user_id = $_SESSION['id'];
$query = "SELECT * FROM profile WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Include header, topbar, and sidebar files
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
?>

<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h1>Welcome <?php echo $user['full_name']; ?></h1> 
                        <div class="info">
                            <button type="button" class="btn btn-primary" onclick="location.href='profile.php?user_id=<?php echo $user_id; ?>';">User Profile</button>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Your content goes here -->
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <?php
    include('includes/footer.php');
    ?>
</div>
