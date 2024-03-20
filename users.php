<?php
include('includes/header.php'); 
include('includes/topbar.php'); 
include('includes/sidebar.php'); 
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">USER PROFILE</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <?php
                            include('includes/db-conn.php');

                            // fetching profile from db
                            $query = "SELECT profile_pic FROM user_profile";
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);
                                $profile_pic = $row['profile_pic'];
                            } else {
                                $profile_pic = 'default_profile_pic.jpg'; // Default profile picture path
                            }
                            ?>

                            <!-- profile pic display -->
                            <img src="<?php echo $profile_pic; ?>" class="img-fluid rounded-circle" style="width: 150px; height: 150px;" alt="Profile Picture">
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        <?php
                                        // Fetch users from database
                                        $query = "SELECT * FROM user_profile";
                                        $result = mysqli_query($conn, $query);

                                        // Check if there are users
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                                    <tr>
                                                        <td><strong>Name:</strong> <?php echo $row['full_name']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Email:</strong> <?php echo $row['email']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Password:</strong> <?php echo $row['password']; ?></td>
                                                    </tr>
                                                    <tr>
                                                    <td><strong>Firstname:</strong> <?php echo $row['firstname']; ?></td>
                                                    <tr>
                                                    </tr>
                                                    <td><strong>MiddleName:</strong> <?php echo $row['middlename']; ?></td>
                                                    <tr>
                                                    </tr>
                                                    <td><strong>Lastname:</strong> <?php echo $row['lastname']; ?></td>
                                                    <tr>
                                                    </tr>
                                                    <td><strong>Address:</strong> <?php echo $row['address']; ?></td>
                                                    <tr>
                                                    </tr>
                                                    <td><strong>Phone Number:</strong> <?php echo $row['phone_number']; ?></td>
                                                    <tr>
                                                    </tr>  
                                                    
                                                        <td>
                                                            <a href='users-edit.php?id=<?php echo $row['user_id']; ?>' class='btn btn-success btn-sm'>Edit</a>
                                                            <!-- You can add more actions here if needed -->
                                                        </td>
                                                    </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='5'>No users found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('includes/footer.php');
?>
