<?php 
// This includes the session check, database connection, and sidebar.
include 'admin_header.php'; 

// Fetch all books and join with the users table to get the seller's name
$sql = "SELECT b.book_id, b.title, b.author, b.price, b.listed_at, u.full_name AS seller_name 
        FROM books b 
        JOIN users u ON b.user_id = u.user_id 
        ORDER BY b.listed_at DESC";
$result = $conn->query($sql);
?>
<style>
    /* Using the same table style as manage_users.php for consistency */
    .content-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 0.9em;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .content-table thead tr {
        background-color: #007bff;
        color: #ffffff;
        text-align: left;
        font-weight: bold;
    }
    .content-table th, .content-table td {
        padding: 12px 15px;
    }
    .content-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }
    .content-table tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }
    .content-table tbody tr:last-of-type {
        border-bottom: 2px solid #007bff;
    }
    .action-btn {
        text-decoration: none;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        background-color: #dc3545; /* Red for delete */
    }
    .message { text-align: center; padding: 10px; border-radius: 4px; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; }
</style>

<h1>Manage Book Listings</h1>
<p>Here is a list of all books currently listed on the platform.</p>

<!-- To show a success message after deletion -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
    <div class="message">The book listing has been successfully deleted.</div>
<?php endif; ?>

<table class="content-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Price</th>
            <th>Seller</th>
            <th>Listed On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($book = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $book['book_id']; ?></td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td>₹<?php echo htmlspecialchars($book['price']); ?></td>
                    <td><?php echo htmlspecialchars($book['seller_name']); ?></td>
                    <td><?php echo date('d M Y', strtotime($book['listed_at'])); ?></td>
                    <td>
                        <a href="delete_book.php?id=<?php echo $book['book_id']; ?>" class="action-btn" onclick="return confirm('Are you sure you want to permanently delete this book listing?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No books have been listed yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php 
$conn->close();
include 'admin_footer.php'; 
?>
