<?php
$servername = "rei.cs.ndsu.nodak.edu";
$username = "garrett_bengtson_371s25";
$password = "bjkmHtGQlN0!";
$database = "garrett_bengtson_db371s25";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
