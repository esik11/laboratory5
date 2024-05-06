<?php
// Start session to manage user data
session_start();

// Include database connection script
include('includes/db-conn.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect the user to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Retrieve user ID from session
$user_id = $_SESSION['id'];

// Query to fetch user data based on user ID
$query = "SELECT * FROM profile WHERE id = $user_id";

// Execute query
$result = mysqli_query($conn, $query);

// Check if query is successful and user data is found
if ($result && mysqli_num_rows($result) > 0) {
    // Fetch user details
    $user = mysqli_fetch_assoc($result);
} else {
    // Handle error if no user found with the given user ID
    // For instance, redirect the user to a login page or display an error message
}

// Check if the form is submitted
if (isset($_POST['update_profile'])) {
    // Retrieve updated profile data from the form
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Query to update user profile
    $update_query = "UPDATE profile SET email='$email', firstname='$firstname', last_name='$lastname', gender='$gender', phone='$phone', address='$address' WHERE id = $user_id";

    // Execute update query
    if (mysqli_query($conn, $update_query)) {
        // Redirect the user to the profile page after successful update
        header("Location: profile.php");
        exit();
    } else {
        // Handle error if update fails
        echo "Error updating profile: " . mysqli_error($conn);
    }
}

// Include header, topbar, and sidebar files for UI
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Profile</h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" id="email" value="<?php echo $user['email']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="firstname">First Name</label>
                                    <input type="text" name="firstname" class="form-control" id="firstname" value="<?php echo $user['firstname']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="lastname">Last Name</label>
                                    <input type="text" name="lastname" class="form-control" id="lastname" value="<?php echo $user['last_name']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <input type="text" name="gender" class="form-control" id="gender" value="<?php echo $user['gender']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" id="phone" value="<?php echo $user['phone']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" class="form-control" id="address" value="<?php echo $user['address']; ?>">
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('includes/footer.php'); ?>
