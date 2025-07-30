<?php
$message_sent = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- NEW DATABASE LOGIC ---
    require 'includes/db_connection.php';

    // Get data from form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message_content = $_POST['message'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message_content);

    // Execute and set the success flag
    if ($stmt->execute()) {
        $message_sent = true;
    }
    
    $stmt->close();
    $conn->close();
    // --- END OF NEW LOGIC ---
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Surat BookCycle</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f9f9f9; }
        .container { max-width: 600px; margin: 30px auto; }
        .form-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-container h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 120px; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .success-message { text-align: center; padding: 20px; background-color: #d4edda; color: #155724; border-radius: 8px; }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="form-container">
            <?php if ($message_sent): ?>
                <div class="success-message">
                    <h3>Thank You!</h3>
                    <p>Your message has been received. We will get back to you shortly.</p>
                </div>
            <?php else: ?>
                <h2>Contact Us</h2>
                <p>Have a question or feedback? Fill out the form below to get in touch with us.</p>
                <form action="contact.php" method="POST">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <button type="submit">Send Message</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
