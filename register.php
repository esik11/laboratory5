<?php
session_start();
include('includes/db-conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Function to validate and sanitize input
    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Validate and sanitize form inputs
    $full_name = validate($_POST['full_name']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $address = validate($_POST['address']);
    $phone_number = validate($_POST['phone']);
    $firstname = validate($_POST['firstname']);
    $lastname = validate($_POST['last_name']);
    $gender = validate($_POST['gender']);

   // $allowedExts = array("jpg", "jpeg", "gif", "png");
    //$file_parts = explode(".", $_FILES["profile_pic"]["name"]);
       // $extension = end($file_parts);
    //if ((($_FILES["profile_pic"]["type"] == "image/gif")
        //|| ($_FILES["profile_pic"]["type"] == "image/jpeg")
        //|| ($_FILES["profile_pic"]["type"] == "image/png")
        //|| ($_FILES["profile_pic"]["type"] == "image/pjpeg"))
        //&& ($_FILES["profile_pic"]["size"] < 500000)
        //&& in_array($extension, $allowedExts)) {
        //if ($_FILES["profile_pic"]["error"] > 0) {
            //echo "Error: " . $_FILES["profile_pic"]["error"] . "<br>";
       // } else {
           // $target_dir = "uploads/";
           // $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
           // if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            //    echo "The file " . basename($_FILES["profile_pic"]["name"]) . " has been uploaded.";
           // } else {
                //echo "Sorry, there was an error uploading your file.";
           // }
       // }
    //} else {
       // echo "Invalid file";
   // }

    // Check if email already exists
    $email_check_query = "SELECT * FROM profile WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $email_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) { // If email exists
        if ($user['email'] === $email) {
            $_SESSION['error'] = "Email already exists";
        }
    }

    // Insert user data into the database if no errors
    if (!isset($_SESSION['error'])) {
        $sql = "INSERT INTO profile (email, password, firstname, last_name, full_name, gender, phone, address )
            VALUES ('$email', '$password', '$firstname', '$lastname', '$full_name', '$gender', '$phone_number' , '$address')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = "New record created successfully";
        } else {
            $_SESSION['error'] = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">User Registration</h2>
                        <?php
                        if(isset($_SESSION['error'])) {
                            echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                            unset($_SESSION['error']);
                        }
                        if(isset($_SESSION['success'])) {
                            echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
                            unset($_SESSION['success']);
                        }
                        ?>
                        <form action="register.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="firstname">First Name</label>
                                <input type="text" id="firstname" name="firstname" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="last_name" id="last_name" name="last_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="full_name" id="full_name" name="full_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <input type="text" id="gender" name="gender" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" class="form-control">
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary btn-block">Register</button>
                            <p class="mb-0">
                            <a href="login.php" class="text-center">Have account already? GO LOG IN</a>
                        </form>
                    </div>
                </div
