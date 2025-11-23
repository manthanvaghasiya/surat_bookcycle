<?php
// Set headers to allow cross-origin requests (from your React app to your PHP app)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require 'includes/db_connection.php';

// Select only available books
$sql = "SELECT book_id, title, author, price, image_path FROM books WHERE status = 'available' ORDER BY listed_at DESC";
$result = $conn->query($sql);

$books = [];
if ($result->num_rows > 0) {
    // Fetch all books into an array
    while($row = $result->fetch_assoc()) {
        // We need to create a full URL for the image path
        $row['image_path'] = "http://localhost/surat_bookcycle/" . $row['image_path'];
        $books[] = $row;
    }
}

// Close the connection
$conn->close();

// Encode the array into JSON and output it
echo json_encode($books);
?>
