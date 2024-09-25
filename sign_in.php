<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require('db_connect.php'); // Connect to the database.
    
    $errors = array(); // Initialize an error array.
    
    // Check for an email address:
    if (empty($_POST['email'])) {
        $errors[] = 'You forgot to enter your email.';
    } else {
        $e = mysqli_real_escape_string($dbc, trim($_POST['email']));
    }
    
    // Check for a password:
    if (empty($_POST['password'])) {
        $errors[] = 'You forgot to enter your password.';
    } else {
        $p = mysqli_real_escape_string($dbc, trim($_POST['password']));
    }
    
    if (empty($errors)) { // If everything's OK.
        
        // Make the query:
        $q = "SELECT userID, f_name, l_name, gender, admin FROM user WHERE email='$e' AND password=SHA1('$p')";
        $r = @mysqli_query($dbc, $q); // Run the query.
        
        if (mysqli_num_rows($r) == 1) { // If one row was returned, the login was successful.
            
            // Start the session:
            session_start();
            
            // Fetch the user information:
            $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
            
            // Set the session variables:
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['first_name'] = $row['f_name'];
            $_SESSION['last_name'] = $row['l_name'];
            $_SESSION['gender'] = $row['gender'];
            $_SESSION['admin'] = $row['admin'];
            
            //Set session timeout
            $_SESSION['timeout'] = time() + 600;
            
            echo '<script>alert("Thank you! You have successfully logged in.");</script>';
            echo '<script>window.location.href = "home.php";</script>';
            exit();
            
        } else { // If no match was found.
            echo '<script>alert("Invalid email address or password. Please try again.");</script>';
        }
        
    } else { // Report the errors.
        echo '<script>alert("Error: ' . implode('\n', $errors) . ' Please try again.");</script>';
    }
    
    mysqli_close($dbc); // Close the database connection.
    
} // End of the main Submit conditional.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/sign_in.css">
    <link rel="icon" type="images/x-icon" href="images/webicon.png">
</head>
<body>

<div class="login-container">
    <div class="login-form">
        <h2>Log in</h2>
        <p>Welcome back! Please enter your details.</p>
        <form action="sign_in.php" method="post">
            <input type="email" name="email" placeholder="Email" value="<?php if (isset($_POST['email'])) echo ($_POST['email']); ?>">
            <input type="password" name="password" placeholder="Password" value="<?php if (isset($_POST['password'])) echo $_POST['password']; ?>">
            <button type="submit" class="login-button">LOGIN</button>
            <p class="sign-up-text">Don’t have an account? <a href="sign_up.php" class="sign-up-link">Sign up</a></p>
        </form>
    </div>
    <div class="ad-section">
    	<div class="branding">
 			<div class="logo">
            	<a href="home.php"><img src="images/logo.png" alt="Sunride"></a>
        	</div>
        	<div class="right-side">
            	<div class="brand">
            		<a href="home.php" style="text-decoration: none;">
                		<span style="color: orange;">SUN</span><span style="color: black;">RIDE</span>
                	</a>
            	</div>
            	<div class="slogan">
                	Drive Together, Thrive Together
            	</div>
        	</div>
    	</div>
    	<div class="carpoolpic">
    		<img src="images/login_carpool.png">
    	</div>
	</div>
</div>
</body>
</html>
