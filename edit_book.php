<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the book ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

require 'includes/db_connection.php';

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// --- Handle Form Submission (POST Request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $condition = $_POST['condition'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // --- !! FIX STARTS HERE !! ---
    // First, get the current image path from the database to ensure we don't lose it.
    $path_stmt = $conn->prepare("SELECT image_path FROM books WHERE book_id = ? AND user_id = ?");
    $path_stmt->bind_param("ii", $book_id, $user_id);
    $path_stmt->execute();
    $path_result = $path_stmt->get_result();
    $current_book = $path_result->fetch_assoc();
    $image_path = $current_book['image_path']; // Start with the existing path
    $path_stmt->close();
    // --- !! FIX ENDS HERE !! ---

    // Check if a new image was uploaded
    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
        // A new file was uploaded, process it
        $target_dir = "uploads/";
        $image_filename = uniqid() . '_' . basename($_FILES["book_image"]["name"]);
        $target_file = $target_dir . $image_filename;
        
        if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_file)) {
            // New image uploaded successfully, delete the old one
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $image_path = $target_file; // Set the new image path for the database update
        } else {
            $error_message = "Sorry, there was an error uploading your new file.";
        }
    }

    // If there was no upload error, update the database
    if (empty($error_message)) {
        $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, genre = ?, book_condition = ?, price = ?, description = ?, image_path = ? WHERE book_id = ? AND user_id = ?");
        $stmt->bind_param("ssssdssii", $title, $author, $genre, $condition, $price, $description, $image_path, $book_id, $user_id);

        if ($stmt->execute()) {
            header("Location: dashboard.php?status=updated");
            exit();
        } else {
            $error_message = "Error: Could not update the book.";
        }
        $stmt->close();
    }
}

// --- Fetch Existing Book Data (GET Request) ---
// This part runs when the page first loads
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ? AND user_id = ?");
$stmt->bind_param("ii", $book_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $book = $result->fetch_assoc();
} else {
    // Book not found or doesn't belong to user
    header("Location: dashboard.php");
    exit();
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Surat BookCycle</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 80px; }
        button { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .current-image-preview { font-weight: bold; margin-bottom: 10px; }
        .current-image-preview img { max-width: 100px; border-radius: 4px; display: block; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Your Book Listing</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="edit_book.php?id=<?php echo $book_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Book Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($book['genre']); ?>">
            </div>
            <div class="form-group">
                <label for="condition">Condition</label>
                <select id="condition" name="condition" required>
                    <option value="Like New" <?php if($book['book_condition'] == 'Like New') echo 'selected'; ?>>Like New</option>
                    <option value="Good" <?php if($book['book_condition'] == 'Good') echo 'selected'; ?>>Good</option>
                    <option value="Acceptable" <?php if($book['book_condition'] == 'Acceptable') echo 'selected'; ?>>Acceptable</option>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price (₹)</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($book['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($book['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="book_image">Upload New Image (Optional)</label>
                <div class="current-image-preview">
                    Current Image:
                    <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="Current book cover">
                </div>
                <input type="file" id="book_image" name="book_image" accept="image/png, image/jpeg">
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
