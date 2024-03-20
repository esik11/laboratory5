<?php
session_start();
include('includes/header.php');
include('includes/db-conn.php');


if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header("Location: login.php");
    exit();
}

// Check if id parameter is present in the URL
if(isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $query = "SELECT * FROM user_profile WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "User not found.";
        exit();
    }
} else {
    // If id parameter is not present, handle the error or redirect the user
    // For instance, you can redirect the user to the profile page or display an error message
    header("Location: profile.php");
    exit();
}

// Check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user input
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Handle profile picture upload
    if ($_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if($check !== false) {
            // File is an image
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["profile_pic"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                // File uploaded successfully, update the database
                $update_query = "UPDATE user_profile SET full_name = '$full_name', email = '$email', password = '$password', phone_number = '$phone_number', address = '$address', profile_pic = '$target_file' WHERE user_id = $user_id";

                if(mysqli_query($conn, $update_query)) {
                    header("Location: user-profile.php?success=User information updated successfully.");
                    exit();
                } else {
                    echo "Error updating user information: " . mysqli_error($conn);
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // If no new profile picture is uploaded, update other user information
        $update_query = "UPDATE user_profile SET full_name = '$full_name', email = '$email', password = '$password', phone_number = '$phone_number', address = '$address' WHERE user_id = $user_id";

        if(mysqli_query($conn, $update_query)) {
            header("Location: user-profile.php?success=User information updated successfully.");
            exit();
        } else {
            echo "Error updating user information: " . mysqli_error($conn);
        }
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Edit User</h4>
            </div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo $user['full_name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" value="<?php echo $user['password']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number" class="form-control" value="<?php echo $user['phone_number']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
            <input type="text" id="address" name="address" class="form-control" value="<?php echo $user['address']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="profile_pic" class="form-label">Profile Picture</label>
                        <input type="file" id="profile_pic" name="profile_pic" class="form-control">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>