<?php
session_start();
$loggedIn = isset($_SESSION['userID']);
$isAdmin = $loggedIn ? $_SESSION['admin'] : '';
$gender = $loggedIn ? $_SESSION['gender'] : '';
$firstName = $loggedIn ? $_SESSION['first_name'] : '';

require_once("session_check.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET)) {
    require('db_connect.php');
    $errors = array();
    
    // Validate search bar fields
    $departure = isset($_GET['departure']) ? $_GET['departure'] : '';
    $destination = isset($_GET['destination']) ? $_GET['destination'] : '';
    $date = isset($_GET['date']) ? $_GET['date'] : '';
    
    if (empty($departure) || empty($destination) || empty($date)) {
        $errors[] = 'All fields are required.';
    }
    
    if (empty($departure)) {
        $errors[] = 'Please choose depature location';
    }
    
    if (empty($destination)) {
        $errors[] = 'Please choose destination';
    }
    
    if (empty($date)) {
        $errors[] = 'Please choose date';
    }
    
    // Check if the date is in the past
    $today = date('Y-m-d');
    if ($date < $today) {
        $errors[] = 'Selected date cannot be in the past.';
    }
        
    // Check if departure and destination are the same
    if ($departure == $destination) {
        $errors[] = 'Departure and destination locations cannot be the same.';
    }
    
    // Display errors using JavaScript alert
    if (!empty($errors)) {
        echo '<script>alert("' . implode('\n', $errors) . '");</script>';
    } else {
        $url = 'avai_rides.php?departure=' . urlencode($departure) . '&destination=' . urlencode($destination) . '&date=' . urlencode($date);
        header('Location: ' . $url);
        exit();
    }
    
    // Close the database connection
    mysqli_close($dbc);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sunride</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <link rel="icon" type="images/x-icon" href="images/webicon.png">
</head>

<body>
    <div class="header">
        <div class="logo">
            <a href="home.php">
                <img src="images/logo.png" alt="Sunride">
                <span style="color: #FFCFB4;">SUN</span><span style="color: black;">RIDE</span>
            </a>
        </div>
        <nav>
            <ul>
                <li>
                    <a href="home.php">
                        <span style="color: #FF5C00;">HOME</span>
                    </a>
                </li>
                <li>
                    <a href="reward.php">
                        <span>POINTS</span>
                    </a>
                </li>
                <li>
                    <a href="history.php">
                        <span>HISTORY</span>
                    </a>
                </li>
                <li>
                    <a href="profile.php">
                        <span>PROFILE</span>
                    </a>
                </li>
                <li>
                    <?php if ($loggedIn): ?>
                        <div class="dropdown">
                            <div class="profilepic">
                                <?php if ($isAdmin): ?>
                                    <img src="images/adminpic.png">
                                <?php else: ?>
                                    <img src="<?php echo 'images/' . $gender . '_avatar.png'; ?>">
                                <?php endif; ?>
                            </div>
                            <span>
                                <?php echo $firstName; ?>
                            </span>
                            <div class="dropdown-content"> <!-- show admin pages if user is admin -->
                                <?php if ($isAdmin): ?>
                                    <a href="view_user.php">View Users</a>
                                <?php endif; ?>
                                <a href="change_password.php">Change Password</a>
                                <a href="logout.php">Log Out</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="sign_in.php" class="dropdown">
                            <div class="profilepic">
                                <img src="images/male_avatar.png">
                            </div>
                            <span>LOG IN</span>
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
    </div>

    <hr style="margin-bottom:0;">

    <div class="background">
        <div class="container">
            <div class="info">
                <p class="message">"It's a sustainable, efficient, affordable, and enjoyable way to travel together."
                </p>
                <p class="detail">Sunride connects people looking to travel with drivers who have vacant seats, offering
                    a
                    sustainable transport solution that promotes shared mobility and reduces carbon emissions.</p>
            </div>
            <div class="carpic">
                <img src="images/homecar.png">
            </div>
        </div>
        <div class="search_bar">
            <form action="home.php" method="get">
                <div class="input_field">
                    <img src="images/location.png">
                    <select id="departure" name="departure">
                        <option value="">Select Depature</option>
                        <?php
                        //display locations using dropdown menu
                        require("db_connect.php");
                        $q = "SELECT locID, name FROM location";
                        $result = mysqli_query($dbc, $q);

                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<option value="' . $row['locID'] . '">' . $row['name'] . '</option>';
                        }

                        mysqli_close($dbc);
                        ?>
                    </select>
                </div>
                <div class="input_field">
                    <img src="images/destination.png">
                    <select id="destination" name="destination">
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
                </div>
                <div class="input_field">
                    <img src="images/calendar.png">
                    <input type="date" id="date" name="date" placeholder="Date" value="<?php if (isset($_GET['date']))
                        echo ($_GET['date']); ?>">
                </div>

                <button type="submit">FIND RIDE</button>
            </form>
            <a href="<?php echo $loggedIn ? 'offer_ride.php' : 'javascript:showAlertAndRedirect();'; ?>">OFFER RIDE</a>
        </div>
        <script>
            function showAlertAndRedirect() {
                alert('You must be logged in to offer a ride.');
                window.location.href = 'sign_in.php';
            }
        </script>
    </div>
    <div class="bottom">
        <table style="margin-top: 20px;">
            <tr>
                <td colspan="2" class="brand"><a href="home.php">SUNRIDE</a></td>

                <td class="spacing"></td>
                <td colspan="2" class="founded">Established 2023,</td>

            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td rowspan="2" class="flag"><img src="images/malaysia.png"></td>
                <td rowspan="2" class="country">Malaysia</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>Phone Number</td>
                <td></td>


            </tr>
            <tr>
                <td>hello.sunride.com</td>
                <td>+60 193902837</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <hr>
        <table style="padding-bottom: 20px;">
            <tr>
                <td>&copy2023 Sunride Inc. All rights reserved.</td>
                <td class="spacing"></td>
                <td class="terms">Terms of Service</td>
                <td class="terms">Privacy Policy</td>
                <td class="terms">Cookies</td>
            </tr>
        </table>
    </div>
</body>

</html>