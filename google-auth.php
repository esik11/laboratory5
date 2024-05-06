<?php

require_once 'vendor/autoload.php';

session_start();

// init configuration
$clientID = '261596654543-lc37q20rp00g40pk7ugsgtl1dl5p38qk.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/laboratory5.php/laboratory5.php/laboratory5/landingpage.php';

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Connect to database
$hostname = "localhost";
$username = "root";
$password = "";
$database = "ipt101";

$conn = mysqli_connect($hostname, $username, $password, $database);