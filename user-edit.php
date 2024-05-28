<?php
// Start session
session_start();

// Include header file
include('includes/header.php');

// Include database connection
include('includes/db-conn.php');

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Retrieve the user ID from the session
$id = $_SESSION['id'];

// Fetch user information from the database based on the user ID
$query = "SELECT username, password, profile_pic, first_name, gender, middle_name, last_name, address, phone, email FROM profile WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    // If user exists, fetch user details
    $user = $result->fetch_assoc();
} else {
    // If user does not exist, display error message
    echo "User not found.";
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user input
    $username = htmlspecialchars($_POST['username']);
    $first_name = htmlspecialchars($_POST['first_name']);
    $middle_name = htmlspecialchars($_POST['middle_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $profile_pic = isset($_FILES['profile_pic']) ? htmlspecialchars($_FILES['profile_pic']['name']) : null;
    $address = htmlspecialchars($_POST['address']);
    $phone = htmlspecialchars($_POST['phone']);
    $gender = htmlspecialchars($_POST['gender']);
    

    // Check if a new password is provided
    if (!empty($_POST['password'])) {
        // Hash the new password
        $new_password = $_POST['password'];
        
        // Check if the new password is the same as the old one
        if (password_verify($new_password, $user['password'])) {
            // If the new password is the same as the old one, display an error message
            echo '<div class="alert alert-danger" role="alert">Cannot use the old password. Please provide a new one.</div>';
            exit(); // Stop further execution
        }
        
        // Hash the new password
        $password = password_hash($new_password, PASSWORD_DEFAULT);
    } else {
        // If no new password is provided, retain the existing password
        $password = $user['password'];
    }

    // Handle profile picture upload
    if ($profile_pic !== null && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        // If profile picture is uploaded successfully
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;

        // Check if uploaded file is an image
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if ($check !== false) {
            // File is an image
            $uploadOk = 1;
        } else {
            // File is not an image
            echo '<div class="alert alert-danger" role="alert">File is not an image.</div>';
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["profile_pic"]["size"] > 500000) {
            // If file size exceeds limit, display error message
            echo '<div class="alert alert-danger" role="alert">Sorry, your file is too large.</div>';
            $uploadOk = 0;
        }

        // Allow only certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            // If file format is not allowed, display error message
            echo '<div class="alert alert-danger" role="alert">Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>';
            $uploadOk = 0;
        }

        // Check if upload was successful
        if ($uploadOk == 0) {
            // If upload failed, display error message
            echo '<div class="alert alert-danger" role="alert">Sorry, your file was not uploaded.</div>';
        } else {
            // If upload was successful, move the file to the target directory
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                // File uploaded successfully, update user information in the database
                $update_query = "UPDATE profile SET username = ?, first_name = ?, middle_name = ?, last_name = ?, address = ?, phone = ?, email = ?, profile_pic = ?, password = ? WHERE id = ?";
                
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("sssssssssi", $username, $first_name, $middle_name, $last_name, $address, $phone, $email, $target_file, $password, $id);
                
                // Execute the update query
                if ($stmt->execute()) {
                    // If update is successful, redirect to user profile page with success message
                    header("Location: profile.php?success=User information updated successfully.");
                    exit();
                } else {
                    // If update fails, display error message
                    echo '<div class="alert alert-danger" role="alert">Error updating user information: ' . $stmt->error . '</div>';
                }
            
            } else {
                // If file upload failed, display error message
                echo '<div class="alert alert-danger" role="alert">Sorry, there was an error uploading your file.</div>';
            }
        }
    } else {
        // If no new profile picture is uploaded, update other user information
        $update_query = "UPDATE profile SET username = ?, first_name = ?, middle_name = ?, last_name = ?, address = ?, phone = ?, email = ?, gender = ?, password = ? WHERE id = ?";
        
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssssssssi", $username, $first_name, $middle_name, $last_name, $address, $phone, $email, $gender, $password, $id);

        // Execute the update query
        if ($stmt->execute()) {
            // If update is successful, redirect to user profile page with success message
            header("Location: profile.php?success=User information updated successfully.");
            exit();
        } else {
            // If update fails, display error message
            echo '<div class="alert alert-danger" role="alert">Error updating user information: ' . $stmt->error . '</div>';
        }
    }
}
?>

<!-- HTML content for the edit user form -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Edit User</h4>
            </div>
            <div class="card-body">
                <?php
                // Display error messages here
                if (isset($error_message)) {
                    echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                    // Clear the error message to prevent it from persisting
                    unset($error_message);
                }
                ?>
                <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <!-- Input fields for updating user information -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank if not changing">
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required pattern="[A-Za-z\s]+" title="First name should only contain letters and spaces.">
                    </div>
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" class="form-control" value="<?php echo htmlspecialchars($user['middle_name']); ?>" pattern="[A-Za-z\s]*" title="Middle name should only contain letters and spaces.">
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required pattern="[A-Za-z\s]+" title="Last name should only contain letters and spaces.">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" name="gender" class="form-control" required>
                            <option value="Male" <?php if ($user['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if ($user['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                            <option value="Other" <?php if ($user['gender'] === 'Other') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="profile_pic" class="form-label">Profile Picture</label>
                        <input type="file" id="profile_pic" name="profile_pic" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea id="address" name="address" class="form-control" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required pattern="\d+" title="Phone number should only contain numbers.">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="profile.php" class="btn btn-primary">BACK</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    // Get values from input fields
    var firstName = document.getElementById('first_name').value;
    var middleName = document.getElementById('middle_name').value;
    var lastName = document.getElementById('last_name').value;
    var phone = document.getElementById('phone').value;

    // Define regular expression patterns for validation
    var namePattern = /^[A-Za-z\s]+$/; // Allows only letters and spaces
    var phonePattern = /^\d+$/; // Allows only digits (numbers)

    // Validate first name
    if (!namePattern.test(firstName)) {
        alert('First name should only contain letters and spaces.');
        return false; // Return false to prevent form submission
    }

    // Validate middle name if provided
    if (middleName && !namePattern.test(middleName)) {
        alert('Middle name should only contain letters and spaces.');
        return false; // Return false to prevent form submission
    }

    // Validate last name
    if (!namePattern.test(lastName)) {
        alert('Last name should only contain letters and spaces.');
        return false; // Return false to prevent form submission
    }

    // Validate phone number
    if (!phonePattern.test(phone)) {
        alert('Phone number should only contain numbers.');
        return false; // Return false to prevent form submission
    }

    // If all validations pass, return true to allow form submission
    return true;
}
</script>


<?php
// Include footer file
include('includes/footer.php');
?>
