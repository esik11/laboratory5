<?php
// Start a PHP session to manage user data.
session_start();

// Include Configuration Files
// ---------------------------
require_once 'vendor/autoload.php'; // Include Google OAuth PHP library
include 'includes/db-conn.php';     // Load database connection.

// Initialize Google Client
$client = new Google_Client();
$client->setClientId('261596654543-g79jvsumotns0c0frjb14cjb3s29nllu.apps.googleusercontent.com'); // Your Google Client ID
$client->setClientSecret('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'); // Your Google Client Secret
$client->setRedirectUri('http://localhost/laboratory5.php/laboratory5.php/laboratory5/login-google.php'); // Redirect URI after login
$client->addScope('profile'); // Request access to user's profile information
$client->addScope('email');   // Request access to user's email address

// Check if the user is being redirected back from Google after authorization.
if (isset($_GET['code'])) {
    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Get user information from Google
    $gauth = new Google_Service_Oauth2($client);
    $google_info = $gauth->userinfo->get();
    $email = $google_info->email;
    $first_name = $google_info->givenName;    // Get the user's first name from Google.
    $last_name = $google_info->familyName;    // Get the user's last name from Google.

    // Check if User Exists
    $check_user_query = "SELECT * FROM profile WHERE email = ?"; // Query to check if the email exists in the database.
    $stmt = mysqli_prepare($conn, $check_user_query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $check_user_result = mysqli_stmt_get_result($stmt);

    // Handle Existing User or New User
    if (mysqli_num_rows($check_user_result) > 0) {
        // Existing User: Log In
        $user_row = mysqli_fetch_assoc($check_user_result); // Fetch the user's data from the database.
        $_SESSION['id'] = $user_row['id'];        // Store user id in session.
        $_SESSION['username'] = $user_row['username'];      // Store username in session.

        // Redirect to landing page
        header("Location: landingpage.php");
        exit();
    } else {
        // New User: Create Account
        // Generate a random username.
        function generate_random_username() {
            $words = ['user', 'member', 'guest', 'participant'];
            $random_word = $words[array_rand($words)];
            $random_number = rand(1000, 9999);
            return $random_word . $random_number;
        }

        $username = generate_random_username(); // Generate a random username.
        $password = 'user123';                  // Default password.
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash the password before storing it.

        // Insert New User into Database
        $insert_user_query = "INSERT INTO profile (username, password, last_name, first_name, email, verifiedEmail) 
                              VALUES (?, ?, ?, ?, ?, 1)"; // Insert user data.
        $stmt = mysqli_prepare($conn, $insert_user_query);
        mysqli_stmt_bind_param($stmt, 'sssss', $username, $hashed_password, $last_name, $first_name, $email);

        if (mysqli_stmt_execute($stmt)) {
            $user_id = mysqli_insert_id($conn); // Get the ID of the newly inserted user.

            // Insert Default Password into Password History
            $insert_password_history_query = "INSERT INTO passwords1 (user_id, password, date_updated) VALUES (?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $insert_password_history_query);
            mysqli_stmt_bind_param($stmt, 'is', $user_id, $hashed_password);
            if (!mysqli_stmt_execute($stmt)) {
                echo "Error: " . mysqli_error($conn); // Display an error if the password history insert fails.
            }

            $_SESSION['id'] = $user_id;
            $_SESSION['username'] = $username;

            // Redirect to landing page
            header("Location: landingpage.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn); // Display error if user creation fails.
        }
    }
} else {
    // If no authorization code is present, display the login button
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container'>
        <div class='row justify-content-center mt-5'>
            <div class='col-md-6 text-center'>
                <!-- Display Google login button -->
                <a href='" . $client->createAuthUrl() . "' class='btn btn-danger btn-lg'>Login With Google</a>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
</body>
</html>";
}
?>
