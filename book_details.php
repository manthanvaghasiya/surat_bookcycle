<?php
session_start();
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
require 'includes/db_connection.php';
$book_id = $_GET['id'];

// Get book details including the new 'status'
$sql = "SELECT b.*, u.full_name AS seller_name 
        FROM books b 
        JOIN users u ON b.user_id = u.user_id 
        WHERE b.book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $book = $result->fetch_assoc();
} else {
    header("Location: index.php");
    exit();
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - Surat BookCycle</title>
    <style>
        /* Add your page styles here */
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f9f9f9; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; }
        .details-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 40px; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .book-image-container img { width: 100%; border-radius: 8px; }
        .book-info h1 { margin-top: 0; }
        .book-info .author { color: #555; font-size: 1.2em; margin-bottom: 20px; }
        .book-info .price { font-size: 2em; font-weight: bold; color: #007bff; margin-bottom: 20px; }
        .info-section { margin-bottom: 15px; }
        .info-section h3 { margin: 0 0 5px 0; font-size: 1em; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-section p { margin: 0; color: #666; }
        .description { line-height: 1.6; }
        .action-btn { display: inline-block; width: 100%; padding: 15px; color: white; text-align: center; text-decoration: none; border-radius: 5px; font-size: 1.2em; margin-top: 20px; }
        .add-order-btn { background-color: #28a745; transition: background-color 0.2s; }
        .add-order-btn:hover { background-color: #218838; }
        .disabled-btn { background-color: #6c757d; cursor: not-allowed; }
        .error-message { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container">
        <?php if(isset($_GET['error'])): ?>
            <p class="error-message">Sorry, this book is no longer available or you cannot purchase your own listing.</p>
        <?php endif; ?>
        <div class="details-grid">
            <div class="book-image-container">
                <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
            </div>
            <div class="book-info">
                <h1><?php echo htmlspecialchars($book['title']); ?></h1>
                <p class="author">by <?php echo htmlspecialchars($book['author']); ?></p>
                <p class="price">₹<?php echo htmlspecialchars($book['price']); ?></p>
                
                <div class="info-section">
                    <h3>Seller</h3>
                    <p><?php echo htmlspecialchars($book['seller_name']); ?></p>
                </div>
                <div class="info-section">
                    <h3>Condition</h3>
                    <p><?php echo htmlspecialchars($book['book_condition']); ?></p>
                </div>
                <div class="info-section">
                    <h3>Description</h3>
                    <p class="description"><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                </div>

                <?php 
                // Button Logic
                if ($book['status'] != 'available') {
                    echo '<a href="#" class="action-btn disabled-btn">Not Available</a>';
                } elseif (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $book['user_id']) {
                    echo '<a href="#" class="action-btn disabled-btn">This is Your Listing</a>';
                } elseif (isset($_SESSION['user_id'])) {
                    echo '<a href="add_to_order.php?id=' . $book['book_id'] . '" class="action-btn add-order-btn">Add to My Orders</a>';
                } else {
                    echo '<a href="login.php" class="action-btn add-order-btn">Login to Order</a>';
                }
                ?>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
