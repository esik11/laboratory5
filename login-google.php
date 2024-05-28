<?php

// Include Google OAuth PHP library
require_once 'vendor/autoload.php';

// Start a session
session_start();

// Initialize Google Client
$client = new Google_Client();
$client->setClientId('261596654543-g79jvsumotns0c0frjb14cjb3s29nllu.apps.googleusercontent.com'); // Your Google Client ID
$client->setClientSecret('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'); // Your Google Client Secret
$client->setRedirectUri('http://localhost/laboratory5.php/laboratory5.php/laboratory5/login-google.php'); // Redirect URI after login
$client->addScope('profile'); // Request access to user's profile information
$client->addScope('email'); // Request access to user's email address

if (isset($_GET['code'])) {
    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Get user information from Google
    $gauth = new Google_Service_Oauth2($client);
    $google_info = $gauth->userinfo->get();
    $email = $google_info->email;
    $name = $google_info->name;

    // Initialize Database Connection
    $db_host = 'localhost';
    $db_username = 'root';
    $db_password = '';
    $db_name = 'ipt101';

    $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Insert user data into the database
    $query = "INSERT INTO profile (full_name, email) VALUES ('$name', '$email')";
    if (mysqli_query($conn, $query)) {
        // Set user ID in session
        $_SESSION['id'] = mysqli_insert_id($conn);

        // Redirect to landing page
        header("Location: landingpage.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
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
