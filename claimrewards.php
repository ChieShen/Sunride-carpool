<?php
session_start();
require_once("session_check.php");
require("db_connect.php");

if (isset($_GET['reward_id'])) {
    $rewardID = $_GET['reward_id'];
    
    // Retrieve the selected reward from the database
    $query = "SELECT * FROM rewards WHERE rwdID = '$rewardID'";
    $result = mysqli_query($dbc, $query);
    $reward = mysqli_fetch_assoc($result);
    
    // Check if the user has enough points to claim the reward
    $userID = $_SESSION['userID'];
    
    // Retrieve the user's reward points from the database
    $userQuery = "SELECT rewardPoints FROM user WHERE userID = '$userID'";
    $userResult = mysqli_query($dbc, $userQuery);
    $userData = mysqli_fetch_assoc($userResult);
    $userPoints = $userData['rewardPoints'];
    
    $rewardPoints = $reward['points'];
    
    if ($userPoints >= $rewardPoints) {
        // Deduct the reward points from the user
        $newPoints = $userPoints - $rewardPoints;
        $updateQuery = "UPDATE user SET rewardPoints = '$newPoints' WHERE userID = '$userID'";
        mysqli_query($dbc, $updateQuery);
        
        echo '<script>alert("You have successfully claimed the reward.");</script>';
        echo '<script>window.location.href = "reward.php";</script>';
        exit();
    } else {
        // If the user doesn't have enough points, display an error message or handle it accordingly
        echo '<script>alert("You do not have enough points to claim the reward.");</script>';
        echo '<script>window.location.href = "reward.php";</script>';
    }
} else {
    echo '<script>alert("Failed to claim reward, please try again later");</script>';
    echo '<script>window.location.href = "reward.php";</script>';
    exit();
}
?>