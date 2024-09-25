<?php
// Start the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// JavaScript alert after successful logout
echo '<script>alert("You have successfully logged out.");</script>';
echo '<script>window.location.href = "home.php";</script>';

exit();
?>
