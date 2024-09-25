<?php
session_start();

if (!isset($_SESSION['userID'])) {
    echo '<script>alert("You have to log in to join a ride");</script>';
    echo '<script>window.location.href = "sign_in.php";</script>';
    exit();
}

require_once("session_check.php");

$rideID = $_GET['rideID'];
$customerID = $_SESSION['userID'];

require('db_connect.php');

// Check if the customer has already joined the ride
$checkQuery = "SELECT * FROM ridecustomer WHERE rideID = $rideID AND customerID = $customerID";
$checkResult = mysqli_query($dbc, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    // The customer has already joined the ride, show an error message or redirect back to the rides page
    echo '<script>alert("You have already joined this ride");</script>';
    echo '<script>window.location.href = "home.php";</script>';
    exit();
}

// Insert the customer and ride information into the ridecustomer table
$insertQuery = "INSERT INTO ridecustomer (rideID, customerID) VALUES ($rideID, $customerID)";
mysqli_query($dbc, $insertQuery);

// show a success message
echo '<script>alert("You have successfully joined a ride");</script>';
echo '<script>window.location.href = "home.php";</script>';
exit();

mysqli_close($dbc);
?>