<?php

session_start(); //retrieve session data

$_SESSION = []; //clearing session array

session_destroy(); //destroy the session on server
header("Location: index.php"); // Correct redirection
exit();
