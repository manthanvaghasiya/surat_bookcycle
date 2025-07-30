<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Styles for the navigation bar */
        body { font-family: Arial, sans-serif; margin: 0; }
        .navbar { width: 100%; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 0 20px; box-sizing: border-box; }
        .nav-container { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; height: 60px; }
        .nav-logo a { font-size: 1.5em; font-weight: bold; text-decoration: none; color: #333; }
        .nav-links { list-style: none; margin: 0; padding: 0; display: flex; align-items: center; }
        .nav-links li { margin-left: 20px; }
        .nav-links a { text-decoration: none; color: #555; font-weight: bold; padding: 8px 12px; border-radius: 4px; transition: background-color 0.3s; }
        .nav-links a:hover { background-color: #f4f4f4; }
        .nav-links a.btn { background-color: #007bff; color: white; }
        .nav-links a.btn:hover { background-color: #0056b3; }
        .dropdown { position: relative; display: inline-block; }
        .dropdown .dropbtn { cursor: pointer; }
        .dropdown-content { display: none; position: absolute; background-color: #f9f9f9; min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; border-radius: 5px; }
        .dropdown-content a { color: black; padding: 12px 16px; text-decoration: none; display: block; text-align: left; }
        .dropdown-content a:hover { background-color: #f1f1f1; }
        .dropdown:hover .dropdown-content { display: block; }

        /* !! NEW STYLE for the user's name !! */
        .user-profile-name {
            color: #007bff;
            font-weight: bold;
            padding: 8px 0;
            border-left: 2px solid #eee;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">Surat BookCycle</a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Links for logged-in users -->
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="my_orders.php">My Orders</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="logout.php">Logout</a></li>
                    <!-- !! NEW: Displaying the user's name !! -->
                    <li class="user-profile-name">
                        Hi, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                    </li>
                <?php else: ?>
                    <!-- Links for logged-out visitors -->
                    <li class="dropdown">
                        <a href="#" class="dropbtn">Login</a>
                        <div class="dropdown-content">
                            <a href="login.php">User Login</a>
                            <a href="admin/login.php">Admin Login</a>
                        </div>
                    </li>
                    <li><a href="register.php" class="btn">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
