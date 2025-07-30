<?php
session_start();

// Redirect to login if not an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the book ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_books.php");
    exit();
}

require '../includes/db_connection.php';

$book_id = $_GET['id'];

// Unlike the user-facing delete, the admin does not need to check for user_id.
// The admin has permission to delete any book.

// First, get the image path to delete the file
$stmt = $conn->prepare("SELECT image_path FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $book = $result->fetch_assoc();
    $image_to_delete = '../' . $book['image_path']; // Adjust path because we are in the admin folder

    // 1. Delete the physical image file
    if (file_exists($image_to_delete)) {
        unlink($image_to_delete);
    }

    // 2. Delete the book record from the database
    $delete_stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $delete_stmt->bind_param("i", $book_id);
    
    if ($delete_stmt->execute()) {
        // Redirect back with a success message
        header("Location: manage_books.php?status=deleted");
        exit();
    }
}

// If something went wrong, just redirect back
header("Location: manage_books.php?error=1");
exit();
?>