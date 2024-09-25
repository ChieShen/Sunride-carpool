<?php
session_start();

$loggedIn = isset($_SESSION['userID']);
$isAdmin = $loggedIn ? $_SESSION['admin'] : '';
$gender = $loggedIn ? $_SESSION['gender'] : '';
$firstName = $loggedIn ? $_SESSION['first_name'] : '';
$userID = $loggedIn ? $_SESSION['userID'] : '';

if (!$loggedIn) {
    echo '<script>alert("Please log in to search a ride."); window.location.href = "sign_in.php";</script>';
    exit; // Stop executing the rest of the PHP code
}

require_once("session_check.php");

require ('db_connect.php');

$departure = urldecode($_GET['departure']);
$destination = urldecode($_GET['destination']);
$date = urldecode($_GET['date']);

// Function to get the location name based on locID
function getLocationName($dbc, $locID)
{
    $query = "SELECT name FROM location WHERE locID = $locID";
    $result = mysqli_query($dbc, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['name'];
}

// Function to get the driver's name based on userID
function getDriverName($dbc, $userID)
{
    $query = "SELECT f_name, l_name FROM user WHERE userID = $userID";
    $result = mysqli_query($dbc, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['l_name'] . ' ' . $row['f_name'];
}

// Get rides
$currentDateTime = date('Y-m-d H:i:s');
$ridesQuery = "SELECT * FROM rides WHERE departureDate = '$date' AND departurelocID = $departure AND destinationlocID = $destination AND CONCAT(departureDate, ' ', departureTime) >= '$currentDateTime' AND driverID != $userID ORDER BY departureDate ASC";
$ridesResult = mysqli_query($dbc, $ridesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Find Rides</title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="css/avai_rides.css">
<link rel="icon" type="images/x-icon" href="images/webicon.png">
</head>

<body>
	<div class="header">
		<div class="logo">
			<a href="home.php"> <img src="images/logo.png" alt="Sunride"> <span
				style="color: #FFCFB4;">SUN</span><span style="color: black;">RIDE</span>
			</a>
		</div>
		<nav>
			<ul>
				<li><a href="home.php"> <span style="color: #FF5C00;">HOME</span>
				</a></li>
				<li><a href="reward.php"> <span>POINTS</span>
				</a></li>
				<li><a href="history.php"> <span>HISTORY</span>
				</a></li>
				<li><a href="profile.php"> <span>PROFILE</span>
				</a></li>
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
						<span><?php echo $firstName; ?></span>
						<div class="dropdown-content">
                                <?php if ($isAdmin): ?>
                                    <a href="view_user.php">View Users</a>
                                <?php endif; ?>
                                <a href="change_password.php">Change Password</a> <a
								href="logout.php">Log Out</a>
						</div>
					</div>
                    <?php else: ?>
                        <a href="sign_in.php" class="dropdown">
						<div class="profilepic">
							<img src="images/male_avatar.png">
						</div> <span>LOG IN</span>
				</a>
                    <?php endif; ?>
                </li>
			</ul>
		</nav>
	</div>

	<hr>
	<div class="backbox">
		<a href="home.php"><img src="images/back.png">HOME/</a>
		<p>Find A Ride</p>
	
	</div>
	<p class="available">Ride Available:</p>

    	<?php if (mysqli_num_rows($ridesResult) > 0): ?>
        	<?php while ($ride = mysqli_fetch_assoc($ridesResult)): ?>
            <!-- Display ride information  -->
	<table>
		<tr>
			<td class="passenger_pic">
    <?php
            // Get the number of customers joined the ride
            $rideID = $ride['rideID'];
            $joinedCustomersQuery = "SELECT COUNT(*) as numJoined FROM rideCustomer WHERE rideID = $rideID";
            $joinedCustomersResult = mysqli_query($dbc, $joinedCustomersQuery);
            $numJoined = mysqli_fetch_assoc($joinedCustomersResult)['numJoined'];

            // Calculate seats available
            $seatsAvailable = $ride['seatsAvai'] - $numJoined;

            // Display profile pictures of joined customers or "Be the first one to join!" message
            if ($numJoined > 0) {
                $joinedCustomersInfoQuery = "SELECT u.gender FROM rideCustomer rc JOIN user u ON rc.customerID = u.userID WHERE rc.rideID = $rideID";
                $joinedCustomersInfoResult = mysqli_query($dbc, $joinedCustomersInfoQuery);

                while ($joinedCustomerInfo = mysqli_fetch_assoc($joinedCustomersInfoResult)) {
                    $gender = $joinedCustomerInfo['gender'];
                    echo '<img src="images/' . $gender . '_avatar.png" alt="Profile Picture">';
                }
            } else {
                echo '<p style="font-weight: bold; color: orange; font-size: 25px;">Be the first one to join!</p>';
            }
            ?>
		</td>
			<td class="ride_info">
                        <?php
            echo 'Driver: ' . getDriverName($dbc, $ride['driverID']) . '<br>' . 
                 'From: ' . getLocationName($dbc, $ride['departurelocID']) . ' &rarr; ' . 
                  getLocationName($dbc, $ride['destinationlocID']) . '<br>' .
                 'Date: ' . $ride['departureDate'] . '<br>' . 
                 'Seats Available: ' . $seatsAvailable . '<br>' . 
                 'Estimated Duration: ' . $ride['eta'];
            ?>
                    </td>
			<td class="price">RM<?php echo $ride['fees']; ?></td>
			<td class="clock"><?php echo date("h:i A", strtotime($ride['departureTime'])); ?></td>
			<td class="join"><?php echo '<a href="join_ride.php?rideID=' . $ride['rideID'] . '">JOIN</a>'; ?></td>
		</tr>
	</table>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="noride">No rides found.</p>
    <?php endif; ?>

</body>
</html>

<?php
mysqli_close($dbc);
?>