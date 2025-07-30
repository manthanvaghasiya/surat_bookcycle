<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db_connection.php';
$buyer_user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE buyer_user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $buyer_user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Surat BookCycle</title>
    <style>
        /* !! LAYOUT FIX STARTS HERE !! */
        html, body {
            height: 100%;
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            background-color: #f9f9f9; 
            display: flex;
            flex-direction: column;
        }
        .content-wrap {
            flex: 1 0 auto; /* This makes the main content area grow to push the footer down */
        }
        /* !! LAYOUT FIX ENDS HERE !! */

        .container { max-width: 900px; margin: 30px auto; }
        h1 { text-align: center; margin-bottom: 30px; }
        .order-card { background-color: #fff; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .order-header { background-color: #f8f9fa; padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
        .order-body { padding: 20px; }
        .order-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee; }
        .order-item:last-child { border-bottom: none; }
        .action-buttons { display: flex; gap: 10px; margin-top: 15px; }
        .action-btn { flex: 1; text-align: center; color: white; padding: 12px; text-decoration: none; border-radius: 5px; }
        .confirm-btn { background-color: #28a745; }
        .cancel-btn { background-color: #dc3545; }
        .status { padding: 5px 10px; border-radius: 15px; font-size: 0.8em; }
        .status-pending { background-color: #ffc107; color: #333; }
        .status-completed { background-color: #198754; color: white; }
        .message { text-align: center; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; }
        .cancelled { background-color: #f8d7da; color: #721c24; }
        .no-orders { text-align: center; padding: 40px; background-color: #fff; border-radius: 8px; }
    </style>
</head>
<body>
    <!-- We wrap the main content in a div to apply the flex-grow property -->
    <div class="content-wrap">
        <?php include 'includes/header.php'; ?>
        <div class="container">
            <h1>My Orders</h1>
            <?php if (isset($_GET['status']) && $_GET['status'] == 'confirmed'): ?>
                <div class="message success">Purchase confirmed! Thank you.</div>
            <?php endif; ?>
            <?php if (isset($_GET['status']) && $_GET['status'] == 'cancelled'): ?>
                <div class="message cancelled">Your pending order has been cancelled.</div>
            <?php endif; ?>

            <?php if ($orders_result->num_rows > 0): ?>
                <?php while($order = $orders_result->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span>Order #<?php echo $order['order_id']; ?></span>
                            <span>Date: <?php echo date('d M Y', strtotime($order['order_date'])); ?></span>
                            <span class="status <?php echo $order['order_status'] == 'Pending' ? 'status-pending' : 'status-completed'; ?>">
                                <?php echo htmlspecialchars($order['order_status']); ?>
                            </span>
                        </div>
                        <div class="order-body">
                            <?php
                            $order_items_sql = "SELECT * FROM order_items WHERE order_id = ?";
                            $items_stmt = $conn->prepare($order_items_sql);
                            $items_stmt->bind_param("i", $order['order_id']);
                            $items_stmt->execute();
                            $items_result = $items_stmt->get_result();
                            
                            while($item = $items_result->fetch_assoc()):
                            ?>
                                <div class="order-item">
                                    <div class="item-details">
                                        <?php echo htmlspecialchars($item['book_title']); ?>
                                        <span>by <?php echo htmlspecialchars($item['book_author']); ?></span>
                                    </div>
                                    <div class="item-price">
                                        ₹<?php echo htmlspecialchars($item['book_price']); ?>
                                    </div>
                                </div>
                            <?php endwhile; $items_stmt->close(); ?>
                            
                            <?php if ($order['order_status'] == 'Pending'): ?>
                                <div class="action-buttons">
                                    <a href="cancel_order.php?order_id=<?php echo $order['order_id']; ?>" class="action-btn cancel-btn" onclick="return confirm('Are you sure you want to cancel this pending order?');">Cancel Order</a>
                                    <a href="confirm_purchase.php?order_id=<?php echo $order['order_id']; ?>" class="action-btn confirm-btn" onclick="return confirm('This will finalize your purchase. Continue?');">Confirm Purchase</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                 <div class="no-orders">
                    <p>You haven't placed any orders yet.</p>
                    <a href="index.php">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
