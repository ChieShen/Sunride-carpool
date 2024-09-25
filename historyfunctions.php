<?php
require ("db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $userID = $_POST['userID'];
    $rideID = $_POST['rideID'];
    $fees = $_POST['fees'];
    $driverID = $_POST['driverID'];    
    $button = $_POST['button'];
    
    switch ($button){
        case 'arrived':
            updatearrived($dbc,$userID,$rideID,$fees,$driverID);
            break;
        case 'transported':
            updatecomplete($dbc,$rideID,$driverID);
            break;
        case 'cancelOffer':
            removeride($dbc,$rideID);
            break;
        case 'cancelJoin':
            removejoin($dbc,$rideID,$userID);
    }
}

function updatearrived ($dbc,$userID,$rideID,$fees,$driverID){
    $cusPointq = "SELECT rewardPoints FROM user WHERE userID = $userID";
    $cusPointr = mysqli_query($dbc, $cusPointq);
    $row = mysqli_fetch_assoc($cusPointr);
    $inicusPoint = $row['rewardPoints'];
    
    $driPointq = "SELECT rewardPoints FROM user WHERE userID = $driverID";
    $driPointr = mysqli_query($dbc, $driPointq);
    $row = mysqli_fetch_assoc($driPointr);
    $inidriPoint = $row['rewardPoints'];
    
    $newcusPoint = $inicusPoint + $fees;
    $newdriPoint = $inidriPoint + $fees;
    
    $updateQuery = "UPDATE ridecustomer SET arrived = 1 WHERE rideID = $rideID AND customerID = $userID";
    $updateQuery .= "; UPDATE user SET rewardPoints = $newcusPoint WHERE userID = $userID";
    $updateQuery .= "; UPDATE user SET rewardPoints = $newdriPoint WHERE userID = $driverID";
    mysqli_multi_query($dbc, $updateQuery);
    
    if (mysqli_affected_rows($dbc) > 0) {
        echo '<script>alert("Thank you! Ride has been updated.");</script>';
        echo '<script>window.location.href = "history.php";</script>';
    } else {
        echo '<script>alert("Failed to update ride, please try again later.");</script>';
        echo '<script>window.location.href = "history.php";</script>';
    }
}

function updatecomplete ($dbc,$rideID,$driverID){
    $updateQuery = "UPDATE rides SET complete = 1 WHERE rideID = $rideID AND driverID = $driverID";
    mysqli_query($dbc,$updateQuery);
    
    if (mysqli_affected_rows($dbc) > 0) {
        echo '<script>alert("Thank you for transporting the passenger(s)!");</script>';
        echo '<script>window.location.href = "history.php";</script>';
    } else {
        echo '<script>alert("Failed to update ride, please try again later.");</script>';
        echo '<script>window.location.href = "history.php";</script>';
    }
}

function removeride ($dbc,$rideID){
    $deleteRideCustomerQuery = "DELETE FROM rideCustomer WHERE rideID = $rideID";
    mysqli_query($dbc, $deleteRideCustomerQuery);
    
    // Remove row from ride table
    $deleteRideQuery = "DELETE FROM rides WHERE rideID = $rideID";
    mysqli_query($dbc, $deleteRideQuery);
    
    if (mysqli_affected_rows($dbc) > 0) {
        echo '<script>alert("Offer has been canceled.");</script>';
        echo '<script>window.location.href = "history.php";</script>';
    } else {
        echo '<script>alert("Failed to update ride, please try again later.");</script>';
        echo '<script>window.location.href = "history.php";</script>';
    }
}

function removejoin ($dbc,$rideID,$userID){
    $deleteQuery = "DELETE FROM rideCustomer WHERE rideID = $rideID AND customerID = $userID";
    mysqli_query($dbc, $deleteQuery);
    
    if (mysqli_affected_rows($dbc) > 0) {
        echo '<script>alert("You have successfully stopped joining this ride");</script>';
        echo '<script>window.location.href = "history.php";</script>';
    } else {
        echo '<script>alert("Failed to update ride, please try again later.");</script>';
        echo '<script>window.location.href = "history.php";</script>';
    }
}
?>
