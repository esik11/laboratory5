<?php
// Start session to manage user data
session_start();

// Include header, topbar, and sidebar files for UI
include('includes/header.php');

// Include database connection script
include('includes/db-conn.php');

// Check if user is logged in
if (isset($_SESSION['id'])) {
    // Retrieve user ID from session and ensure it's an integer
    $id = intval($_SESSION['id']);

    // Prepare SQL statement with a parameterized query to prevent SQL injection
    $query = "SELECT username, email, first_name, middle_name, last_name, password, profile_pic, gender, date_of_birth, phone, address FROM profile WHERE id = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "i", $id);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if query is successful and user data is found
    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch user details
        $user = mysqli_fetch_assoc($result);
    } else {
        // Handle error if no user found with the given user ID
        // For instance, redirect the user to a login page or display an error message
    }
} else {
    // Handle the case where the user is not logged in
    // For instance, redirect the user to a login page
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Profile</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../../index3.html" class="brand-link">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">User Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="<?php echo $user['profile_pic'] ?>"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php echo $user['last_name']; ?>(<?php echo $user['username']; ?>)</h3>

                <p class="text-muted text-center">Software Engineer</p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>EMAIL:</b> <a class="float-right"><?php echo $user['email']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>ADDRESS:</b> <a class="float-right"><?php echo $user['address']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>PHONE:</b> <a class="float-right"><?php echo $user['phone']; ?></a>
                  </li>
                </ul>

                <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">About Me</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Education</strong>

                <p class="text-muted">
                  B.S. in Computer Science from the University of Tennessee at Knoxville
                </p>

                <hr>

                <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

                <p class="text-muted"><?php echo $user['address']; ?></p>

                <hr>

                <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

                <p class="text-muted">
                </p>

                <hr>

                <strong><i class="far fa-file-alt mr-1"></i> Birthday</strong>
                <p class="text-muted"><?php echo $user['date_of_birth']; ?></p>

                <p class="text-muted"></p>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#profileSettings">Profile Settings</a></li>
              <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#loginSettings">Login Settings</a></li>
            </ul>
          </div><!-- /.card-header -->
                      <div class="card-body">
    <div class="tab-content">
    <div class="tab-pane active" id="profileSettings">
    <form action="profile-update.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <!-- Input fields for updating user information -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
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
                        <option value="male" <?php echo $user['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo $user['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo $user['gender'] === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
                    <div id="dateOfBirthError" class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label for="profile_pic" class="form-label">Profile Picture</label>
                    <input type="file" id="profile_pic" name="profile_pic" class="form-control" value="<?php echo htmlspecialchars($user['profile_pic']); ?>">
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea id="address" name="address" class="form-control" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required pattern="\d{11}" title="Phone number must be exactly 11 digits.">
                      </div>
                        </div>
                <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="user-profile.php" class="btn btn-primary">BACK</a>
                        <a href="logout.php" class="btn btn-primary">logout</a>
                    </div>
                </div>
            </form>
      
                  <div class="tab-pane" id="loginSettings">
    <form id="passwordForm" action="code-pass.php" method="POST" onsubmit="return validatePasswordForm()">
        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" id="current_password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
                <div id="error_message" class="text-danger"></div>
                <button type="submit" class="btn btn-primary">Change Password</button>
                <a href="user-profile.php" class="btn btn-primary">BACK</a>
            </div>
        </div>
    </form>
    <div id="message-container"></div>
</div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="assets/dist/js/demo.js"></script>
<script>
$(document).ready(function(){
    // Hide all tab panes except the active one
    $('.tab-pane').not('.active').hide();

    // Handle tab clicks to show the corresponding tab pane
    $('.nav-link').on('click', function(e) {
        e.preventDefault(); // Prevent default behavior of tab links
        var target = $(this).attr('href');
        $('.tab-pane').hide(); // Hide all tab panes
        $(target).show(); // Show the clicked tab pane
        $('.nav-link').removeClass('active'); // Remove 'active' class from all tab links
        $(this).addClass('active'); // Add 'active' class to the clicked tab link
    });
});
</script>
<script>
document.getElementById('phone').addEventListener('input', function (e) {
    var x = e.target.value.replace(/\D/g, '');
    e.target.value = x.slice(0, 11);
});

function validateForm() {
    var phoneNumberInput = document.getElementById('phone');
    var phoneNumber = phoneNumberInput.value.trim();
    
    // Check if phone number is exactly 11 digits and contains only numbers
    if (!/^\d{11}$/.test(phoneNumber)) {
        alert('Please enter a valid phone number with exactly 11 digits.');
        phoneNumberInput.focus();
        return false;
    }
    
    // All validations passed
    return true;
}
</script>


<script>
    // Function to validate date of birth
    function validateForm() {
        var dateOfBirthInput = document.getElementById('date_of_birth');
        var dateOfBirthError = document.getElementById('dateOfBirthError');
        var dateOfBirth = dateOfBirthInput.value;
        var currentDate = new Date().toISOString().split('T')[0]; // Get current date in 'YYYY-MM-DD' format
        var inputDate = new Date(dateOfBirth);

        // Check if input is a valid date
        if (isNaN(inputDate.getTime())) {
            dateOfBirthInput.classList.add('is-invalid');
            dateOfBirthError.textContent = "Please input a valid birth date.";
            return false;
        }

        // Check if input date is in the future
        if (dateOfBirth > currentDate) {
            dateOfBirthInput.classList.add('is-invalid');
            dateOfBirthError.textContent = "Cannot input a future date as the date of birth.";
            return false;
        }

        // Check if user is at least 13 years old
        var age = new Date(currentDate).getFullYear() - inputDate.getFullYear();
        var monthDiff = new Date(currentDate).getMonth() - inputDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && new Date(currentDate).getDate() < inputDate.getDate())) {
            age--;
        }
        if (age < 13) {
            dateOfBirthInput.classList.add('is-invalid');
            dateOfBirthError.textContent = "User must be at least 13 years old.";
            return false;
        }

        // Check if year is not before 1900
        if (inputDate.getFullYear() < 1900) {
            dateOfBirthInput.classList.add('is-invalid');
            dateOfBirthError.textContent = "Please input a valid birth year (after 1900).";
            return false;
        }

        // If all conditions are met, remove any existing error message and return true
        dateOfBirthInput.classList.remove('is-invalid');
        dateOfBirthError.textContent = "";
        return true;
    }
