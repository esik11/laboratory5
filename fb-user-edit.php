<?php
session_start();
include('includes/header.php');

include('includes/db-conn.php');


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
    $success = $stmt->execute();
    
    // Check for errors
    if (!$success) {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    } else {
        // Check if any rows were updated
        if ($stmt->affected_rows == 0) {
            // Update session variables
        $_SESSION['fb_email'] = $email;
        $_SESSION['fb_name'] = "{$firstname} {$lastname}";
        $_SESSION['phone'] = $phone;
        $_SESSION['gender'] = $gender;
        $_SESSION['address'] = $address;

        // Redirect to a different page after successful update
        header("Location: fb-user-edit.php");
        exit();
        }
    }
    
    // Close the prepared statement
    $stmt->close();
}

// Fetch user profile data from the database
// Fetch user profile data from the database
if (isset($_SESSION['fb_id']) && !empty($_SESSION['fb_id'])) {
    // Check if the fb_id exists in the database
    $query = "SELECT COUNT(*) as count FROM profile WHERE fb_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $_SESSION['fb_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];

    if ($count > 0) {
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

<!-- Your HTML form for updating profile information goes here -->

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <!-- Header content -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card">
                        <div class="card-body">
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
                                           id="lastname" value="<?php echo isset($userinfo['last_name']) ? $userinfo['last_name'] : ''; ?>">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
include('includes/footer.php');
?>
