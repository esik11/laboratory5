<html>  
<head>  
    <title>Facebook Login Form</title>  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" /> 
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous"> 
</head>
<style>
 .box
 {
  width:100%;
  max-width:400px;
  background-color:#f9f9f9;
  border:1px solid #ccc;
  border-radius:5px;
  padding:16px;
  margin:0 auto;
 }
</style>
<body> 
<?php
session_start();
require_once 'vendor/autoload.php';

$fb = new Facebook\Facebook([
'app_id' => '2171913889856711', // your app id
'app_secret' => '0f7559cb3aba35e469f8d9ea344182c3', // your app secret
'default_graph_version' => 'v2.4',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // optional
try {
if (isset($_SESSION['facebook_access_token'])) {
$accessToken = $_SESSION['facebook_access_token'];
} else {
$accessToken = $helper->getAccessToken();
}
} catch(Facebook\Exceptions\facebookResponseException $e) {
// When Graph returns an error
echo 'Graph returned an error: ' . $e->getMessage();
exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
// When validation fails or other local issues
echo 'Facebook SDK returned an error: ' . $e->getMessage();
exit;
}
if (isset($accessToken)) {
if (isset($_SESSION['facebook_access_token'])) {
$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
} else {
// getting short-lived access token
$_SESSION['facebook_access_token'] = (string) $accessToken;
// OAuth 2.0 client handler
$oAuth2Client = $fb->getOAuth2Client();
// Exchanges a short-lived access token for a long-lived one
$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
// setting default access token to be used in script
$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
}
// redirect the user to the profile page if it has "code" GET variable
if (isset($_GET['code'])) {
header('Location: profile.php');
}
// getting basic info about user
try {
$profile_request = $fb->get('/me?fields=name,first_name,last_name,email');
$profile = $profile_request->getGraphUser();
$fbid = $profile->getProperty('id');           // To Get Facebook ID
$fbfullname = $profile->getProperty('name');   // To Get Facebook full name
$fbemail = $profile->getProperty('email');    //  To Get Facebook email
$fbpic = "<img src='https://graph.facebook.com/$fbid/picture?redirect=true'>";
# save the user information in session variable
$_SESSION['fb_id'] = $fbid.'</br>';
$_SESSION['fb_name'] = $fbfullname.'</br>';
$_SESSION['fb_email'] = $fbemail.'</br>';
$_SESSION['fb_pic'] = $fbpic.'</br>';
} catch(Facebook\Exceptions\FacebookResponseException $e) {
// When Graph returns an error
echo 'Graph returned an error: ' . $e->getMessage();
session_destroy();
// redirecting user back to app login page
header("Location: ./");
exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
// When validation fails or other local issues
echo 'Facebook SDK returned an error: ' . $e->getMessage();
exit;
}
} else {
	?>
    <div class="container">  
    <div class="table-responsive">  
    <h3 align="center">Login using Facebook in PHP</h3>
    
       <?php
       $loginUrl = $helper->getLoginUrl('http://localhost/laboratory5.php/laboratory5.php/laboratory5/fb-login.php', $permissions);
       ?>
       <center><a href="<?php echo $loginUrl; ?>" class="btn btn-primary btn-block"><i class="fab fa-facebook-square"></i> Log in with Facebook!</a></center>
      </div>
      </div>
   </div>  
  </div>
<?php } ?>
</body>  
</html>