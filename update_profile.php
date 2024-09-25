<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require('db_connect.php'); // Connect to the database
    
    $errors = array(); // Initialize an error array
    $userID = isset($_POST['userID']) ? $_POST['userID'] : 0;
    
    // Check for a first name
    if (empty($_POST['first_name'])) {
        $errors[] = 'You forgot to enter your first name.';
    } else {
        $fn = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
    }
    
    // Check for a last name
    if (empty($_POST['last_name'])) {
        $errors[] = 'You forgot to enter your last name.';
    } else {
        $ln = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
    }
    
    // Check for gender
    if (isset($_POST['gender'])) {
        $gender = mysqli_real_escape_string($dbc, $_POST['gender']);
    } else {
        $errors[] = 'You forgot to select your gender.';
    }
    
    // Check for an email address
    if (empty($_POST['email'])) {
        $errors[] = 'You forgot to enter your email.';
    } else {
        $echeck = mysqli_real_escape_string($dbc, trim($_POST['email']));
        // Check if the email is already in the database
        $check_email_query = "SELECT userID FROM user WHERE email = '$echeck' AND userID != '$userID'";
        $email_result = mysqli_query($dbc, $check_email_query);
        
        if (mysqli_num_rows($email_result) > 0) {
            $errors[] = 'This email address is already registered. Please use a different email.';
        } else {
            $e = $echeck;
        }
    }
    
    // Check for phone number
    $mobile_pattern = "/^(01[^1,5]\-\d{7}|01[1,5]\-\d{8})$/";
    if (empty($_POST['phone_number'])) {
        $errors[] = 'You forgot to enter your phone number.';
    } else {
        $pcheck = mysqli_real_escape_string($dbc, trim($_POST['phone_number']));
        
        // Check if the phone number is already in the database
        $check_phone_query = "SELECT userID FROM user WHERE phone = '$pcheck' AND userID != '$userID'";
        $phone_result = mysqli_query($dbc, $check_phone_query);
        
        if (mysqli_num_rows($phone_result) > 0) {
            $errors[] = 'This phone number is already registered. Please use a different phone number.';
        } else if (preg_match($mobile_pattern, $pcheck)) {
            $phone = $pcheck;
        } else {
            $errors[] = 'The phone number format is incorrect.';
        }
    }
    
    // Check for location
    if (empty($_POST['location'])) {
        $errors[] = 'You forgot to select your location.';
    } else {
        $location = mysqli_real_escape_string($dbc, trim($_POST['location']));
    }
    
    if (empty($errors)) { // If everything's OK
        
        // Update the user profile in the database...
        
        // Prepare the query
        $q = "UPDATE user SET f_name = '$fn', l_name = '$ln', gender = '$gender', email = '$e', phone = '$phone', location = '$location' WHERE userID = '$userID'";
        
        $r = mysqli_query($dbc, $q); // Run the query
        
        if ($r) { // If it ran OK
            
            if ($userID == $_SESSION['userID']){
                $_SESSION['first_name'] = $fn;
                $_SESSION['last_name'] = $ln;
                $_SESSION['gender'] = $gender;
            }
            // Display success message using JavaScript alert
            echo '<script>alert("The profile has been updated successfully.");</script>';
            echo '<script>window.location.href = "edit_user.php?userID=' . $userID . '";</script>';
            
        } else { // If it did not run OK
            
            // Display error message using JavaScript alert
            echo '<script>alert("System Error: The profile could not be updated due to a system error. We apologize for any inconvenience.");</script>';
            
        }
        
        mysqli_close($dbc); // Close the database connection
        
        exit();
        
    } else { // Report the errors
        
        // Display error messages usingJavaScript alert:
        echo '<script>alert("Error: ' . implode('\n', $errors) . ' Please try again.");</script>';
        echo '<script>window.location.href = "edit_user.php?userID=' . $userID . '";</script>';
        
        
    } // End of if (empty($errors)) IF.
    
    mysqli_close($dbc); // Close the database connection
    
} // End of the main Submit conditional.
?>