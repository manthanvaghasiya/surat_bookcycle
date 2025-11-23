<?php
// Allow requests from your React app's origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow Authorization header
header("Content-Type: application/json; charset=UTF-8");

require 'includes/db_connection.php';

// --- This is a simple way to get the user ID from a real session ---
// In a real app, you'd use a more secure token-based authentication.
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["message" => "You must be logged in to list a book."]);
    exit();
}
$user_id = $_SESSION['user_id'];
// --- End of session check ---

// Get form data from POST request
$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
$price = $_POST['price'] ?? '';
// ... get other fields

// --- Image Upload Logic ---
$image_path = '';
if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
    $target_dir = "uploads/";
    $image_filename = uniqid() . '_' . basename($_FILES["book_image"]["name"]);
    $target_file = $target_dir . $image_filename;
    
    if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_file)) {
        $image_path = $target_file;
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Sorry, there was an error uploading your file."]);
        exit();
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Book image is required."]);
    exit();
}
// --- End Image Upload Logic ---

// Insert into database
$stmt = $conn->prepare("INSERT INTO books (user_id, title, author, price, image_path, status) VALUES (?, ?, ?, ?, ?, 'available')");
$stmt->bind_param("isids", $user_id, $title, $author, $price, $image_path);

if ($stmt->execute()) {
    http_response_code(201); // Created
    echo json_encode(["message" => "Book listed successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Error: Could not list the book."]);
}

$stmt->close();
$conn->close();
?>
