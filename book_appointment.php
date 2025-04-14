<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connection.php';
require 'StudentQuery.php';

if (!isset($_SESSION["student_id"])) {
    header("Location: studentlogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slot_id']) && isset($_POST['group_id'])) {
    $slotID = $_POST['slot_id'];
    $groupID = $_POST['group_id'];
    $query = new StudentQuery();

    $statements = $query->scheduleAppointment($slotID, $groupID);

    if (!is_array($statements)) {
        echo "<p>Error: scheduleAppointment() did not return an array.</p>";
        var_dump($statements);
        exit();
    }

    foreach ($statements as $sql) {
        if (!$conn->query($sql)) {
            echo "<p>SQL Error:</p><pre>$sql</pre>";
            echo "<p>MySQL error: " . $conn->error . "</p>";
            exit();
        }
    }

    header("Location: studentdashboard.php?success=Booked");
    exit();
} else {
    echo "<p>Missing slot_id or group_id from form.</p>";
    var_dump($_POST);
    exit();
}
