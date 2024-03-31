<?php
session_start();
include('includes/header.php');
include('includes/topbar.php');
include('includes/db-conn.php');
require_once 'config.php';

if (!isset($_SESSION['fb_id']) || empty($_SESSION['fb_id'])) {
    echo "<div class='alert alert-danger'>Session fb_id is not set or invalid.</div>";
    exit();
}
echo "<div>Session fb_id: " . $_SESSION['fb_id'] . "</div>";

// Check if the form is submitted for profile update
if (isset($_POST['update_profile'])) {
    // Validate and sanitize input data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $gender = filter_var($_POST['gender'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);

    // Check if session fb_id is set and valid
    if (!isset($_SESSION['fb_id']) || empty($_SESSION['fb_id'])) {
        echo "<div class='alert alert-danger'>Session fb_id is not set or invalid.</div>";
        exit(); // Stop execution if session fb_id is not valid
    }

    // Prepare and execute the update query
    $query = "UPDATE profile SET email = ?, firstname = ?, last_name = ?, gender = ?, phone = ?, address = ? WHERE fb_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssss", $email, $firstname, $lastname, $gender, $phone, $address, $_SESSION['fb_id']);
    $stmt->execute();

    // Check for errors and changes
    if ($stmt->errno) {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    } else {
        // Check if the query was successful
        if ($stmt->affected_rows > 0) {
            echo "<div class='alert alert-success'>Profile updated successfully.</div>";
            // Redirect to a different page after successful update
            header("Location: profile.php");
            exit();
        } else {
            // No rows affected, no changes made
            echo "<div class='alert alert-warning'>No changes detected or failed to update profile.</div>";
        }
    }

    // Close the prepared statement
    $stmt->close();
}

// Fetch user profile data from the database
if (isset($_SESSION['fb_id']) && !empty($_SESSION['fb_id'])) {
    // Prepare and execute the select query
    $query = "SELECT * FROM profile WHERE fb_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $_SESSION['fb_id']);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userinfo = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>User profile not found.</div>";
    }

    // Close the prepared statement
    $stmt->close();
}

// Check if the database connection was successful
if ($conn->connect_error) {
    echo "<div class='alert alert-danger'>Error: " . $conn->connect_error . "</div>";
}

// Close the database connection
$conn->close();
?>

<div class="content-wrapper">
    <section class="content-header">
        <!-- Header content -->
    </section>
    <section class="content">
        <?php ob_start(); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card">
                        <div class="card-body">
                            <?php
                            // Check if the form is submitted
                            if (!isset($_POST['update_profile'])) {
                                // Display the form
                                ?>
                                <form method="post" action="">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" class="form-control" id="email"
                                               value="<?php echo isset($userinfo['email']) ? $userinfo['email'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="firstname">First Name</label>
                                        <input type="text" name="firstname" class="form-control"
                                               id="firstname" value="<?php echo isset($userinfo['firstname']) ? $userinfo['firstname'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="lastname">Last Name</label>
                                        <input type="text" name="lastname" class="form-control"
                                               id="lastname" value="<?php echo isset($userinfo['lastname']) ? $userinfo['lastname'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <input type="text" name="gender" class="form-control"
                                               id="gender" value="<?php echo isset($userinfo['gender']) ? $userinfo['gender'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="text" name="phone" class="form-control"
                                               id="phone" value="<?php echo isset($userinfo['phone']) ? $userinfo['phone'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" name="address" class="form-control"
                                               id="address" value="<?php echo isset($userinfo['address']) ? $userinfo['address'] : ''; ?>">
                                    </div>
                                    <input type="submit" name="update_profile"
                                           class="btn btn-primary" value="Update">
                                    <a href="profile.php" class="d-block">go back</a>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php ob_end_flush(); ?>
    </section>
</div>

<?php include('includes/footer.php'); ?>
