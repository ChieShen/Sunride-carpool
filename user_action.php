<?php
session_start();
require("db_connect.php");

if (isset($_SESSION['userID']) && $_SESSION['admin']) {
    // Get the action from the URL parameter
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        
        // Get the user ID from the URL parameter
        if (isset($_GET['userID'])) {
            $userID = $_GET['userID'];
            
            // Perform the action based on the provided value
            switch ($action) {
                case 'delete':
                    // Delete the user from the database
                    $query = "DELETE FROM ridecustomer WHERE rideID IN (SELECT rideID FROM rides WHERE driverID = '$userID')";
                    mysqli_query($dbc, $query);
                    
                    $query = "DELETE FROM ridecustomer WHERE customerID = '$userID'";
                    mysqli_query($dbc, $query);
                    
                    $query = "DELETE FROM rides WHERE driverID = '$userID'";
                    mysqli_query($dbc, $query);
                    
                    $query = "DELETE FROM user WHERE userID = '$userID'";
                    mysqli_query($dbc, $query);
                    
                    echo '<script>alert("User has been deleted");</script>';                    
                    break;
                case 'make_admin':
                    // Update the user's admin status in the database
                    $query = "UPDATE user SET admin = 1 WHERE userID = '$userID'";
                    mysqli_query($dbc, $query);
                    echo '<script>alert("The user has been made an admin");</script>';                   
                    break;
                default:
                    echo '<script>alert("No action has been done");</script>';              
                    exit();
            }
            
            echo '<script>window.location.href = "view_user.php";</script>';
        } else {
            echo '<script>alert("User id not found");</script>';
            echo '<script>window.location.href = "view_user.php";</script>';
            exit();
        }
    } else {
        echo '<script>alert("Action not provided");</script>';
        echo '<script>window.location.href = "view_user.php";</script>';
        exit();
    }
}
?>