</script>

<script>
function validatePasswordForm() {
    var currentPassword = document.getElementById('current_password').value;
    var newPassword = document.getElementById('new_password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    var errorMessage = document.getElementById('error_message');

    if (newPassword !== confirmPassword) {
        errorMessage.textContent = 'New password and confirm password do not match.';
        return false;
    }

    // Additional client-side validation can be added here

    return true; // Allow form submission
}

document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get('success');
    const errorMessage = urlParams.get('error');
    const messageContainer = document.getElementById('message-container');
    const activeTab = urlParams.get('tab') || 'loginSettings'; // Default to loginSettings

    // Activate the specified tab
    const activeTabElement = document.querySelector(`#${activeTab}-tab`);
    if (activeTabElement) {
        activeTabElement.classList.add('active');
        document.querySelector(`#${activeTab}`).classList.add('show', 'active');
    }

    if (successMessage) {
        messageContainer.innerHTML = `<div class="alert alert-success">${successMessage}</div>`;
    }

    if (errorMessage) {
        let errorText = '';
        switch (errorMessage) {
            case 'password_mismatch':
                errorText = 'New password and confirm password do not match.';
                break;
            case 'current_password_incorrect':
                errorText = 'Current password is incorrect.';
                break;
            case 'cannot_use_old_password':
                errorText = 'New password cannot be the same as the old password.';
                break;
            case 'user_not_found':
                errorText = 'User not found.';
                break;
            default:
                errorText = 'An unknown error occurred.';
        }
        messageContainer.innerHTML = `<div class="alert alert-danger">${errorText}</div>`;
    }
});
</script>
</body>
</html>
