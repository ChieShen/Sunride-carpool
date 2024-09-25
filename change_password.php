<?php
session_start();
require_once("session_check.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require('db_connect.php'); // Connect to the db.
    
    $errors = array(); // Initialize an error array.
    
    // Check for the current password:
    if (empty($_POST['current_password'])) {
        $errors[] = 'You forgot to enter your current password.';
    } else {
        $p = mysqli_real_escape_string($dbc, trim($_POST['current_password']));
    }
    
    // Check for a new password and match against the confirmed password:
    if (!empty($_POST['new_password'])) {
        if ($_POST['new_password'] != $_POST['confirm_password']) {
            $errors[] = 'Your new password did not match the confirmed password.';
        } else {
            $np = mysqli_real_escape_string($dbc, trim($_POST['new_password']));
        }
    } else {
        $errors[] = 'You forgot to enter your new password.';
    }
    
    if (empty($errors)) { // If everything's OK.
        // Check that they've entered the right password:
        $userID = $_SESSION['userID']; // Assuming you have the userID stored in the session.
        $q = "SELECT userID FROM user WHERE (userID='$userID' AND password=SHA1('$p'))";
        $r = @mysqli_query($dbc, $q);
        $num = @mysqli_num_rows($r);
        if ($num == 1) { // Match was made.
            // Make the UPDATE query:
            $q = "UPDATE user SET password=SHA1('$np') WHERE userID='$userID'";
            $r = @mysqli_query($dbc, $q);
            
            if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
                // Print a success message.
                echo '<script>alert("Your password has been successfully changed.");</script>';
                echo '<script>window.location.href = "home.php";</script>';
            } else { // If it did not run OK.
                // Public message:
                echo '<script>alert("New password cannot be the same as the old password.");</script>';
                echo '<script>window.location.href = "change_password.php";</script>';                
            }
            
            mysqli_close($dbc); // Close the database connection.
            
            exit();
        } else { // Invalid password.
            echo '<script>alert("Current password entered is incorrect.");</script>';
            echo '<script>window.location.href = "change_password.php";</script>';
        }
    } else { // Report the errors.
        echo '<script>alert("Error: ' . implode('\n', $errors) . ' Please try again.");</script>';
    } // End of if (empty($errors)) IF.
    
    mysqli_close($dbc); // Close the database connection.
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="css/sign_up.css">
    <link rel="icon" type="images/x-icon" href="images/webicon.png">
</head>
<body>
    <div class="signup-container">
        <div class="form-container">
            <div class="logo-container">
                <a href="home.php"><img src="images/logo.png" alt="Sunride Logo" class="logo"></a>  
            </div>
            <h2>Change Password</h2>
            <form action="change_password.php" method="post">
                <input type="password" name="current_password" placeholder="Current Password">
                <input type="password" name="new_password" placeholder="New Password">
                <input type="password" name="confirm_password" placeholder="Confirm Password">
                <button type="submit" class="confirm-button">CONFIRM</button>
            </form>
        </div>
    </div>
</body>
</html>