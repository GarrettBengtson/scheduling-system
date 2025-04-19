<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/queries/InstructorQuery.php';

use InstructorQuery\InstructorQuery;

$queries = new InstructorQuery($conn);

session_start();

$timeslots =  $queries->getAllFutureAvailableTimeSlots($_SESSION["instructor_id"]);
$groups = $queries->getAllGroups();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $timeslotID = htmlspecialchars($_POST["timeslot"]);
    $groupID = htmlspecialchars($_POST["group"]);

    if ($timeslotID == "Select Timeslot" || $groupID == "Select Group") {
        echo "Please select a valid timeslot and group.";
    } else {
        $result = $queries->setupAppointment($timeslotID, $groupID);
        echo $result;
        echo "Appointment scheduled successfully.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Schedule Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <script>
        function validateForm() {
            const timeslot = document.getElementById("timeslot").value;
            const group = document.getElementById("group").value;

            if (timeslot === "Select Timeslot" || group === "Select Group") {
                alert("Please select a valid timeslot and group.");
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <?php include("./components/header.php"); ?>
    <div class="bg">
        <div class="container center-content">
            <div class="card text-center">
                <?php
                if (!isset($_SESSION["loggedin"])) {
                    echo '<div class="card-header ndsu-green">' .
                        '<h1 class="header">Not Logged In</h1>' .
                        '</div>';
                    echo "<p>Please return home.</p>";
                    echo '<p><a href="logout.php" class="btn btn-ndsu">Go Home</a></p>';
                } else {
                    echo '<div class="card-header ndsu-green">' .
                        '<h1 class="header">Schedule Appointment</h1>' .
                        '</div>';
                    echo '<div class="card-body">';
                    // Table to display available timeslots
                    echo '<div class="card-section">';
                    print '<h2 class="section-header">Future Available Timeslots</h2>';
                    print '<table class="table table-success">';
                    print '<thead>';
                    print '<tr>';
                    print '<th scope="col">Start Time</th>';
                    print '<th scope="col">End Time</th>';
                    print '<th scope="col">Date</th>';
                    print '</tr>';
                    print '</thead>';
                    print '<tbody>';
                    foreach ($timeslots as $timeslot) {
                        print '<tr>';
                        print '<td>' . htmlspecialchars($timeslot["startTime"]) . '</td>';
                        print '<td>' . htmlspecialchars($timeslot["endTime"]) . '</td>';
                        print '<td>' . htmlspecialchars(string: $timeslot["date"]) . '</td>';
                        print '</tr>';
                    }
                    print '</tbody>';
                    print '</table>';
                    echo '</div>';

                    echo '<h2>Schedule New Appointment</h2>';
                    echo '<form method="post" action="" onsubmit="return validateForm()">';
                    echo '<div class="mb-3">';
                    echo '<label for="timeslot" class="form-label">Timeslot</label>';
                    echo '<div class="input-group">';
                    echo '<select class="form-control" id="timeslot" name="timeslot" required>';
                    echo '<option value="Select Timeslot">Select Timeslot</option>';
                    foreach ($timeslots as $timeslot) {
                        echo '<option value="' . htmlspecialchars($timeslot["timeslotId"]) . '">' . htmlspecialchars($timeslot["date"] . ': ' . $timeslot["startTime"] . ' - ' . $timeslot["endTime"]) . '</option>';
                    }
                    echo '</select>';
                    echo '<span class="input-group-text"><i class="bi bi-caret-down-fill"></i></span>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="mb-3">';
                    echo '<label for="group" class="form-label">Group</label>';
                    echo '<div class="input-group">';
                    echo '<select class="form-control" id="group" name="group" required>';
                    echo '<option value="Select Group">Select Group</option>';
                    foreach ($groups as $group) {
                        echo '<option value="' . htmlspecialchars($group['id']) . '">' . htmlspecialchars($group['projectName']) . '</option>';
                    }
                    echo '</select>';
                    echo '<span class="input-group-text"><i class="bi bi-caret-down-fill"></i></span>';
                    echo '</div>';
                    echo '</div>';
                    echo '<button type="submit" class="btn btn-ndsu">Schedule Appointment</button>';
                    echo '<br/>';
                    echo '<br/>';
                    echo '<a href="instructor_dashboard.php" class="btn btn-ndsu">Go Back</a>';
                    echo '</form>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
    <?php include("./components/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.js"></script>
</body>

</html>