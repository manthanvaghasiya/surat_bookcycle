<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the book ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

require 'includes/db_connection.php';

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// --- Security Check & Cleanup ---
// First, get the image path to delete the file from the server
// And verify that the book belongs to the logged-in user
$stmt = $conn->prepare("SELECT image_path FROM books WHERE book_id = ? AND user_id = ?");
$stmt->bind_param("ii", $book_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    // The user owns this book, proceed with deletion
    $book = $result->fetch_assoc();
    $image_to_delete = $book['image_path'];

    // 1. Delete the physical image file
    if (file_exists($image_to_delete)) {
        unlink($image_to_delete);
    }

    // 2. Delete the book record from the database
    $delete_stmt = $conn->prepare("DELETE FROM books WHERE book_id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $book_id, $user_id);
    
    if ($delete_stmt->execute()) {
        // Redirect to dashboard with a success message
        header("Location: dashboard.php?status=deleted");
        exit();
    } else {
        // Handle potential error
        header("Location: dashboard.php?error=deletefailed");
        exit();
    }
    $delete_stmt->close();

} else {
    // The book does not exist or does not belong to the user.
    // Redirect them to their dashboard without doing anything.
    header("Location: dashboard.php");
    exit();
}

$stmt->close();
$conn->close();
?>
