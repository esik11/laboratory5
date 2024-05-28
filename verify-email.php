
<?php
session_start(); // Start the session to manage user data

include('includes/db-conn.php'); // Include the database connection file

if (isset($_GET['token'])) { // Check if 'token' parameter is present in the URL
    $token = $_GET['token']; // Get the token from the URL

    // Query to select user with the given token and who is not yet verified
    $verify_query = "SELECT * FROM profile WHERE verify_token=? AND verifiedEmail='0' LIMIT 1";
    $stmt = mysqli_prepare($conn, $verify_query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if there is a row returned by the query
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result); // Fetch the user data
        $id = $row['id']; // Get the user's ID

        // Update user's verification status to '1' (verified)
        $update_query = "UPDATE profile SET verifiedEmail='1' WHERE id=?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $update_query_run = mysqli_stmt_execute($stmt);

        // Check if the update query was successful
        if ($update_query_run) {
            $_SESSION['status'] = "Your account has been verified successfully!"; // Set session status message
            header("Location: login.php"); // Redirect to the login page
            exit(); // Terminate script execution
        } else {
            $_SESSION['status'] = "Verification failed!"; // Set session status message
            header("Location: login.php"); // Redirect to the login page
            exit(); // Terminate script execution
        }
    } else {
        $_SESSION['status'] = "Invalid or expired token."; // Set session status message
        header("Location: login.php"); // Redirect to the login page
        exit(); // Terminate script execution
    }
} else {
    $_SESSION['status'] = "Token not found."; // Set session status message
    header("Location: login.php"); // Redirect to the login page
    exit(); // Terminate script execution
}
?>