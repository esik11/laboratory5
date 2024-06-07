<?php
session_start();

// Clear session data
$_SESSION = array();
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();
?>