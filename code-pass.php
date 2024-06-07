<?php
session_start();

include('includes/db-conn.php');

// Check if the user is logged in
if (isset($_SESSION['id'])) {
    // Retrieve user ID from session and ensure it's an integer
    $user_id = intval($_SESSION['id']);

    // Retrieve current password from the form
    $current_password = $_POST['current_password'];

    // Retrieve new password from the form
    $new_password = $_POST['new_password'];

    // Retrieve confirm password from the form
    $confirm_password = $_POST['confirm_password'];

    // Prepare SQL statement to retrieve the current password from the database
    $query = "SELECT password FROM profile WHERE id =?";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if query is successful and user data is found
    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch user details
        $user = mysqli_fetch_assoc($result);

        // Verify if the current password matches the one in the database
        if (password_verify($current_password, $user['password'])) {
            // Check if the new password matches the confirm password
            if ($new_password === $confirm_password) {
                // Check if the new password is different from the current password
                if ($new_password!== $current_password) {
                    // Retrieve old passwords from the passwords table
                    $old_passwords_query = "SELECT password FROM passwords1 WHERE user_id =?";
                    $old_passwords_stmt = mysqli_prepare($conn, $old_passwords_query);
                    mysqli_stmt_bind_param($old_passwords_stmt, "i", $user_id);
                    mysqli_stmt_execute($old_passwords_stmt);
                    $old_passwords_result = mysqli_stmt_get_result($old_passwords_stmt);

                    $is_old_password = false;
                    while ($old_password = mysqli_fetch_assoc($old_passwords_result)) {
                        if (password_verify($new_password, $old_password['password'])) {
                            $is_old_password = true;
                            break;
                        }
                    }

                    if (!$is_old_password) {
                        // Hash the new password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                        // Insert the new password into the passwords table
                        $insert_query = "INSERT INTO passwords1 (user_id, password) VALUES (?,?)";
                        $insert_stmt = mysqli_prepare($conn, $insert_query);
                        mysqli_stmt_bind_param($insert_stmt, "is", $user_id, $hashed_password);
                        mysqli_stmt_execute($insert_stmt);

                        // Update the user's password in the users table
                        $update_query = "UPDATE profile SET password =? WHERE id =?";
                        $update_stmt = mysqli_prepare($conn, $update_query);
                        mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $user_id);
                        mysqli_stmt_execute($update_stmt);

                        // Close the statements
                        mysqli_stmt_close($insert_stmt);
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