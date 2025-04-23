<?php
session_start();
require 'connection.php';
require __DIR__ . '/queries/StudentQuery.php';

use StudentQuery\StudentQuery;
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$studentID = $_SESSION['user_id'];
$query = new StudentQuery();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['slot_id']) && isset($_POST['group_id'])) {
    $timeSlotID = intval($_POST['slot_id']);
    $groupID = intval($_POST['group_id']);

    // get sql string from query class
    $sqlString = $query->scheduleAppointment($timeSlotID, $groupID);

    // convert string into array of seperate elements
    $statements = explode(";", $sqlString);

    $error = false;
    foreach ($statements as $sql) {
        $sql = trim($sql);
        if (!empty($sql)) {
            if (!$conn->query($sql)) {
                $error = $conn->error;
                break;
            }
        }
    }

    if ($error) {
        echo "<div class='alert alert-danger text-center'>Error booking appointment: $error</div>";
    } else {
        // redirected back to dashboard after success
        header("Location: student_dashboard.php?booked=1");
        exit();
    }
} else {
    echo "<div class='alert alert-warning text-center'>Invalid booking request.</div>";
}
?>
