<?php

if (isset($_SESSION['timeout']) && time() > $_SESSION['timeout']) {
    session_unset();
    session_destroy();
    echo '<script>alert("Your session has timed-out. Please log in again.");</script>';
    echo '<script>window.location.href = "sign_in.php";</script>';
    exit();
}
?>
