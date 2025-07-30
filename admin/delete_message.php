<?php
session_start();

// Redirect to login if not an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the message ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_messages.php");
    exit();
}

require '../includes/db_connection.php';

$message_id = $_GET['id'];

// Prepare and execute the delete statement
$stmt = $conn->prepare("DELETE FROM contact_messages WHERE message_id = ?");
$stmt->bind_param("i", $message_id);

if ($stmt->execute()) {
    // Redirect back with a success message
    header("Location: manage_messages.php?status=deleted");
    exit();
}

// If something went wrong, just redirect back
header("Location: manage_messages.php?error=1");
exit();
?>