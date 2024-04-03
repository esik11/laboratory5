<?php
session_start();
require_once 'vendor/autoload.php';
include ('includes/db-conn.php');

$fb = new Facebook\Facebook([
    'app_id' => '', // your app id
    'app_secret' => '', // your app secret
    'default_graph_version' => 'v2.4',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // optional

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
$fbgender = isset($userNode['gender']) ? $userNode['gender'] : 'Unknown'; // Check if gender exists
$fbaddress = isset($userNode['address']) ? $userNode['address'] : 'Unknown'; // Check if address exists

// Set default values for phone
$fbphone = 'Unknown';

// You can also attempt to fetch phone from additional fields if available
if ($userNode->getField('phone')) {
    $fbphone = $userNode->getField('phone');
}

$fbpic = "<img src='https://graph.facebook.com/$fbid/picture?redirect=true'>";

// Store user data in session variables
$_SESSION['fb_id'] = $fbid;
$_SESSION['fb_name'] = $fbfullname;
$_SESSION['fb_email'] = $fbemail;
$_SESSION['gender'] = $fbgender;
$_SESSION['phone'] = $fbphone;
$_SESSION['address'] = $fbaddress;

        // Check if the user's data exists in the database
        $query = "SELECT * FROM profile WHERE fb_id = '" . mysqli_real_escape_string($conn, $fbid) . "'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 0) {
            // If the user's data doesn't exist, insert it
            $query = "INSERT INTO profile (fb_id, full_name, email, gender, phone, address) VALUES ('$fbid', '$fbfullname', '$fbemail', '$fbgender', '$fbphone', '$fbaddress')";
            mysqli_query($conn, $query);
        }

        // Redirect to the profile page
        header('Location: profile.php');
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
    <!DOCTYPE html>
    <html>
    <head>
        <title>Facebook Login Form</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
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
                <center><a href="<?= $loginUrl; ?>" class="btn btn-primary btn-block"><i class="fab fa-facebook-square"></i> Log in with Facebook!</a></center>
            </div>
        </div>
    </body>
    </html>
<?php
}
?>
