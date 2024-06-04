<?php
session_start();

include('includes/db-conn.php');

// Check if the user is logged in
if (isset($_SESSION['id'])) {
    // Retrieve user ID from session and ensure it's an integer
    $id = intval($_SESSION['id']);

    // Retrieve current password from the form
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';

    // Retrieve new password from the form
    $new_password = $_POST['new_password'];

    // Retrieve confirm password from the form
    $confirm_password = $_POST['confirm_password'];

    // Prepare SQL statement to retrieve the current password from the database
    $query = "SELECT password FROM profile WHERE id = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "i", $id);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if query is successful and user data is found
    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch user details
        $user = mysqli_fetch_assoc($result);
        $db_password = $user['password'];

        // Check if the user has a password set in the database
        if (empty($db_password) || password_verify($current_password, $db_password)) {
            // Check if the new password matches the confirm password
            if ($new_password === $confirm_password) {
                // Check if the new password is different from the current password
                if ($new_password !== $current_password) {
                    // Hash the new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Prepare SQL statement to update the password in the database
                    $update_query = "UPDATE profile SET password = ? WHERE id = ?";

                    // Prepare the statement
                    $update_stmt = mysqli_prepare($conn, $update_query);

                    // Bind parameters
                    mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $id);

                    // Execute the statement
                    mysqli_stmt_execute($update_stmt);

                    // Close the statement
                    mysqli_stmt_close($update_stmt);

                    // Redirect back to the profile page with a success message
                    header("Location: profile.php?tab=loginSettings&success=Password updated successfully.");
                    exit();
                } else {
                    // Redirect back to the profile page with an error message
                    header("Location: profile.php?tab=loginSettings&error=cannot_use_old_password");
                    exit();
                }
            } else {
                // Redirect back to the profile page with an error message
                header("Location: profile.php?tab=loginSettings&error=password_mismatch");
                exit();
            }
        } else {
            // Redirect back to the profile page with an error message
            header("Location: profile.php?tab=loginSettings&error=current_password_incorrect");
            exit();
        }
    } else {
        // Redirect back to the profile page with an error message
        header("Location: profile.php?tab=loginSettings&error=user_not_found");
        exit();
    }
} else {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}
?>
