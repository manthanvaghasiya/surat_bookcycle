<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'includes/db_connection.php';

    // Get form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $condition = $_POST['condition'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Server-side validation for description length
    if (strlen($description) > 600) {
        $error_message = "Description cannot exceed 600 characters.";
    }

    // Image Upload Logic
    $image_path = '';
    if (empty($error_message)) {
        if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
            $target_dir = "uploads/";
            $image_filename = uniqid() . '_' . basename($_FILES["book_image"]["name"]);
            $target_file = $target_dir . $image_filename;
            
            if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error_message = "Book image is required.";
        }
    }

    // If image upload was successful and no errors, insert into database
    if (empty($error_message) && !empty($image_path)) {
        $stmt = $conn->prepare("INSERT INTO books (user_id, title, author, genre, book_condition, price, description, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        // --- !! THE FIX IS HERE !! ---
        // The 7th parameter 'd' (double) has been changed to 's' (string) for the description.
        $stmt->bind_param("isssssss", $user_id, $title, $author, $genre, $condition, $price, $description, $image_path);

        if ($stmt->execute()) {
            header("Location: dashboard.php?status=success");
            exit();
        } else {
            $error_message = "Error: Could not list the book.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List a Book - Surat BookCycle</title>
    <!-- Add your self-contained styles here if not using style.css -->
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 80px; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .char-counter { text-align: right; font-size: 0.8em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h2>List Your Book for Sale</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="list_book.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Book Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre">
            </div>
            <div class="form-group">
                <label for="condition">Condition</label>
                <select id="condition" name="condition" required>
                    <option value="Like New">Like New</option>
                    <option value="Good">Good</option>
                    <option value="Acceptable">Acceptable</option>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price (₹)</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" maxlength="600" oninput="updateCounter()"></textarea>
                <div id="char-counter" class="char-counter">600 characters remaining</div>
            </div>
            <div class="form-group">
                <label for="book_image">Book Image</label>
                <input type="file" id="book_image" name="book_image" accept="image/png, image/jpeg" required>
            </div>
            <button type="submit">List My Book</button>
        </form>
    </div>

    <script>
        function updateCounter() {
            const textarea = document.getElementById('description');
            const counter = document.getElementById('char-counter');
            const remaining = textarea.maxLength - textarea.value.length;
            counter.textContent = remaining + ' characters remaining';
        }
        updateCounter();
    </script>

</body>
</html>
