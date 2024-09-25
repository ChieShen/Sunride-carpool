<?php
// Start the session
session_start();

// Check if the user is logged in
$loggedIn = isset($_SESSION['userID']);
$userID = $loggedIn ? $_SESSION['userID'] : '';
$isAdmin = $loggedIn ? $_SESSION['admin'] : '';
$gender = $loggedIn ? $_SESSION['gender'] : '';
$firstName = $loggedIn ? $_SESSION['first_name'] : '';

if (!$loggedIn) {
    echo '<script>alert("Please log in to access the history."); window.location.href = "sign_in.php";</script>';
    exit; // Stop executing the rest of the PHP code
}
require_once("session_check.php");
// Require the database connection file
require('db_connect.php');


// Retrieve the past rides of the logged-in user
$sqlPastRides = "(SELECT r.*, rc.arrived AS arrived
                 FROM rides r
                 INNER JOIN ridecustomer rc ON r.rideID = rc.rideID
                 WHERE rc.customerID = '$userID'
                 AND r.departureDate < CURDATE())
                 UNION
                 (SELECT r.*, NULL AS arrived
                 FROM rides r
                 WHERE r.driverID = '$userID'
                 AND r.departureDate < CURDATE())
                 ORDER BY departureDate DESC, departureTime DESC;";
$pastRides = mysqli_query($dbc, $sqlPastRides);

$sqlScheduledRides = "(SELECT r.*
                      FROM rides r
                      INNER JOIN ridecustomer rc ON r.rideID = rc.rideID
                      WHERE rc.customerID = '$userID'
                      AND r.departureDate >= CURDATE())
                      UNION
                      (SELECT r.*
                      FROM rides r
                      WHERE r.driverID = '$userID'
                      AND r.departureDate >= CURDATE())
                      ORDER BY departureDate ASC, departureTime ASC";
$scheduledRides = mysqli_query($dbc, $sqlScheduledRides);


// Function to format the time in 12-hour format
function formatTime($time)
{
    return date('h:i A', strtotime($time));
}

