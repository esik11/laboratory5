<?php

require_once 'vendor/autoload.php';



// init configuration
$clientID = '261596654543-06t17sqk6vk1d338be2r4f0kiaco81ao.apps.googleusercontent.com';
$clientSecret = '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';
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