<?php
session_start();

include('includes/db-conn.php');

if (isset($_SESSION['id'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Escape user inputs for security
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
        $middleName = mysqli_real_escape_string($conn, $_POST['middle_name']);
        $lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $dateOfBirth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $phoneNumber = mysqli_real_escape_string($conn, $_POST['phone']);

        // Validate phone number format (optional)
        $phoneNumber = mysqli_real_escape_string($conn, $_POST['phone']);

        // Validate phone number format
        if (!preg_match("/^\d{10,14}$/", $phoneNumber)) {
            // Invalid phone number format
            echo "<script>alert('Please enter a valid phone number.');</script>";
            echo "<script>window.location.href = 'profile.php#profileSettings';</script>";
            exit();
        }

        $userId = $_SESSION['id'];

        // Check if a new profile picture is uploaded
        if ($_FILES['profile_pic']['size'] > 0) {
            $targetDir = "uploads/"; // Directory where uploaded files will be saved
            $profilePic = $targetDir . basename($_FILES["profile_pic"]["name"]); // Path of the uploaded profile picture
            
            // Get the file extension
            $fileExtension = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);

            // Check if the file extension is not allowed
            if (!in_array(strtolower($fileExtension), array("jpg", "jpeg", "png", "gif"))) {
                // Invalid file type
                echo "<script>alert('Invalid file type. Please upload an image file.');</script>";
                echo "<script>window.location.href = 'profile.php#profileSettings';</script>";
                exit();
            }

            // Move the uploaded file to the designated directory
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profilePic)) {
                // Update profile picture path in the database
                $updateProfilePicQuery = "UPDATE profile SET profile_pic='$profilePic' WHERE id='$userId'";
                mysqli_query($conn, $updateProfilePicQuery);
            } else {
                // Failed to upload profile picture
                echo "<script>alert('Failed to upload profile picture.');</script>";
            }
        }

        // Update user data in the database
        $updateQuery = "UPDATE profile SET username='$username', first_name='$firstName', middle_name='$middleName', last_name='$lastName', email='$email', gender='$gender', date_of_birth='$dateOfBirth', address='$address', phone='$phoneNumber' WHERE id='$userId'";

        if (mysqli_query($conn, $updateQuery)) {
            // User data updated successfully
            echo "<script>alert('Profile updated successfully.');</script>";
            echo "<script>setTimeout(function(){ window.location.href = 'profile.php#profileSettings?success=Profile updated successfully.'; }, 1000);</script>";
            exit();
        } else {
            // Failed to update user data
            echo "<script>alert('Error updating profile.');</script>";
            echo "<script>window.location.href = 'profile.php#profileSettings?error=Error updating profile.';</script>";
            exit();
        }
    } else {
        // Redirect if accessed via GET method
        header("Location: profile.php");
        exit();
    }
} else {
    // Redirect if user is not logged in
    header("Location: login.php");
    exit();
}
?>
