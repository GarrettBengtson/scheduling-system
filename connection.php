<?php
$servername = "rei.cs.ndsu.nodak.edu";
$username = "caleb_scott_371s25";
$password = "sh52c7RBvN0!";
$database = "caleb_scott_db371s25";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
