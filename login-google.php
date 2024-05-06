<?php

// Include Google OAuth PHP library
require_once 'vendor/autoload.php';

// Start a session
session_start();

// Initialize Google Client
$client = new Google_Client();
$client->setClientId('');
$client->setClientSecret('');
$client->setRedirectUri('http://localhost/laboratory5.php/laboratory5.php/laboratory5/login-google.php');
$client->addScope('profile');
$client->addScope('email');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

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
    $query = "INSERT INTO profile (email, full_name) VALUES ('$email', '$name')";
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
    echo "<a href='" . $client->createAuthUrl() . "'>Login With Google</a>";
}

?>
