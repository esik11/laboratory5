<?php

require_once 'vendor/autoload.php';



// init configuration
$clientID = '';
$clientSecret = '';
$redirectUri = 'http://localhost/laboratory5.php/laboratory5.php/laboratory5/index.php';

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