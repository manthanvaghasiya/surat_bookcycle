<?php
$servername = "localhost";
$username = "root";
$password = ""; // Default password for XAMPP is empty
$dbname = "bookcycle_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // You can uncomment this to test
?>