<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

require 'includes/db_connection.php';

$book_id = $_GET['id'];
$buyer_user_id = $_SESSION['user_id'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ? AND status = 'available' FOR UPDATE");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $book = $result->fetch_assoc();
        
        if ($book['user_id'] == $buyer_user_id) {
            throw new Exception("Cannot buy your own book.");
        }

        // Mark the book as 'reserved'
        $update_book_stmt = $conn->prepare("UPDATE books SET status = 'reserved' WHERE book_id = ?");
        $update_book_stmt->bind_param("i", $book_id);
        $update_book_stmt->execute();

        // Create a new 'Pending' order
        $order_stmt = $conn->prepare("INSERT INTO orders (buyer_user_id, total_price, order_status) VALUES (?, ?, 'Pending')");
        $order_stmt->bind_param("id", $buyer_user_id, $book['price']);
        $order_stmt->execute();
        $new_order_id = $conn->insert_id;

        // !! UPDATED: Add the book_id to order_items !!
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, book_title, book_author, book_price, seller_user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $item_stmt->bind_param("iisssi", $new_order_id, $book_id, $book['title'], $book['author'], $book['price'], $book['user_id']);
        $item_stmt->execute();

        $conn->commit();
        header("Location: my_orders.php?status=added");
        exit();

    } else {
        throw new Exception("Book is no longer available.");
    }

} catch (Exception $e) {
    $conn->rollback();
    header("Location: book_details.php?id=$book_id&error=not_available");
    exit();
}
?>
