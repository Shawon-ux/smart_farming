<?php
// Include at the top of every admin page: redirects to login unless an admin is signed in.
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}