function getLocationName($dbc, $locID)
{
    $query = "SELECT name FROM location WHERE locID = $locID";
    $result = mysqli_query($dbc, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['name'];
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>History</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/history.css">
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
                        <span>HOME</span>
                    </a>
                </li>
                <li>
                    <a href="reward.php">
                        <span>POINTS</span>
                    </a>
                </li>
                <li>
                    <a href="history.php">
                        <span style="color: #FF5C00;">HISTORY</span>
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
                            <div class="dropdown-content">
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
    <hr>
    <div class="backbox">
        <a href="home.php"><img src="images/back.png">HOME/</a>
        <p>History</p>
    </div>

    <div class="history-container">
        <div class="past-rides">
            <h2 style="margin-left: 23%;">Past Rides</h2>
            <?php if (mysqli_num_rows($pastRides) > 0): ?>
                <?php while ($ride = mysqli_fetch_assoc($pastRides)): ?>
                    <table class="ride-table">
                        <tr>
                            <td class="profile-pic">
                                <?php
                                // Get the number of customers joined the ride
                                $rideID = $ride['rideID'];
                                $fees = $ride['fees'];
                                $driverID = $ride['driverID'];
                                $joinedCustomersQuery = "SELECT COUNT(*) as numJoined FROM rideCustomer WHERE rideID = $rideID";
                                $joinedCustomersResult = mysqli_query($dbc, $joinedCustomersQuery);
                                $numJoined = mysqli_fetch_assoc($joinedCustomersResult)['numJoined'];


                                // Display profile pictures of joined customers or "Be the first one to join!" message
                                if ($numJoined > 0) {
                                    $joinedCustomersInfoQuery = "SELECT u.gender FROM rideCustomer rc JOIN user u ON rc.customerID = u.userID WHERE rc.rideID = $rideID";
                                    $joinedCustomersInfoResult = mysqli_query($dbc, $joinedCustomersInfoQuery);

                                    while ($joinedCustomerInfo = mysqli_fetch_assoc($joinedCustomersInfoResult)) {
                                        $gender = $joinedCustomerInfo['gender'];
                                        echo '<img src="images/' . $gender . '_avatar.png" alt="Profile Picture">';
                                    }
                                } elseif (($driverID == $userID) && $numJoined == 0) {
                                    echo 'No one joined the ride...';
                                }
                                ?>
                            </td>
                            <td class="ride-details">
                                <?php echo date('Y-m-d', strtotime($ride['departureDate'])) . ' <b>&#183;</b> ' . formatTime($ride['departureTime']) . '<br>' .
                                    getLocationName($dbc, $ride['departurelocID']) . ' &rarr;<b> ' . getLocationName($dbc, $ride['destinationlocID']) . '</b>'; ?>
                            </td>
                            <td class="fees">RM
                                <?php echo $fees; ?>
                            </td>
                            <td class="button">
                                <?php
                                if ($driverID == $userID) {
                                    $complete = $ride['complete'];
                                    if ($complete) {
                                        echo '<img src="images/complete.png" style="width: 70px; height: 70px;">';
                                    } else {
                                        echo '<form action="historyfunctions.php" method="post">';
                                        echo '<input type="hidden" name="userID" value="' . $userID . '">';
                                        echo '<input type="hidden" name="rideID" value="' . $rideID . '">';
                                        echo '<input type="hidden" name="fees" value="' . $fees . '">';
                                        echo '<input type="hidden" name="driverID" value="' . $driverID . '">';
                                        echo '<button class="complete" type="submit" name="button" value ="transported">Transported</button>';
                                        echo '</form>';
                                    }
                                } else {
                                    $arrived = $ride['arrived'];
                                    if ($arrived) {
                                        echo '<img src="images/complete.png" style="width: 70px; height: 70px;">';
                                    } else {
                                        echo '<form action="historyfunctions.php" method="post">';
                                        echo '<input type="hidden" name="userID" value="' . $userID . '">';
                                        echo '<input type="hidden" name="rideID" value="' . $rideID . '">';
                                        echo '<input type="hidden" name="fees" value="' . $fees . '">';
                                        echo '<input type="hidden" name="driverID" value="' . $driverID . '">';
                                        echo '<button class="complete" type="submit" name="button" value ="arrived">Arrived</button>';
                                        echo '</form>';
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No past rides found.</p>
            <?php endif; ?>
        </div>


        <div class="scheduled-rides">
            <h2 style="margin-left: 10%;">Scheduled Rides</h2>
            <?php
            if (mysqli_num_rows($scheduledRides) > 0): ?>
                <?php while ($ride = mysqli_fetch_assoc($scheduledRides)): ?>
                    <table class="ride-table">
                        <tr>
                            <td class="profile-pic">
                                <?php
                                // Get the number of customers joined the ride
                                $rideID = $ride['rideID'];
                                $driverID = $ride['driverID'];
                                $fees = $ride['fees'];
                                $joinedCustomersQuery = "SELECT COUNT(*) as numJoined FROM rideCustomer WHERE rideID = $rideID";
                                $joinedCustomersResult = mysqli_query($dbc, $joinedCustomersQuery);
                                $numJoined = mysqli_fetch_assoc($joinedCustomersResult)['numJoined'];

                                // Display profile pictures of joined customers or "Waiting for others to join" message
                                if ($numJoined > 0) {
                                    $joinedCustomersInfoQuery = "SELECT u.gender FROM rideCustomer rc JOIN user u ON rc.customerID = u.userID WHERE rc.rideID = $rideID";
                                    $joinedCustomersInfoResult = mysqli_query($dbc, $joinedCustomersInfoQuery);

                                    while ($joinedCustomerInfo = mysqli_fetch_assoc($joinedCustomersInfoResult)) {
                                        $gender = $joinedCustomerInfo['gender'];
                                        echo '<img src="images/' . $gender . '_avatar.png" alt="Profile Picture">';
                                    }
                                } elseif (($driverID == $userID) && $numJoined == 0) {
                                    echo 'Waiting for others to join...';
                                }
                                ?>
                            </td>
                            <td class="ride-details">
                                <?php echo date('Y-m-d', strtotime($ride['departureDate'])) . ' <b>&#183;</b> ' . formatTime($ride['departureTime']) . '<br>' .
                                    getLocationName($dbc, $ride['departurelocID']) . ' &rarr;<b> ' . getLocationName($dbc, $ride['destinationlocID']) . '</b>'; ?>
                            </td>
                            <td class="fees">RM
                                <?php echo $fees; ?>
                            </td>
                            <td class="button">
                                <?php
                                if ($driverID == $userID) {
                                    echo '<form action="historyfunctions.php" method="post">';
                                    echo '<input type="hidden" name="userID" value="' . $userID . '">';
                                    echo '<input type="hidden" name="rideID" value="' . $rideID . '">';
                                    echo '<input type="hidden" name="fees" value="' . $fees . '">';
                                    echo '<input type="hidden" name="driverID" value="' . $driverID . '">';
                                    echo '<button class="cancel" type="submit" name="button" value ="cancelOffer">Cancel Offer</button>';
                                    echo '</form>';
                                } else {
                                    echo '<form action="historyfunctions.php" method="post">';
                                    echo '<input type="hidden" name="userID" value="' . $userID . '">';
                                    echo '<input type="hidden" name="rideID" value="' . $rideID . '">';
                                    echo '<input type="hidden" name="fees" value="' . $fees . '">';
                                    echo '<input type="hidden" name="driverID" value="' . $driverID . '">';
                                    echo '<button class="cancel" type="submit" name="button" value ="cancelJoin">Cancel Join</button>';
                                    echo '</form>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No scheduled rides found.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>