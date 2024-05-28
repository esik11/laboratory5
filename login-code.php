<?php
session_start(); // Start session to manage user data across requests
include "includes/db-conn.php"; // Include the database connection file

// Check if the user is already logged in
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php"); // Redirect to the dashboard if logged in
    exit(); // Terminate script execution
}

// Check if username and password are submitted via POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Function to validate input data
    function validate($data){
        $data = trim($data); // Remove whitespace from the beginning and end of the string
        $data = stripslashes($data); // Remove backslashes (\)
        $data = htmlspecialchars($data); // Convert special characters to HTML entities
        return $data;
    }

    // Validate and sanitize username and password
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = validate($_POST['username']);
        $password = validate($_POST['password']);

        // Check if username is empty
        if (empty($username)) {
            header("Location: login.php?error=User Name is required"); // Redirect with error message
            exit(); // Terminate script execution
        } elseif (empty($password)) { // Check if password is empty
            header("Location: login.php?error=Password is required"); // Redirect with error message
            exit(); // Terminate script execution
        } else {
            // SQL query to select user based on username
            $sql = "SELECT * FROM profile WHERE username=?";
            // Prepare the statement to prevent SQL injection
            $stmt = mysqli_prepare($conn, $sql);

            // Bind parameters to the prepared statement
            mysqli_stmt_bind_param($stmt, "s", $username);

            // Execute the prepared statement
            mysqli_stmt_execute($stmt);

            // Get the result of the query
            $result = mysqli_stmt_get_result($stmt);

            // Check if only one row is returned (valid username)
            if(mysqli_num_rows($result) === 1){
                // Fetch the associative array containing user data
                $row = mysqli_fetch_assoc($result);
                // Check if the user's email is verified, or the user logged in via Facebook
                if($row['verifiedEmail'] == 1 || !empty($row['fb_id'])){
                    // Verify the entered password with the stored hashed password
                    if(password_verify($password, $row['password'])){
                        // Set session variables with user data
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['id'] = $row['id'];
                        // Redirect to home page with success message
                        header("Location: landingpage.php?message=Login successful");
                        exit(); // Terminate script execution
                    } else {
                        // Redirect with error message for incorrect password
                        header("Location: login.php?error=Incorrect Password");
                        exit(); // Terminate script execution
                    }
                } else {
                    // Redirect with error message for unverified email
                    header("Location: login.php?error=Please verify your email");
                    exit(); // Terminate script execution
                }
            } else {
                // Redirect with error message for incorrect username
                header("Location: login.php?error=Incorrect User name");
                exit(); // Terminate script execution
            }
        }
    } else {
        // Redirect to login page if username or password is not set
        header("Location: login.php");
        exit(); // Terminate script execution
    }
}
?>
