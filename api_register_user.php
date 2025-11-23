<?php
// Allow requests from your React app's origin
header("Access-Control-Allow-Origin: *");
// Allow POST method
header("Access-Control-Allow-Methods: POST");
// Allow Content-Type header
header("Access-Control-Allow-Headers: Content-Type");
// Set the content type of the response to JSON
header("Content-Type: application/json; charset=UTF-8");

require 'includes/db_connection.php';

// Get the posted data from the React app
$data = json_decode(file_get_contents("php://input"));

// Basic validation
if (
    !isset($data->fullname) || !isset($data->email) || !isset($data->password) ||
    empty(trim($data->fullname)) || empty(trim($data->email)) || empty(trim($data->password))
) {
    // Send an error response
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Please fill all the required fields."]);
    exit();
}

$fullname = trim($data->fullname);
$email = trim($data->email);
$password = trim($data->password);

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $fullname, $email, $hashed_password);

try {
    if ($stmt->execute()) {
        // Send a success response
        http_response_code(201); // Created
        echo json_encode(["message" => "User was successfully registered."]);
    }
} catch (mysqli_sql_exception $e) {
    // Check for duplicate email error
    if ($e->getCode() == 1062) {
        http_response_code(409); // Conflict
        echo json_encode(["message" => "This email address is already registered."]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => "An error occurred during registration."]);
    }
}

$stmt->close();
$conn->close();
?>
