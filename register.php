<?php
// This block of PHP code will run when the user submits the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'includes/db_connection.php';

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $hashed_password);

    try {
        if ($stmt->execute()) {
            // !! CHANGE: Redirect to login page on success !!
            header("Location: login.php?status=registered");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $error_message = "This email address is already registered.";
        } else {
            $error_message = "An error occurred. Please try again later.";
        }
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
    <title>Register - Surat BookCycle</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .register-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .message { text-align: center; padding: 10px; border-radius: 4px; margin-top: 15px; background-color: #f8d7da; color: #721c24; }
        /* !! NEW STYLES for extra links !! */
        .extra-links { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-size: 0.9em; }
        .extra-links a { color: #007bff; text-decoration: none; }
        .extra-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Create Your Account</h2>

        <?php if (!empty($error_message)): ?>
            <div class="message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Register</button>
        </form>

        <!-- !! NEW: Links at the bottom of the form !! -->
        <div class="extra-links">
            <a href="index.php">← Back to Home</a>
            <a href="login.php">Already have an account?</a>
        </div>
    </div>
</body>
</html>
