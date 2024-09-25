<?php
session_start();
$loggedIn = isset($_SESSION['userID']);
$isAdmin = $loggedIn ? $_SESSION['admin'] : '';
$gender = $loggedIn ? $_SESSION['gender'] : '';
$firstName = $loggedIn ? $_SESSION['first_name'] : '';

// Include the database connection file
require_once("session_check.php");
require "db_connect.php";

// Retrieve user data from the database
$userID = isset($_GET['userID']) ? $_GET['userID'] : 0;

$gender = isset($_SESSION['gender']) ? ucfirst($_SESSION['gender']) : '';

$sql = "SELECT * FROM `user` WHERE `userID` = '$userID'";
$result = mysqli_query($dbc, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $fname = $row['f_name'];
    $lname = $row['l_name'];
    $email = $row['email'];
    $gen = $row['gender'];
    $phone = $row['phone'];
    $locationID = $row['location'];
    $rewardPoints = $row['rewardPoints'];
    $admin = $row['admin'];
    
    if ($admin) {
        $profilepic = "images/adminpic.png";
    } else {
        $profilepic = "images/" . $gen . "_avatar.png";
    }
    
    // Retrieve location name from the database
    $locationSql = "SELECT `name` FROM `location` WHERE `locID` = '$locationID'";
    $locationResult = mysqli_query($dbc, $locationSql);
    
    if (mysqli_num_rows($locationResult) > 0) {
        $locationRow = mysqli_fetch_assoc($locationResult);
        $location = $locationRow['name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Profile</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/edit_user.css">
    <link rel="icon" type="images/x-icon" href="images/webicon.png">
</head>

<body>
    <div class="header">
        <div class="logo">
            <a href="home.php"> <img src="images/logo.png" alt="Sunride"> <span style="color: #FFCFB4;">SUN</span><span
                    style="color: black;">RIDE</span>
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="home.php"> <span>HOME</span>
                    </a></li>
                <li><a href="reward.php"> <span>POINTS</span>
                    </a></li>
                <li><a href="history.php"> <span>HISTORY</span>
                    </a></li>
                <li><a href="profile.php"> <span style="color: #FF5C00;">PROFILE</span>
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
                            <span>
                                <?php echo $firstName; ?>
                            </span>
                            <div class="dropdown-content">
                                <?php if ($isAdmin): ?>
                                    <a href="view_user.php">View Users</a>
                                <?php endif; ?>
                                <a href="change_password.php">Change Password</a> <a href="logout.php">Log Out</a>
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
        <a href="profile.php"><img src="images/back.png">Profile/</a>
        <p>Edit Profile</p>
    </div>

    <div class="edit-form">
        <form method="POST" action="update_profile.php">
            <table class="profile-table">
                <tr>
                    <td rowspan="9" class="accountpic">
                        <img src="<?php echo $profilepic; ?>" alt="Profile Picture">
                        <input type="hidden" name="userID" value="<?php echo $userID; ?>">
                    </td>
                    <td class="label">First Name:</td>
                    <td><input type="text" name="first_name" value="<?php echo $fname; ?>"></td>
                </tr>
                <tr>
                    <td class="label">Last Name:</td>
                    <td><input type="text" name="last_name" value="<?php echo $lname; ?>"></td>
                </tr>
                <tr>
                    <td class="label">Gender:</td>
                    <td>
                        <select name="gender">
                            <?php if ($gen == "male"): ?>
                                <option value="male" selected>Male</option>
                                <option value="female">Female</option>
                            <?php elseif ($gen == "female"): ?>
                                <option value="male">Male</option>
                                <option value="female" selected>Female</option>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td><input type="email" name="email" value="<?php echo $email; ?>"></td>
                </tr>
                <tr>
                    <td class="label">Phone:</td>
                    <td><input type="text" name="phone_number" value="<?php echo $phone; ?>"></td>
                </tr>
                <tr>
                    <td class="label">Location: </td>
                    <td><select name="location">
                            <?php
                            echo "<option value='$locationID' >$location</option>";
                            // Retrieve location data from the database
                            $locationSql = "SELECT * FROM `location`";
                            $locationResult = mysqli_query($dbc, $locationSql);

                            if (mysqli_num_rows($locationResult) > 0) {
                                while ($locationRow = mysqli_fetch_assoc($locationResult)) {
                                    $locID = $locationRow['locID'];
                                    $locationName = $locationRow['name'];
                                    if ($locID != $locationID) {
                                        $selected = ($locID == $locationID) ? 'selected' : '';
                                        echo "<option value='$locID' $selected>$locationName</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
            <div class="button-align">
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</body>

</html>