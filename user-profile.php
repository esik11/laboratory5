<?php
session_start();
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
include('includes/db-conn.php');
// Assuming you have stored the logged-in user's user_id in a session variable named 'user_id'
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $query = "SELECT full_name, email, password, firstname, middlename, lastname, address, phone_number, profile_pic FROM user_profile WHERE user_id = $user_id";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        // Handle error if no user found with the given user_id
        // For instance, redirect the user to a login page or display an error message
    }
} else {
    // Handle the case where the user is not logged in
    // For instance, redirect the user to a login page
    header("Location: login.php");
    exit();
}
?>


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
                  <img class="profile-user-img img-fluid img-circle"
                       src="<?php echo $user['profile_pic'] ?>"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php echo $user['lastname']; ?></h3>

                <p class="text-muted text-center">Software Engineer</p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Name:</b> <a class="float-right"><?php echo $user['firstname']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Email:</b> <a class="float-right"><?php echo $user['email']; ?></a>
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
                        <a href="#">Full Name: </a>
                      </span>
                      <span class="description"><?php echo $user['full_name']; ?></span>
                    </div>
                    <!-- /.user-block -->
                    <p>Email: <?php echo $user['email']; ?></p>
                    <p>Password: <?php echo $user['password']; ?></p>
                    <p>First Name: <?php echo $user['firstname']; ?></p>
                    <p>Middle Name: <?php echo $user['middlename']; ?></p>
                    <p>Last Name: <?php echo $user['lastname']; ?></p>
                    <p>Address: <?php echo $user['address']; ?></p>
                    <p>Phone Number: <?php echo $user['phone_number']; ?></p>           
                    <a href='users-edit.php?id=<?php echo $user_id; ?>' class='btn btn-success btn-sm'>Edit</a>

                    <a href="../../labact.php/admin/logout.php" class="d-block">Logout</a>                
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
