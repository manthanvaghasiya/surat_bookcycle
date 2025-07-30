<!-- <?php
session_start();

// User must be logged in to purchase
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Book ID must be provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

require 'includes/db_connection.php';

$book_id = $_GET['id'];
$buyer_user_id = $_SESSION['user_id'];

// --- Get Book Details ---
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $book = $result->fetch_assoc();
    
    // Prevent user from buying their own book
    if ($book['user_id'] == $buyer_user_id) {
        header("Location: book_details.php?id=$book_id&error=own_book");
        exit();
    }

    // Start a transaction to ensure all queries succeed or none do
    $conn->begin_transaction();

    try {
        // 1. Create a new order
        $order_stmt = $conn->prepare("INSERT INTO orders (buyer_user_id, total_price) VALUES (?, ?)");
        $order_stmt->bind_param("id", $buyer_user_id, $book['price']);
        $order_stmt->execute();
        $new_order_id = $conn->insert_id; // Get the ID of the new order

        // 2. Add the book to order_items
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, book_title, book_author, book_price, seller_user_id) VALUES (?, ?, ?, ?, ?)");
        $item_stmt->bind_param("issdi", $new_order_id, $book['title'], $book['author'], $book['price'], $book['user_id']);
        $item_stmt->execute();

        // 3. Delete the original book listing
        $delete_stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
        $delete_stmt->bind_param("i", $book_id);
        $delete_stmt->execute();

        // 4. Delete the physical image file
        if (file_exists($book['image_path'])) {
            unlink($book['image_path']);
        }

        // If all queries were successful, commit the transaction
        $conn->commit();

        // Redirect to a new "My Orders" page
        header("Location: my_orders.php?status=success");
        exit();

    } catch (mysqli_sql_exception $exception) {
        // If any query fails, roll back the changes
        $conn->rollback();
        // Redirect with an error
        header("Location: book_details.php?id=$book_id&error=purchase_failed");
        exit();
    }

} else {
    // Book not found
    header("Location: index.php");
    exit();
}
?> -->
//not usable file 