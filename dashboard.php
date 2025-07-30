<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in. If not, redirect them to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); // Stop executing the script
}

require 'includes/db_connection.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// Fetch the books listed by the current user
$sql = "SELECT book_id, title, price, image_path FROM books WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// We need to store the result to use it twice (for the message and the list)
$user_books = [];
while ($row = $result->fetch_assoc()) {
    $user_books[] = $row;
}
$book_count = count($user_books);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Surat BookCycle</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f9f9f9; }
        .container { padding: 30px; }
        .action-box { background-color: #e9f7ff; border: 1px solid #bce8f1; border-radius: 8px; padding: 20px; text-align: center; max-width: 400px; margin: 20px auto; }
        .action-box h3 { margin-top: 0; color: #31708f; }
        .action-box p { color: #31708f; }
        .action-box a.btn { display: inline-block; text-decoration: none; background-color: #007bff; color: white; padding: 12px 25px; border-radius: 4px; font-size: 1em; margin-top: 10px; }
        .message { text-align: center; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; }
        .deleted { background-color: #f8d7da; color: #721c24; }
        .listings-section h2 { border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .book-list-item { display: flex; align-items: center; background-color: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .book-list-item img { width: 60px; height: 80px; object-fit: cover; border-radius: 4px; margin-right: 15px; }
        .book-info { flex-grow: 1; }
        .book-info h4 { margin: 0 0 5px 0; }
        .book-actions a { text-decoration: none; color: white; padding: 6px 12px; border-radius: 4px; margin-left: 10px; font-size: 0.9em; }
        .edit-btn { background-color: #ffc107; }
        .delete-btn { background-color: #dc3545; }
        .no-books { color: #666; }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <div class="container">
        <!-- Status Messages -->
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="message success">Your book has been listed successfully!</div>
        <?php endif; ?>
        <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
            <div class="message deleted">Your book listing has been deleted.</div>
        <?php endif; ?>
        <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
            <div class="message success">Your book listing has been updated.</div>
        <?php endif; ?>

        <div class="action-box">
            <?php 
            // !! NEW: Personalized message logic !!
            if ($book_count == 0): ?>
                <h3>Do you have a book to sell?</h3>
                <p>List it with us today.</p>
            <?php else: ?>
                <h3>Looking to sell another book?</h3>
                <p>We’re ready!</p>
            <?php endif; ?>
            <a href="list_book.php" class="btn">List a New Book</a>
        </div>
        
        <div class="listings-section">
            <h2>Your Active Listings</h2>
            <?php if ($book_count > 0): ?>
                <?php foreach($user_books as $book): ?>
                    <div class="book-list-item">
                        <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="Book Cover">
                        <div class="book-info">
                            <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                            <p>Price: ₹<?php echo htmlspecialchars($book['price']); ?></p>
                        </div>
                        <div class="book-actions">
                            <a href="edit_book.php?id=<?php echo $book['book_id']; ?>" class="edit-btn">Edit</a>
                            <a href="delete_book.php?id=<?php echo $book['book_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this listing?');">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-books">You have not listed any books yet.</p>
            <?php endif; ?>
        </div>

    </div>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
