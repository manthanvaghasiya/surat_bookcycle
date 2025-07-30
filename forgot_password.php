<?php
$message_shown = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // In a real project, you would check if the email exists and send a reset link.
    // For this simulation, we will just show a confirmation message.
    $message_shown = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Surat BookCycle</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .forgot-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        h2 { margin-bottom: 20px; }
        p { color: #666; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .success-message { padding: 20px; background-color: #d4edda; color: #155724; border-radius: 8px; }
        .back-link { display: block; margin-top: 20px; font-size: 0.9em; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="forgot-container">
        <?php if ($message_shown): ?>
            <div class="success-message">
                <h3>Check Your Email</h3>
                <p>If an account with that email address exists, we have sent instructions to reset your password.</p>
            </div>
        <?php else: ?>
            <h2>Forgot Your Password?</h2>
            <p>Enter your email address below, and we'll send you a link to reset your password.</p>
            <form action="forgot_password.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit">Send Reset Link</button>
            </form>
        <?php endif; ?>
        <a href="login.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>
