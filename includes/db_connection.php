<?php
$servername = "127.0.0.1"; // This is correct from before
$username = "root";
$password = ""; // Default password for XAMPP is empty
$dbname = "bookcycle_db";
$port = 3307; // <-- THIS IS THE FIX

// Create connection
// We add the $port variable to the end
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // You can uncomment this to test
?>