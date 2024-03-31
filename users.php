<?php
include ('includes/header.php');
include ('includes/db-conn.php');
include ('includes/topbar.php');
require_once 'config.php';

// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token['access_token']);

  // get profile info
  $google_oauth = new Google_Service_Oauth2($client);
  $google_account_info = $google_oauth->userinfo->get();
  $userinfo = [
    'email' => $google_account_info['email'],
    'firstname' => $google_account_info['givenName'],
    'last_name' => $google_account_info['familyName'],
    'gender' => $google_account_info['gender'],
    'full_name' => $google_account_info['name'],
    'picture' => $google_account_info['picture'],
    'verifiedEmail' => $google_account_info['verifiedEmail'],
    'token' => $google_account_info['id'],
    'address' => $google_account_info['address'],
    'phone' => $google_account_info['phone'],
  ];

  // checking if user is already exists in database
  $sql = "SELECT * FROM profile WHERE email ='{$userinfo['email']}'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    // user is exists
    $userinfo = mysqli_fetch_assoc($result);
    $token = $userinfo['token'];
  } else {
    // user is not exists
    $sql = "INSERT INTO profile (email, firstname, last_name, gender, full_name, picture, verifiedEmail, token) VALUES ('{$userinfo['email']}', '{$userinfo['firstname']}', '{$userinfo['last_name']}', '{$userinfo['gender']}', '{$userinfo['full_name']}', '{$userinfo['picture']}', '{$userinfo['verifiedEmail']}', '{$userinfo['token']}')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
      $token = $userinfo['token'];
    } else {
      echo "User is not created";
      die();
    }
}

// update verifiedEmail field in database
$sql = "UPDATE profile SET verifiedEmail = '{$userinfo['verifiedEmail']}' WHERE token = '{$token}'";
$result = mysqli_query($conn, $sql);
if (!$result) {
  echo "Failed to update verifiedEmail field in database";
  die();
}

  // save user data into session
  $_SESSION['user_token'] = $token;
} else {
  if (!isset($_SESSION['user_token'])) {
    header("Location: index.php");
    die();
  }

  // checking if user is already exists in database
  $sql = "SELECT * FROM profile WHERE token ='{$_SESSION['user_token']}'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    // user is exists
    $userinfo = mysqli_fetch_assoc($result);
  }
}

?>
</html>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">User Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                <img src="<?= $userinfo['picture'] ?>" alt="" width="90px" height="90px">
                      
                </div>

                <h3 class="profile-username text-center"><?php $userinfo['last_name']; ?></h3>

                <p class="text-muted text-center">Software Engineer</p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Name:</b> <a class="float-right"><?php echo $userinfo['firstname']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Email:</b> <a class="float-right"><?php echo $userinfo['email']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Friends</b> <a class="float-right">13,287</a>
                  </li>
                </ul>

                <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">About Me</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Education</strong>

                <p class="text-muted">
                  B.S. in Computer Science from the University of Tennessee at Knoxville
                </p>
 
                <hr>

                <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

                <p class="text-muted">Malibu, California</p>

                <hr>

                <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

                <p class="text-muted">
                  <span class="tag tag-danger">UI Design</span>
                  <span class="tag tag-success">Coding</span>
                  <span class="tag tag-info">Javascript</span>
                  <span class="tag tag-warning">PHP</span>
                  <span class="tag tag-primary">Node.js</span>
                </p>

                <hr>

                <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>

                <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
            


              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Profile Information</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
              <div class="tab-content">
                <div class="active tab-pane" id="activity">
                  <div class="post">
                    <div class="user-block">
                      <span class="username">
                      <div class="user-block">
            <span class="username">
                <a href="#">Full Name: </a>
            </span>
            <span class="description"><?php echo $userinfo['full_name']; ?></span>
            </div>
            <!-- /.user-block -->
            <p>Email: <?php echo $userinfo ['email']; ?></p>
            <p>First Name: <?php echo $userinfo['firstname']; ?></p>
            <p>Last Name: <?php echo $userinfo['last_name']; ?></p>      
            <p>ID:<?php echo $userinfo['id']; ?></p>    
            <p>tokenID: <?php echo $userinfo['token']; ?></p>
            <p>address: <?php echo $userinfo['address']; ?></p>
            <p>phone: <?php echo $userinfo['phone']; ?></p>
            <p>gender: <?php echo $userinfo['gender']; ?></p>
            <a href='user-edit.php?token=<?php echo $userinfo['token']; ?>' class='btn btn-success btn-sm'>Edit</a>
            <li>Full Name: <?php echo $userinfo['full_name']; ?></li> 
            <a href="logout.php" class="d-block">Logout</a>                
                </div>
              </div>
              </div><!-- /.card-body -->
            </div><!-- /.card -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section><!-- /.content -->
  </div><!-- /.content-wrapper -->
<?php
include('includes/footer.php');
?>
