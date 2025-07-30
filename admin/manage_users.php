<?php 
// This includes the session check, database connection, and sidebar.
include 'admin_header.php'; 

// Fetch all users from the database, ordering by registration date
$sql = "SELECT user_id, full_name, email, created_at, is_admin FROM users ORDER BY created_at DESC";
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
    .admin-yes {
        color: #28a745;
        font-weight: bold;
    }
    .action-btn {
        text-decoration: none;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        background-color: #dc3545; /* Red for delete */
    }
    .disabled-btn {
        background-color: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .message { text-align: center; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }
</style>

<h1>Manage Users</h1>
<p>Here is a list of all registered users on Surat BookCycle.</p>

<!-- To show status messages -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
    <div class="message success">The user and all their data have been successfully deleted.</div>
<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] == 'self_delete'): ?>
    <div class="message error">You cannot delete your own admin account.</div>
<?php endif; ?>

<table class="content-table">
    <thead>
        <tr>
            <th>User ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Is Admin?</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="<?php echo $user['is_admin'] ? 'admin-yes' : ''; ?>">
                        <?php echo $user['is_admin'] ? 'Yes' : 'No'; ?>
                    </td>
                    <td>
                        <?php 
                        // !! UPDATED LOGIC !!
                        // Prevent deletion if the user is an admin
                        if ($user['is_admin']): ?>
                            <a href="#" class="action-btn disabled-btn" title="Admin accounts cannot be deleted.">Delete</a>
                        <?php else: ?>
                            <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" class="action-btn" onclick="return confirm('WARNING: This will permanently delete the user and all their book listings and orders. Are you absolutely sure?');">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No users found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php 
$conn->close();
include 'admin_footer.php'; 
?>
