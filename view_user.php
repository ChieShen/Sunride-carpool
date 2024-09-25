<?php
session_start();
$loggedIn = isset($_SESSION['userID']);
$isAdmin = $loggedIn ? $_SESSION['admin'] : '';
$gender = $loggedIn ? $_SESSION['gender'] : '';
$firstName = $loggedIn ? $_SESSION['first_name'] : '';

require_once("session_check.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sunride - User Management</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/view_user.css">
    <link rel="icon" type="images/x-icon" href="images/webicon.png">
</head>

<body>    <div class="header">
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

	<hr>
    <h1>User Management</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <?php
        // Fetch user data from the database
        require('db_connect.php'); // Connect to the database
        $query = "SELECT userID, l_name, f_name, email, admin FROM user";
        $result = mysqli_query($dbc, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $userID = $row['userID'];
            $lastName = $row['l_name'];
            $firstName = $row['f_name'];
            $email = $row['email'];
            $admin = $row['admin'];
        ?>
            <tr>
                <td><?php echo $userID; ?></td>
                <td><?php echo $firstName . ' ' . $lastName; ?></td>
                <td class="email"><?php echo $email; ?></td>
                <td>
                    <a href="edit_user.php?userID=<?php echo $userID; ?>" class="button edit">Edit</a>
                    <a href="user_action.php?action=delete&userID=<?php echo $userID; ?>" class="button delete">Delete</a>
                    <?php if(!$admin): ?>
                    <a href="user_action.php?action=make_admin&userID=<?php echo $userID; ?>" class="button admin">Make Admin</a>
                    <?php else: ?>
        			<span class="role">Admin</span>
    				<?php endif; ?>
                </td>
            </tr>
        <?php
        }
        mysqli_close($dbc); // Close the database connection
        ?>
    </tbody>
</table>
</body>

</html>