<?php

require_once 'vendor/autoload.php';



// init configuration
$clientID = '261596654543-4d2vaialdgg55gnghhpuf7lf3bbdlevl.apps.googleusercontent.com';
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