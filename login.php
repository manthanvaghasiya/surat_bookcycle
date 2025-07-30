<?php
// ALWAYS start the session at the very top of the page
session_start();

// If the user is already logged in, redirect them to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// This block runs when the user submits the login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'includes/db_connection.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL to prevent injection
    $stmt = $conn->prepare("SELECT user_id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // User with that email exists, now check the password
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Password is correct!
            // Store user data in the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];

            // !! CHANGE: Redirect to the homepage instead of the dashboard !!
            header("Location: index.php");
            exit();
        } else {
            // Incorrect password
            $error_message = "Invalid email or password.";
        }
    } else {
        // No user found with that email
        $error_message = "Invalid email or password.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Surat BookCycle</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .message { text-align: center; padding: 10px; border-radius: 4px; margin-top: 15px; }
        .error { background-color: #f8d7da; color: #721c24; }
        .success { background-color: #d4edda; color: #155724; }
        .extra-links { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-size: 0.9em; }
        .extra-links a { color: #007bff; text-decoration: none; }
        .home-link-container { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to Your Account</h2>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'registered'): ?>
            <div class="message success">Registration successful! Please log in.</div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="extra-links">
            <a href="forgot_password.php">Forgot Password?</a>
            <a href="register.php">Don't have an account?</a>
        </div>
        <div class="home-link-container">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>
