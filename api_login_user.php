<?php
// Allow requests from your React app's origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

require 'includes/db_connection.php';

// Get the posted data from the React app
$data = json_decode(file_get_contents("php://input"));

// Basic validation
if (!isset($data->email) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode(["message" => "Email and password are required."]);
    exit();
}

$email = trim($data->email);
$password = trim($data->password);

// Prepare SQL to prevent injection
$stmt = $conn->prepare("SELECT user_id, full_name, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    
    // Verify the hashed password
    if (password_verify($password, $user['password'])) {
        // Password is correct, send back user data (without the password hash)
        http_response_code(200);
        echo json_encode([
            "message" => "Login successful.",
            "user" => [
                "user_id" => $user['user_id'],
                "full_name" => $user['full_name']
            ]
        ]);
    } else {
        // Incorrect password
        http_response_code(401); // Unauthorized
        echo json_encode(["message" => "Invalid email or password."]);
    }
} else {
    // No user found with that email
    http_response_code(401);
    echo json_encode(["message" => "Invalid email or password."]);
}

$stmt->close();
$conn->close();
?>
