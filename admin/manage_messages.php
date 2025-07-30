<?php 
// This includes the session check, database connection, and sidebar.
include 'admin_header.php'; 

// Fetch all messages from the database, newest first
$sql = "SELECT * FROM contact_messages ORDER BY sent_at DESC";
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
        margin-right: 5px;
    }
    .reply-btn {
        background-color: #28a745; /* Green for reply */
    }
    .delete-btn {
        background-color: #dc3545; /* Red for delete */
    }
    .message { text-align: center; padding: 10px; border-radius: 4px; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; }
</style>

<h1>Contact Form Messages</h1>
<p>Here are all the messages submitted through the contact form.</p>

<!-- To show a success message after deletion -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
    <div class="message">The message has been successfully deleted.</div>
<?php endif; ?>

<table class="content-table">
    <thead>
        <tr>
            <th>From</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Received On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($msg = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($msg['name']); ?></td>
                    <td><?php echo htmlspecialchars($msg['email']); ?></td>
                    <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
                    <td><?php echo date('d M Y', strtotime($msg['sent_at'])); ?></td>
                    <td>
                        <!-- !! NEW: Clickable Reply Button !! -->
                        <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=RE: <?php echo htmlspecialchars($msg['subject']); ?>" class="action-btn reply-btn">Reply</a>
                        
                        <a href="delete_message.php?id=<?php echo $msg['message_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">There are no messages.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php 
$conn->close();
include 'admin_footer.php'; 
?>
