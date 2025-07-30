<?php
session_start();

// Unset only the admin session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);

// Redirect to the admin login page
header("Location: login.php");
exit();
?>
