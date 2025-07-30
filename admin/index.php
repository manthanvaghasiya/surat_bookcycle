<?php 
// This includes the session check and database connection
include 'admin_header.php'; 

// --- Fetch Statistics ---

// 1. Get total number of users
$users_result = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$total_users = $users_result->fetch_assoc()['total_users'];

// 2. Get total number of book listings
$books_result = $conn->query("SELECT COUNT(*) AS total_books FROM books");
$total_books = $books_result->fetch_assoc()['total_books'];

$conn->close();
?>
<style>
    /* Additional styles for the dashboard cards */
    .stats-container { display: flex; gap: 20px; }
    .stat-card { flex: 1; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .stat-card h4 { margin-top: 0; color: #6c757d; }
    .stat-card .stat-number { font-size: 2.5em; font-weight: bold; color: #343a40; }
</style>

<h1>Dashboard</h1>

<div class="stats-container">
    <div class="stat-card">
        <h4>Total Users</h4>
        <p class="stat-number"><?php echo $total_users; ?></p>
    </div>
    <div class="stat-card">
        <h4>Total Book Listings</h4>
        <p class="stat-number"><?php echo $total_books; ?></p>
    </div>
</div>

<!-- We will add more content here later -->

<?php include 'admin_footer.php'; // We will create this file next ?>
