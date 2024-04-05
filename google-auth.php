<?php

require_once 'vendor/autoload.php';

session_start();

// init configuration
$clientID = '261596654543-bsod7hsll25hbvg0jjmjqbil1ulon0bl.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/laboratory5.php/admindashboard/labact.php/admin/index.php';

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