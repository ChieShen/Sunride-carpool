<?php
session_start();
$loggedIn = isset($_SESSION['userID']);
$isAdmin = $loggedIn ? $_SESSION['admin'] : '';
$gender = $loggedIn ? $_SESSION['gender'] : '';
$firstName = $loggedIn ? $_SESSION['first_name'] : '';

if (!$loggedIn) {
    echo '<script>alert("Please log in to access your profile."); window.location.href = "sign_in.php";</script>';
    exit; // Stop executing the rest of the PHP code
}

require_once("session_check.php");

// Include the database connection file
require ("db_connect.php");

// Retrieve user data from the database
$userID = $_SESSION['userID'];

if ($isAdmin) {
    $profilepic = "images/adminpic.png";
} else {
    $profilepic = "images/" . $gender . "_avatar.png";
}

$gender = isset($_SESSION['gender']) ? ucfirst($_SESSION['gender']) : '';

$sql = "SELECT * FROM `user` WHERE `userID` = '$userID'";
$result = mysqli_query($dbc, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $lastName = $row['l_name'];
    $email = $row['email'];
    $phone = $row['phone'];
    $locationID = $row['location'];
    $rewardPoints = $row['rewardPoints'];
    
    // Retrieve location name from the database
    $locationSql = "SELECT `name` FROM `location` WHERE `locID` = '$locationID'";
    $locationResult = mysqli_query($dbc, $locationSql);
    
    if (mysqli_num_rows($locationResult) > 0) {
        $locationRow = mysqli_fetch_assoc($locationResult);
        $location = $locationRow['name'];
    } else {
        $location = "N/A";
    }
    
    // Retrieve the number of rides offered by the user
    $ridesOfferedSql = "SELECT COUNT(*) AS `ridesOffered` FROM `rides` WHERE `driverID` = '$userID'";
    $ridesOfferedResult = mysqli_query($dbc, $ridesOfferedSql);
    
    if (mysqli_num_rows($ridesOfferedResult) > 0) {
        $ridesOfferedRow = mysqli_fetch_assoc($ridesOfferedResult);
        $ridesOffered = $ridesOfferedRow['ridesOffered'];
    } else {
        $ridesOffered = 0;
    }
    
    // Retrieve the number of rides joined by the user
    $ridesJoinedSql = "SELECT COUNT(*) AS `ridesJoined` FROM `ridecustomer` WHERE `customerID` = '$userID'";
    $ridesJoinedResult = mysqli_query($dbc, $ridesJoinedSql);
    
    if (mysqli_num_rows($ridesJoinedResult) > 0) {
        $ridesJoinedRow = mysqli_fetch_assoc($ridesJoinedResult);
        $ridesJoined = $ridesJoinedRow['ridesJoined'];
    } else {
        $ridesJoined = 0;
    }
    
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Profile</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/profile.css">
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
                        <span>HISTORY</span>
                    </a>
                </li>
                <li>
                    <a href="profile.php">
                        <span style="color: #FF5C00;">PROFILE</span>
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

    <div class="topbar">
        <div class="backbox">
            <a href="home.php"><img src="images/back.png">HOME/</a>
            <p>Profile</p>
        </div>

        <div class="editprofile">
            <a href="edit_user.php?userID=<?php echo $userID; ?>">EDIT PROFILE</a>
        </div>
    </div>

    <table class="profile-table">
        <tr>
            <td rowspan="9" class="accountpic">
                <img src="<?php echo $profilepic; ?>" alt="Profile Picture">
            </td>
            <td class="label">First Name:</td>
            <td>
                <?php echo $firstName; ?>
            </td>
        </tr>
        <tr>
            <td class="label">Last Name:</td>
            <td>
                <?php echo $lastName; ?>
            </td>
        </tr>
        <tr>
            <td class="label">Gender:</td>
            <td>
                <?php echo $gender; ?>
            </td>
        </tr>
        <tr>
            <td class="label">Email:</td>
            <td>
                <?php echo $email; ?>
            </td>
        </tr>
        <tr>
            <td class="label">Phone:</td>
            <td>
                <?php echo $phone; ?>
            </td>
        </tr>
        <tr>
            <td class="label">Location:</td>
            <td>
                <?php echo $location; ?>
            </td>
        </tr>
        <tr>
            <td class="label">Reward Points:</td>
            <td>
                <?php echo $rewardPoints; ?>
            </td>
        </tr>
        <tr>
            <td class="label">Rides Offered:</td>
            <td>
                <?php echo $ridesOffered; ?>
            </td>
        </tr>
        <tr>
            <td class="label">Rides Joined:</td>
            <td>
                <?php echo $ridesJoined; ?>
            </td>
    </table>
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