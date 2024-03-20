<?php
session_start();
require_once 'vendor/autoload.php';
include('includes/db-conn.php');

// Google API credentials
$clientID = '204720556534-rq0o9l4nkodtj6cpbj8ook0spq7v8uds.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUrl = 'http://localhost/laboratory5.php/admindashboard/labact.php/admin/index.php';

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUrl);
$client->addScope('profile');
$client->addScope('email');

// Check if user is attempting to login via Google
if (isset($_GET['code']) && isset($_GET['redirect_uri'])) {
    $redirect_uri = $_GET['redirect_uri'];
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Get user information from Google
    $googleOAuth = new Google_Service_Oauth2($client);
    $googleInfo = $googleOAuth->userinfo->get();
    $email = $googleInfo->email;
    $name = $googleInfo->name;

    // Check if the user exists in your database
    $query = "SELECT * FROM user_profile WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        // Set the session variable and redirect to index.php
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['user_id'];
        header("Location: $redirect_uri");
        exit();
    } else {
        // User doesn't exist, create a new user account
        $query = "INSERT INTO user_profile (email, name) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $email, $name);
        mysqli_stmt_execute($stmt);

        // Get the user ID of the newly created user
        $userID = mysqli_insert_id($conn);
        $_SESSION['user_id'] = $userID;
        header("Location: $redirect_uri");
        exit();
    }
}

// Display the Google Sign-In button
echo '<a href="' . $client->createAuthUrl() . '"><img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Sign in with Google" /></a>';
?>
