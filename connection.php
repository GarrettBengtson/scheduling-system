<?php
$servername = "rei.cs.ndsu.nodak.edu";
$username = "zachary_s_weinzierl_371s25";
$password = "2VkstZKnYN0!";
$database = "zachary_s_weinzierl_db371s25";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
