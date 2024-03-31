<?php
require_once 'config.php';

if (isset($_SESSION['user_token'])) {
  header("Location: http://localhost/laboratory5.php/laboratory5.php/laboratory5/index.php");
} else {
  echo "<a href='" . $client->createAuthUrl() . "'>Google Login</a>";
}
