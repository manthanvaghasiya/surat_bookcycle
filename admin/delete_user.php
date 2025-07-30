<?php
session_start();

// Security: Ensure an admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Security: Ensure a user ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_to_delete_id = $_GET['id'];
$admin_id = $_SESSION['admin_id'];

// Security: Prevent an admin from deleting their own account
if ($user_to_delete_id == $admin_id) {
    header("Location: manage_users.php?error=self_delete");
    exit();
}

require '../includes/db_connection.php';

// Use a transaction to ensure all or nothing is deleted
$conn->begin_transaction();

try {
    // 1. Find and delete all book images uploaded by the user
    $img_stmt = $conn->prepare("SELECT image_path FROM books WHERE user_id = ?");
    $img_stmt->bind_param("i", $user_to_delete_id);
    $img_stmt->execute();
    $images = $img_stmt->get_result();
    while ($img = $images->fetch_assoc()) {
        $file_path = '../' . $img['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    $img_stmt->close();

    // 2. Delete all book listings by the user
    $books_stmt = $conn->prepare("DELETE FROM books WHERE user_id = ?");
    $books_stmt->bind_param("i", $user_to_delete_id);
    $books_stmt->execute();
    $books_stmt->close();

    // 3. Find all orders placed by the user to delete their items
    $order_stmt = $conn->prepare("SELECT order_id FROM orders WHERE buyer_user_id = ?");
    $order_stmt->bind_param("i", $user_to_delete_id);
    $order_stmt->execute();
    $orders = $order_stmt->get_result();
    while ($order = $orders->fetch_assoc()) {
        $items_stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $items_stmt->bind_param("i", $order['order_id']);
        $items_stmt->execute();
        $items_stmt->close();
    }
    $order_stmt->close();

    // 4. Delete all orders placed by the user
    $orders_main_stmt = $conn->prepare("DELETE FROM orders WHERE buyer_user_id = ?");
    $orders_main_stmt->bind_param("i", $user_to_delete_id);
    $orders_main_stmt->execute();
    $orders_main_stmt->close();

    // 5. Finally, delete the user themselves
    $user_stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $user_stmt->bind_param("i", $user_to_delete_id);
    $user_stmt->execute();
    $user_stmt->close();

    // If all queries were successful, commit the transaction
    $conn->commit();
    header("Location: manage_users.php?status=deleted");
    exit();

} catch (mysqli_sql_exception $exception) {
    // If any query fails, roll back the changes
    $conn->rollback();
    header("Location: manage_users.php?error=delete_failed");
    exit();
}
?>