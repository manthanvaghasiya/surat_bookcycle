<?php
// Set headers for CORS and JSON content type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE"); // Allow DELETE method
header("Content-Type: application/json; charset=UTF-8");

// We need the session to know who the user is
session_start(); 
require 'includes/db_connection.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["message" => "You must be logged in to delete a book."]);
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. Get the book ID from the query string (e.g., ?book_id=12)
$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;

if ($book_id <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "A valid book ID is required."]);
    exit();
}

$conn->begin_transaction();

try {
    // 3. Get the image path AND verify the book belongs to this user
    $stmt = $conn->prepare("SELECT image_path, status FROM books WHERE book_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $book_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $book = $result->fetch_assoc();
        $image_to_delete = $book['image_path'];

        // 4. CRITICAL: Check if the book is 'reserved' or 'sold'
        if ($book['status'] != 'available') {
            http_response_code(403); // Forbidden
            echo json_encode(["message" => "Cannot delete a book that is part of a pending or completed order."]);
            $conn->rollback();
            exit();
        }

        // 5. Delete the physical image file
        if (file_exists($image_to_delete)) {
            unlink($image_to_delete);
        }

        // 6. Delete the book record from the database
        $delete_stmt = $conn->prepare("DELETE FROM books WHERE book_id = ? AND user_id = ?");
        $delete_stmt->bind_param("ii", $book_id, $user_id);
        $delete_stmt->execute();
        
        $conn->commit();
        http_response_code(200); // OK
        echo json_encode(["message" => "Book deleted successfully."]);
        exit();

    } else {
        // Book not found or doesn't belong to the user
        http_response_code(404); // Not Found
        echo json_encode(["message" => "Book not found or you do not have permission to delete it."]);
        $conn->rollback();
        exit();
    }

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "An error occurred: " . $e->getMessage()]);
    exit();
}
?>