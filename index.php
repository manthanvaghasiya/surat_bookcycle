<?php
require 'includes/db_connection.php';

// --- Search Logic ---
$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

// !! UPDATED QUERY: Select ALL books, regardless of status !!
$sql = "SELECT * FROM books";
$params = [];
$types = '';

// If a search term is provided, add a WHERE clause to the query
if (!empty($search_term)) {
    $sql .= " WHERE title LIKE ? OR author LIKE ?";
    $search_param = "%" . $search_term . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

$sql .= " ORDER BY listed_at DESC";

$stmt = $conn->prepare($sql);

// If there are parameters, bind them
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
// --- End of Updated Logic ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat BookCycle - Home</title>
    <style>
        /* Your self-contained styles for the homepage */
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f9f9f9; }
        .container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        .header-text { text-align: center; margin: 40px 0; }
        .search-container { margin: 30px 0; text-align: center; }
        .search-container input[type="text"] { width: 50%; padding: 12px; border: 1px solid #ccc; border-radius: 25px; font-size: 1em; }
        .search-container button { padding: 12px 20px; border: none; background-color: #007bff; color: white; border-radius: 25px; cursor: pointer; margin-left: -90px; }
        .search-results-header { margin-top: 20px; }
        .book-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .book-card-link { text-decoration: none; color: inherit; position: relative; display: block; }
        .book-card { background-color: #fff; border: 1px solid #ddd; border-radius: 8px; text-align: center; padding: 15px; transition: box-shadow 0.3s; height: 100%; display: flex; flex-direction: column; justify-content: space-between; }
        .book-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .book-card img { max-width: 100%; height: 200px; object-fit: cover; border-radius: 4px; }
        .book-card h3 { margin: 10px 0 5px 0; font-size: 1em; }
        .book-card p { margin: 0; color: #555; font-size: 0.9em; }
        .book-card h4 { margin: 10px 0 0 0; color: #007bff; }
        .no-books { text-align: center; color: #888; font-size: 1.2em; grid-column: 1 / -1; }
        /* !! NEW STYLE for the 'Not Available' overlay !! */
        .not-available-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2em;
            font-weight: bold;
            color: #dc3545;
            border-radius: 8px;
            pointer-events: none; /* Allows clicks to go through to the link if needed, but we disable the link anyway */
        }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="header-text">
            <h1>Welcome to Surat BookCycle</h1>
            <p>Your community marketplace for used books.</p>
        </div>

        <div class="search-container">
            <form action="index.php" method="GET">
                <input type="text" name="search" placeholder="Search for a book title or author..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <h2 class="search-results-header">
            <?php
            if (!empty($search_term)) {
                echo 'Search Results for "' . htmlspecialchars($search_term) . '"';
            } else {
                echo 'All Books';
            }
            ?>
        </h2>

        <div class="book-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Check if the book is available
                    $is_available = ($row['status'] == 'available');
                    // If the book is not available, the link will not work
                    $link = $is_available ? 'book_details.php?id=' . $row["book_id"] : '#';
                    $link_class = $is_available ? 'book-card-link' : 'book-card-link disabled';

                    echo '<a href="' . $link . '" class="' . $link_class . '">';
                    echo '<div class="book-card">';
                    echo '<div>';
                    echo '<img src="' . htmlspecialchars($row["image_path"]) . '" alt="' . htmlspecialchars($row["title"]) . '">';
                    echo '<h3>' . htmlspecialchars($row["title"]) . '</h3>';
                    echo '<p>by ' . htmlspecialchars($row["author"]) . '</p>';
                    echo '</div>';
                    echo '<h4>Rs.' . htmlspecialchars($row["price"]) . '</h4>';
                    echo '</div>';
                    
                    // If the book is not available, add the overlay
                    if (!$is_available) {
                        echo '<div class="not-available-overlay">Not Available</div>';
                    }

                    echo '</a>';
                }
            } else {
                echo "<p class='no-books'>No books found.</p>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
