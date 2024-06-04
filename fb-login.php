<?php
// Start a PHP session to persist data between page requests
session_start();

// Include the Facebook SDK
require_once 'vendor/autoload.php';

// Include database connection file
include ('includes/db-conn.php');

// Initialize Facebook SDK with app credentials
$fb = new Facebook\Facebook([
    'app_id' => '743970277887237', // your app id
    'app_secret' => '7e8d1f975a30010550a191d9a02da0ba', // your app secret
    'default_graph_version' => 'v2.4',
]);

// Get the Facebook redirect login helper
$helper = $fb->getRedirectLoginHelper();

// Define required permissions
$permissions = ['email']; // optional

// Check if there's an email exists error in the session
if (isset($_SESSION['email_exists_error'])) {
    // Display error message if it exists
    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['email_exists_error'] . '</div>';
    // Remove the error message from session after displaying
    unset($_SESSION['email_exists_error']);
}

try {
    // Attempt to get the access token from the Facebook redirect
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

if (isset($accessToken)) {
    // Store the access token in the session
    $_SESSION['facebook_access_token'] = (string) $accessToken;

    // Set the default access token for subsequent requests
    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);

    try {
        // Get the user's profile data
        $response = $fb->get('/me?fields=id,name,email,gender,address');
        $userNode = $response->getGraphUser();

        // Extract user data
        $fbid = $userNode->getId();
        $fbfullname = $userNode->getName();
        $fbemail = $userNode->getEmail();
 
        // You can also attempt to fetch phone from additional fields if available
       

        // Generate the Facebook profile picture URL
        $fbpic = "<img src='https://graph.facebook.com/$fbid/picture?redirect=true'>";

        // Store user data in session variables
        $_SESSION['fb_id'] = $fbid;
        $_SESSION['fb_name'] = $fbfullname;
        $_SESSION['fb_email'] = $fbemail;
        $_SESSION['gender'] = $fbgender;
        $_SESSION['phone'] = $fbphone;
        $_SESSION['address'] = $fbaddress;
        
        // Assuming you have a session variable called 'user_id'
        $user_id = $_SESSION['id']; 

        // Check if the email already exists in the database
        $query = "SELECT * FROM profile WHERE email = '" . mysqli_real_escape_string($conn, $fbemail) . "'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // If the email already exists, set an error message
            $_SESSION['email_exists_error'] = 'EMAIL IS ALREADY BEEN USED. PLEASE USE ANOTHER EMAIL.';
            // Redirect back to fb-login.php with JavaScript to display the error message
            header("Location: fb-login.php");
            exit;
        }

        // Check if the user's data exists in the database
        $query = "SELECT * FROM profile WHERE fb_id = '" . mysqli_real_escape_string($conn, $fbid) . "'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 0) {
            // If the user's data doesn't exist, insert it
            $query = "INSERT INTO profile (fb_id, full_name, email, gender, phone, address) VALUES ('$fbid', '$fbfullname', '$fbemail', '$fbgender', '$fbphone', '$fbaddress')";
            mysqli_query($conn, $query);
        }

        // Get the user ID from the database
        $query = "SELECT id FROM profile WHERE fb_id = '" . mysqli_real_escape_string($conn, $fbid) . "'";
        $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $user_id = $row['id'];
            // Store user ID in session variable
            $_SESSION['id'] = $user_id;
        }

        // Redirect to the profile page
        header('Location: landingpage.php');
        exit;

    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // Graph API error occurred
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // Facebook SDK error occurred
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
} else {
    // Generate Facebook login URL
    $loginUrl = $helper->getLoginUrl('http://localhost/laboratory5.php/laboratory5.php/laboratory5/fb-login.php', $permissions);
?>
<!-- HTML for login form -->
<!DOCTYPE html>
<html>
<head>
    <title>Facebook Login Form</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <!-- Custom CSS -->
    <style>
        .box {
            width:100%;
            max-width:400px;
            background-color:#f9f9f9;
            border:1px solid #ccc;
            border-radius:5px;
            padding:16px;
            margin:0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="table-responsive">
            <h3 align="center">Login using Facebook in PHP</h3>
            <!-- Facebook login button -->
            <center><a href="<?= $loginUrl; ?>" class="btn btn-primary btn-block"><i class="fab fa-facebook-square"></i> Log in with Facebook!</a></center>
            <!-- Link to regular login page -->
            <a href="login.php" class="btn btn-danger primary">Back to login</a>
        </div>
    </div>
</body>
</html>
<?php
}
?>
