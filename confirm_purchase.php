<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['order_id'])) {
    header("Location: my_orders.php");
    exit();
}

require 'includes/db_connection.php';

$order_id = $_GET['order_id'];
$buyer_user_id = $_SESSION['user_id'];

$conn->begin_transaction();

try {
    // 1. Verify the order belongs to the user and is 'Pending'
    $order_stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND buyer_user_id = ? AND order_status = 'Pending'");
    $order_stmt->bind_param("ii", $order_id, $buyer_user_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();

    if ($order_result->num_rows == 1) {
        // 2. Get the book_id from the order_items table
        $item_stmt = $conn->prepare("SELECT book_id FROM order_items WHERE order_id = ?");
        $item_stmt->bind_param("i", $order_id);
        $item_stmt->execute();
        $item_result = $item_stmt->get_result();
        $item = $item_result->fetch_assoc();
        $book_id_to_sell = $item['book_id'];

        // 3. Update the order status to 'Completed'
        $update_order_stmt = $conn->prepare("UPDATE orders SET order_status = 'Completed' WHERE order_id = ?");
        $update_order_stmt->bind_param("i", $order_id);
        $update_order_stmt->execute();

        // 4. Update the book status to 'sold'
        $update_book_stmt = $conn->prepare("UPDATE books SET status = 'sold' WHERE book_id = ?");
        $update_book_stmt->bind_param("i", $book_id_to_sell);
        $update_book_stmt->execute();
        
        $conn->commit();
        header("Location: my_orders.php?status=confirmed");
        exit();
    } else {
        // Order not found or not pending
        throw new Exception("Invalid order.");
    }

} catch (Exception $e) {
    $conn->rollback();
    header("Location: my_orders.php?error=confirmation_failed");
    exit();
}
?>
