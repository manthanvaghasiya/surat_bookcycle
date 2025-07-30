<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Surat BookCycle</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f8f9fa; }
        .admin-wrapper { display: flex; }
        .sidebar { width: 250px; background-color: #343a40; color: white; min-height: 100vh; padding-top: 20px; }
        .sidebar h2 { text-align: center; color: #fff; margin-bottom: 30px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li a { display: block; color: #adb5bd; padding: 15px 20px; text-decoration: none; transition: background-color 0.3s, color 0.3s; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: #495057; color: #fff; }
        .main-content { flex-grow: 1; padding: 20px; }
        .main-content .header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #dee2e6; margin-bottom: 20px; }
        .main-content .header a { text-decoration: none; background-color: #dc3545; color: white; padding: 8px 15px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_books.php">Manage Books</a></li>
            <li><a href="manage_messages.php">View Messages</a></li>
            <li><a href="manage_orders.php">Manage Orders</a></li>
            <li><a href="../index.php" target="_blank">View Live Site</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header">
            <h3>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h3>
            <a href="logout.php">Logout</a>
        </div>
