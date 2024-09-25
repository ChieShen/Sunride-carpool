<?php
session_start();
$loggedIn = isset($_SESSION['userID']);
$isAdmin = $loggedIn ? $_SESSION['admin'] : '';
$gender = $loggedIn ? $_SESSION['gender'] : '';
$firstName = $loggedIn ? $_SESSION['first_name'] : '';

//check if user it logged in
if (!$loggedIn) {
    echo '<script>alert("Please log in to access your profile."); window.location.href = "sign_in.php";</script>';
    exit; // Stop executing the rest of the PHP code
}
require_once("session_check.php");
require("db_connect.php");

// Retrieve reward data from the database
$query = "SELECT * FROM rewards";
$result = mysqli_query($dbc, $query);
$rewards = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Points</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/reward.css">
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
                    <a href="rewards.php">
                        <span style="color: #FF5C00;">POINTS</span>
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
                            <span><?php echo $firstName; ?></span>
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
    <hr style="margin-bottom:0;">
    <div class="background">
	<div class="backbox">
		<a href="home.php"><img src="images/back.png">HOME/</a>
		<p>Points</p>
	</div>
	
	<div class="container">
		<table class="accinfo">
			<tr>
				<td rowspan="2" class="accountpic">
    				<?php
                    if ($isAdmin) {
                        echo '<img src="images/adminpic.png" alt="Profile Picture">';
                    } else {
                        $accountPic = $gender === 'female' ? 'female_avatar.png' : 'male_avatar.png';
                        echo '<img src="images/' . $accountPic . '" alt="Profile Picture">';
                    }
                    ?>
				</td>				
				<td class="label">Name</td>
            	<td>
                	<?php
                    // Retrieve user data from the database
                    $query = "SELECT f_name, l_name, rewardPoints FROM user WHERE userID = '{$_SESSION['userID']}'";
                    $result = mysqli_query($dbc, $query);
                    $userData = mysqli_fetch_assoc($result);

                    // Display the user's full name
                    echo $userData['f_name'] . ' ' . $userData['l_name'];
                    ?>
            	</td>
        	</tr>
        	<tr>
            	<td class="label">Points</td>
            	<td>
                	<?php
                    // Display the user's points
                    echo $userData['rewardPoints'];
                    ?>
            	</td>
			</tr>
		</table>
		<hr>
		<div class="rewardsection">
            <?php foreach ($rewards as $index => $reward): ?>
                <table class="reward" <?php if ($index % 2 !== 0) echo 'style="margin-right: 0; margin-left: auto;"'; ?>>
                    <tr>
                        <td rowspan="3" class="rewardpic"><img src="<?php echo $reward['image']?>"></td>
                        <td class="rewardname"><?php echo $reward['name']; ?></td>
                        <td class="requiredpoints">[<?php echo $reward['points']; ?> points]</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="color: gray;"><?php echo $reward['description']; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><a href="claimrewards.php?reward_id=<?php echo $reward['rwdID']; ?>" class="button1">CLAIM</a></td>
                    </tr>
                </table>
            <?php endforeach; ?>
        </div>
		
	</div>
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