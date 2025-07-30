<?php 
// This includes the session check, database connection, and sidebar.
include 'admin_header.php'; 

// Fetch all orders and join with the users table to get the buyer's name
$sql = "SELECT o.order_id, o.order_date, o.total_price, u.full_name AS buyer_name 
        FROM orders o
        JOIN users u ON o.buyer_user_id = u.user_id 
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>
<style>
    /* Using the same table style for consistency */
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
        vertical-align: top;
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
    .message { text-align: center; padding: 10px; border-radius: 4px; margin-bottom: 20px; background-color: #d4edda; color: #155724; }
    /* Styles for the items list */
    .items-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .items-list li {
        padding-bottom: 8px;
        margin-bottom: 8px;
        border-bottom: 1px solid #e9ecef;
    }
    .items-list li:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }
    .author-name {
        font-size: 0.9em;
        color: #6c757d;
        display: block;
    }
</style>

<h1>Manage Orders</h1>
<p>Here is a list of all orders placed on the platform.</p>

<?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
    <div class="message">The order has been successfully deleted.</div>
<?php endif; ?>

<table class="content-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Buyer Name</th>
            <th>Order Date</th>
            <th>Item Details</th>
            <th>Seller Name</th>
            <th>Total Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($order = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $order['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                    <td><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                    
                    <?php
                    // For each order, fetch its items, author, and the seller's name
                    $order_items_sql = "SELECT oi.book_title, oi.book_author, u.full_name AS seller_name
                                        FROM order_items oi
                                        JOIN users u ON oi.seller_user_id = u.user_id
                                        WHERE oi.order_id = ?";
                    $items_stmt = $conn->prepare($order_items_sql);
                    $items_stmt->bind_param("i", $order['order_id']);
                    $items_stmt->execute();
                    $items_result = $items_stmt->get_result();
                    
                    // Prepare arrays to hold the details for each item in the order
                    $item_details = [];
                    $sellers = [];
                    while($item = $items_result->fetch_assoc()) {
                        $item_details[] = htmlspecialchars($item['book_title']) . '<span class="author-name">by ' . htmlspecialchars($item['book_author']) . '</span>';
                        $sellers[] = htmlspecialchars($item['seller_name']);
                    }
                    $items_stmt->close();
                    ?>
                    
                    <!-- Display the details in separate columns -->
                    <td><ul class="items-list"><li><?php echo implode('</li><li>', $item_details); ?></li></ul></td>
                    <td><ul class="items-list"><li><?php echo implode('</li><li>', $sellers); ?></li></ul></td>

                    <td>₹<?php echo htmlspecialchars($order['total_price']); ?></td>
                    <td>
                        <a href="delete_order.php?id=<?php echo $order['order_id']; ?>" class="action-btn" onclick="return confirm('Are you sure you want to permanently delete this order record?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No orders have been placed yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php 
$conn->close();
include 'admin_footer.php'; 
?>
