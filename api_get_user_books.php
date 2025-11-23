<?php
// Set headers for CORS and JSON content type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require 'includes/db_connection.php';

// Get the user ID from the query string (e.g., ?user_id=5)
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "A valid user ID is required."]);
    exit();
}

// Select all books listed by this specific user
$sql = "SELECT book_id, title, author, price, image_path, status FROM books WHERE user_id = ? ORDER BY listed_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Create a full URL for the image path
        $row['image_path'] = "http://localhost/surat_bookcycle/" . $row['image_path'];
        $books[] = $row;
    }
}

$conn->close();

// Encode the array into JSON and output it
echo json_encode($books);
?>
