<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require ('db_connect.php'); // Connect to the db.
    
    $errors = array(); // Initialize an error array.
    
    // Check for a first name:
    if (empty($_POST['first_name'])) {
        $errors[] = 'You forgot to enter your first name.';
    } else {
        $fn = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
    }
    
    // Check for a last name:
    if (empty($_POST['last_name'])) {
        $errors[] = 'You forgot to enter your last name.';
    } else {
        $ln = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
    }
    
    // Check for gender:
    if (isset($_POST['gender'])) {
        $gender = mysqli_real_escape_string($dbc, $_POST['gender']);
    } else {
        $errors[] = 'You forgot to select your gender.';
    }
    
    // Check for an email address:
    if (empty($_POST['email'])) {
        $errors[] = 'You forgot to enter your email.';
    } else{
        $echeck = mysqli_real_escape_string($dbc, trim($_POST['email']));
        // Check if the email is already in the database:
        $check_email_query = "SELECT userID FROM user WHERE email = '$echeck'";
        $email_result = mysqli_query($dbc, $check_email_query);
        
        if (mysqli_num_rows($email_result) > 0) {
            $errors[] = 'This email address is already registered. Please use a different email.';
        }
        else{
            $e = $echeck;
        }
    }
    
    // Check for a password and match against the confirmed password:
    if (!empty($_POST['password'])){
        if($_POST['password'] != $_POST['confirm-password']){
            $errors[] = 'Your password did not match the confirmed password.';
        }else{
            $p = mysqli_real_escape_string($dbc, trim($_POST['password']));
        }
    }else{
        $errors[] = 'You forgot to enter your password.';
    }
    
    // Check for phone number:
    $mobile_pattern = "/^(01[^1,5]\-\d{7}|01[1,5]\-\d{8})$/";
    if (empty($_POST['phone_number'])) {
        $errors[] = 'You forgot to enter your phone number.';
    } else {
        $pcheck = mysqli_real_escape_string($dbc, trim($_POST['phone_number']));
        
        // Check if the phone number is already in the database:
        $check_phone_query = "SELECT userID FROM user WHERE phone = '$pcheck'";
        $phone_result = mysqli_query($dbc, $check_phone_query);
        
        if (mysqli_num_rows($phone_result) > 0) {
            $errors[] = 'This phone number is already registered. Please use a different phone number.';
        }
        else if (preg_match($mobile_pattern,$pcheck)){
            $phone = $pcheck;
        }else{
            $errors[]= 'The phone number format is incorrect.';
        }
    }
    
    // Check for location:
    if (empty($_POST['location'])) {
        $errors[] = 'You forgot to select your location.';
    } else {
        $location = mysqli_real_escape_string($dbc, trim($_POST['location']));
    }
    
    if (empty($errors)) { // If everything's OK.
        
        // Register the user in the database...
        
        // Make the query:
        $q = "INSERT INTO user (f_name, l_name, gender, email, password, phone, location, admin, registerDate) VALUES ('$fn', '$ln', '$gender', '$e', SHA1('$p'), '$phone', '$location', '0', NOW() )";
        $r = @mysqli_query ($dbc, $q); // Run the query.
        if ($r) { // If it ran OK.
            
            // Display success message using JavaScript alert:
            echo '<script>alert("Thank you! You are now registered successfully.");</script>';
            echo '<script>window.location.href = "sign_in.php";</script>';
            
        } else { // If it did not run OK.
            
            // Display error message using JavaScript alert:
            echo '<script>alert("System Error: You could not be registered due to a system error. We apologize for any inconvenience.");</script>';
            
        } // End of if ($r) IF.
        
        mysqli_close($dbc); // Close the database connection.
        
        exit();
        
    } else { // Report the errors.
        
        // Display error messages using JavaScript alert:
        echo '<script>alert("Error: ' . implode('\n', $errors) . ' Please try again.");</script>';
        
    } // End of if (empty($errors)) IF.
    
    mysqli_close($dbc); // Close the database connection.
    
} // End of the main Submit conditional.
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/sign_up.css">
    <link rel="icon" type="images/x-icon" href="images/webicon.png">
</head>
<body>

<div class="signup-container">
    <div class="form-container">
        <div class="logo-container">
            <a href="home.php"><img src="images/logo.png" alt="Sunride Logo" class="logo"></a>  
        </div>
        <h2>Sign Up</h2>
        <form action="sign_up.php" method="post">
    		<div class="input-group">
        		<input type="text" id="first_name" name="first_name" placeholder="First Name" value="<?php if (isset($_POST['first_name'])) echo ($_POST['first_name']); ?>">
   	     		<input type="text" id="last_name" name="last_name" placeholder="Last Name" value="<?php if (isset($_POST['last_name'])) echo ($_POST['last_name']); ?>">
    		</div>
    		<div class="gender-group">
        		<label>Gender:</label>
        		<input type="radio" id="male" name="gender" value="male" <?php if (isset($_POST['gender']) && $_POST['gender'] === 'male') echo 'checked'; ?>>
        		<label for="male">Male</label>
        		<input type="radio" id="female" name="gender" value="female" <?php if (isset($_POST['gender']) && $_POST['gender'] === 'female') echo 'checked'; ?>>
        		<label for="female">Female</label>
    		</div>
    		<input type="email" id="email" name="email" placeholder="Email Address" value="<?php if (isset($_POST['email'])) echo ($_POST['email']); ?>">
    		<input type="password" id="password" name="password" placeholder="Password" value="<?php if (isset($_POST['pass1'])) echo $_POST['pass1']; ?>">
    		<input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" value="<?php if (isset($_POST['pass2'])) echo $_POST['pass2']; ?>">
    		<input type="text" id="phone_number" name="phone_number" placeholder="Phone Number eg: 01x-xxxxxxxx" class="textinput" value="<?php if (isset($_POST['phone_number'])) echo ($_POST['phone_number']); ?>">
    		<select id="location" name="location">
        		<option value="">Select Location</option>
        		<?php
                
        		require("db_connect.php");
                $q = "SELECT locID, name FROM location";
                $result = mysqli_query($dbc, $q);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $row['locID'] . '">' . $row['name'] . '</option>';
                }

                // Close the database connection
                mysqli_close($dbc);
                ?>
    		</select>
    		<button type="submit" class="confirm-button">CONFIRM</button>
		</form>

    </div>
</div>

</body>
</html>