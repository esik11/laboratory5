<?php
session_start();
include ('includes/header.php');
include ('includes/db-conn.php');
include ('includes/topbar.php');
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
                <img src="<?= $_SESSION['fb_pic'] ?>" alt="" width="90px" height="90px">
                      
                </div>

                <h3 class="profile-username text-center"><?php echo $_SESSION['fb_name']; ?></h3>

                <p class="text-muted text-center">Software Engineer</p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Name:</b> <a class="float-right"><?php echo $_SESSION['fb_name']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Email:</b> <a class="float-right"><?php echo $_SESSION['fb_email']; ?></a>
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
                  <span class="tag tag-info">Javascript</span><span class="tag tag-warning">PHP</span>
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
                <span class="description"><?php echo $_SESSION['fb_name']; ?></span>
            </div>
            <!-- /.user-block -->
                  <p>FB ID: <?php echo $_SESSION['fb_id'] ; ?></p>
      <p>FB EMAIL: <?php echo $_SESSION['fb_email']; ?></p>
      <p>FB NAME: <?php  echo $_SESSION['fb_name']; ?></p>    
      <p>FB PHONE: <?php echo $_SESSION['phone']; ?></p>
      <p>FB GENDER: <?php echo $_SESSION['gender']; ?></p>
      <p>FB ADDRESS: <?php  echo  $_SESSION['address']; ?></p>    
      <a href='fb-user-edit.php?fb_id=<?php echo $_SESSION['fb_id']; ?>' class='btn btn-success btn-sm'>Edit</a>
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