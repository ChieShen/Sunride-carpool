<?php
session_start();

$loggedIn = isset($_SESSION['userID']);
$isAdmin = $loggedIn ? $_SESSION['admin'] : '';
$gender = $loggedIn ? $_SESSION['gender'] : '';
$firstName = $loggedIn ? $_SESSION['first_name'] : '';

require_once("session_check.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require('db_connect.php');
    $errors = array();
    
    // Validate form fields
    $startLoc = isset($_POST['startloc']) ? $_POST['startloc'] : '';
    $stopLoc = isset($_POST['stoploc']) ? $_POST['stoploc'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $startTime = isset($_POST['starttime']) ? $_POST['starttime'] : '';
    $duration = isset($_POST['duration']) ? $_POST['duration'] : '';
    $availableSeats = isset($_POST['avaiseat']) ? $_POST['avaiseat'] : '';
    $fare = isset($_POST['fare']) ? $_POST['fare'] : '';
    
    // Additional validation for seats (between 1 and 7)
    if (!is_numeric($availableSeats) || $availableSeats < 1 || $availableSeats > 6) {
        $errors[] = 'Number of available seats must be a number between 1 and 6.';
    }
    
    // Validate other fields (not empty, not in the past, etc.)
    if (empty($startLoc)) {
        $errors[] = 'Please select a departure location.';
    }
    
    if (empty($stopLoc)) {
        $errors[] = 'Please select a destination.';
    }
    
    if (empty($date)) {
        $errors[] = 'Please select a date.';
    }
    
    if (empty($startTime)) {
        $errors[] = 'Please select a start time.';
    }
    
    if (empty($duration)) {
        $errors[] = 'Please enter the duration.';
    }
    
    if (empty($availableSeats)) {
        $errors[] = 'Please enter the number of available seats.';
    }
    
    if (empty($fare)) {
        $errors[] = 'Please enter the fare.';
    }
    
    // Validate start and stop locations are different
    if ($startLoc == $stopLoc) {
        $errors[] = 'Start and stop locations cannot be the same.';
    }
    
    // Validate date and time not in the past
    $currentDateTime = new DateTime();
    $selectedDateTime = new DateTime("$date $startTime");
    
    if ($selectedDateTime < $currentDateTime) {
        $errors[] = 'Selected date and time cannot be in the past.';
    }
    
    $userID = $_SESSION['userID'];  
    // If there are no errors, proceed to update data in the database
    if (empty($errors)) {
        // Assuming your rides table has columns: departurelocID, destinationlocID, departureDate, departureTime, eta, seatsAvai, fees
        $insertQuery = "INSERT INTO rides (driverID, departurelocID, destinationlocID, departureDate, departureTime, eta, seatsAvai, fees)
                        VALUES ('$userID','$startLoc', '$stopLoc', '$date', '$startTime', '$duration', '$availableSeats', '$fare')";
        
        // Perform the SQL query (you might want to check for errors here)
        mysqli_query($dbc, $insertQuery);
        
        // Redirect to a success page or perform other actions
        echo '<script>alert("SUCCESS!!!");</script>';
        echo '<script>window.location.href = "history.php";</script>';
        exit();
    }
    
    // If there are errors, display them using JavaScript alert
    echo '<script>alert("' . implode('\n', $errors) . '");</script>';
    
    mysqli_close($dbc);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sunride</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/offer_ride.css">
</head>

<body>
    <div class="topbar">
        <div class="backbox">
            <a href="home.php"><img src="images/back.png">HOME/</a>
            <p>Offer A Ride</p>
        </div>
        <div class="logo">
            <img src="images/logo.png" alt="Sunride">
            <div class="slogan">
                <span style="color: #FFCFB4;">SUN</span><span style="color: black;">RIDE</span>
                <p>Drive Together, Thrive Together</p>
            </div>
        </div>
    </div>

    <div class="wrapper">
        <form method="post">
            <div class="container">
                <table>
                    <tr>
                        <td class="profilepic">
                            <img src="<?php echo 'images/' . $gender . '_avatar.png'; ?>" alt="Profile Picture">
                        </td>
                        <td> <b><?php echo $firstName; ?></b></td>
                </table>

                <table class="rideloctime">
                    <tr>
                        <td class="fromtopic" rowspan="3"><img src="images/fromto.png"></td>
                        <td>
                            <!-- Use dropdown menus for start and stop locations -->
                            <select name="startloc">
                                <option value="">Select Departure</option>
                                <!-- Fetch locations from the database and populate the options -->
                                <?php
                                require("db_connect.php");
                                $q = "SELECT locID, name FROM location";
                                $result = mysqli_query($dbc, $q);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . $row['locID'] . '">' . $row['name'] . '</option>';
                                }
                                mysqli_close($dbc);
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="date" id="date" name="date" placeholder="Date">
                            <input type="time" id="starttime" name="starttime" placeholder="Ride Begins..." style="margin-left: 25px">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <!-- Use dropdown menu for stop location -->
                            <select name="stoploc" id="stoploc">
                                <option value="">Select Destination</option>
                                <?php
                                require("db_connect.php");
                                $q = "SELECT locID, name FROM location";
                                $result = mysqli_query($dbc, $q);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . $row['locID'] . '">' . $row['name'] . '</option>';
                                }
                                mysqli_close($dbc);
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <table class="ridedetails">
                    <tr>
                        <td class="piccol"><img src="images/locationtimer.png"></td>
                        <td>
                            <input type="number" name="duration" id="duration" placeholder="No. of"> minutes
                        </td>
                    </tr>
                    <tr>
                        <td class="piccol"><img src="images/seat.png"></td>
                        <td>
                            <input type="number" id="avaiseat" name="avaiseat" placeholder="No. of"> seats available
                        </td>
                    </tr>
                    <tr>
                        <td class="piccol"><img src="images/moneysack.png"></td>
                        <td>RM <input type="number" name="fare" id="fare"></td>
                    </tr>
                </table>
            </div>
            <br><br><input class="confirm" type="submit" value="CONFIRM">
        </form>
    </div>
</body>
</html>
