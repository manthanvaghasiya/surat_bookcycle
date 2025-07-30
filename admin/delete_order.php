<?php
session_start();

// Security: Ensure an admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Security: Ensure an order ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_orders.php");
    exit();
}

require '../includes/db_connection.php';

$order_id_to_delete = $_GET['id'];

// Use a transaction to ensure both tables are updated correctly
$conn->begin_transaction();

try {
    // 1. Delete the items associated with the order from the 'order_items' table
    $items_stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $items_stmt->bind_param("i", $order_id_to_delete);
    $items_stmt->execute();
    $items_stmt->close();

    // 2. Delete the main order record from the 'orders' table
    $order_stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $order_stmt->bind_param("i", $order_id_to_delete);
    $order_stmt->execute();
    $order_stmt->close();

    // If both deletions were successful, commit the transaction
    $conn->commit();
    header("Location: manage_orders.php?status=deleted");
    exit();

} catch (mysqli_sql_exception $exception) {
    // If any part fails, roll back all changes to prevent partial deletion
    $conn->rollback();
    header("Location: manage_orders.php?error=delete_failed");
    exit();
}
?>