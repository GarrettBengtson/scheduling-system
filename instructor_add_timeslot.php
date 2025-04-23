<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . '/queries/InstructorQuery.php';

use InstructorQuery\InstructorQuery;

$queries = new InstructorQuery($conn);
session_start();

$timeslots =  $queries->getAllTimeSlotsOrderedByDate($_SESSION["user_id"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        echo "ADDING";
        $startTime = htmlspecialchars($_POST["startTime"]);
        $endTime = htmlspecialchars($_POST["endTime"]);
        $date = htmlspecialchars($_POST["date"]);

        if (empty($startTime) || empty($endTime) || empty($date)) {
            echo "Please fill in all fields and select a valid group.";
        } else {
            $result = $queries->createTimeSlot($_SESSION["user_id"], $startTime, $endTime, $date);
            $timeslots =  $queries->getAllTimeSlotsOrderedByDate($_SESSION["user_id"]);
            if ($result) {
                //redirection to the home page
                if (ob_get_contents()) ob_end_clean();
                session_write_close();
                header('Location: instructor_dashboard.php');
                exit();
            } else {
                echo "An error occured when creating timeslot";
            }
        }
    }
    if (isset($_POST['deleteTimeslotId'])) {
        echo "DELETING";
        $result = $queries->deleteTimeSlot($_POST['deleteTimeslotId']);
        echo $result;
        $timeslots =  $queries->getAllTimeSlotsOrderedByDate($_SESSION["user_id"]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Add Timeslot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <script>
        function validateForm() {
            const startTime = document.getElementById("startTime").value;
            const endTime = document.getElementById("endTime").value;
            const date = document.getElementById("date").value;


            // Function to convert 24-hour time to 12-hour time with AM/PM
            function convertTo12Hour(time) {
                const [hours, minutes] = time.split(':');
                let period = 'AM';
                let hour = parseInt(hours);

                if (hour >= 12) {
                    period = 'PM';
                    if (hour > 12) {
                        hour -= 12;
                    }
                } else if (hour === 0) {
                    hour = 12;
                }
                const formattedHour = hour < 10 ? `0${hour}` : hour;
                return `${formattedHour}:${minutes} ${period}`;
            }

            const startTime12Hour = convertTo12Hour(startTime);
            const endTime12Hour = convertTo12Hour(endTime);


            const timeslots = [];
            const table = document.querySelector("tbody");
            const rows = table.querySelectorAll("tr");

            rows.forEach(row => {
                const cells = row.querySelectorAll("td");
                const timeslot = {
                    startTime: cells[0].textContent.trim(),
                    endTime: cells[1].textContent.trim(),
                    date: cells[2].textContent.trim()
                };
                timeslots.push(timeslot);
            });
            if (!startTime || !endTime || !date) {
                alert("Please fill in all fields and select a valid group.");
                return false;
            }

            let matchFound = false;
            for (const timeslot of timeslots) {
                if (timeslot.startTime === startTime12Hour && timeslot.endTime === endTime12Hour && timeslot.date === date) {
                    matchFound = true;
                    break;
                }
            }

            if (matchFound) {
                alert("Timeslot Already Made!");
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
                        '<h1 class="header">Add Timeslot</h1>' .
                        '</div>';
                    echo '<div class="card-body">';
                    // Table to display available timeslots
                    echo '<div class="card-section">';
                    print '<h2 class="section-header">Already Made Timeslots</h2>';
                    if (is_array($timeslots) && count($timeslots) > 0 && isset($timeslots)) {
                        print '<table class="table table-success">';
                        print '<thead>';
                        print '<tr>';
                        print '<th scope="col">Start Time</th>';
                        print '<th scope="col">End Time</th>';
                        print '<th scope="col">Date</th>';
                        echo '<th scope="col">Actions</th>';
                        print '</tr>';
                        print '</thead>';
                        print '<tbody>';
                        foreach ($timeslots as $timeslot) {
                            print '<tr>';
                            print '<td>' . htmlspecialchars($timeslot["startTime"]) . '</td>';
                            print '<td>' . htmlspecialchars($timeslot["endTime"]) . '</td>';
                            print '<td>' . htmlspecialchars($timeslot["date"]) . '</td>';
                            print '<td><form method="POST" action="">';
                            print '<input type="hidden" name="deleteTimeslotId" value="' . $timeslot['timeslotId'] . '">';
                            print '<button type="submit" class="btn btn-danger">Delete Timeslot</button>';
                            print '</form></td>';
                            print '</tr>';
                        }
                        print '</tbody>';
                        print '</table>';
                    } else {
                        echo '<div class="mx-auto mb-4 no-results-cont text-center"><p class="no-results">There are no timeslots created</p></div>';
                    }
                    echo '</div>';

                    echo '<h2>Make New Timeslot</h2>';
                    echo '<form method="post" action="" onsubmit="return validateForm()">';
                    echo '<input type="hidden" name="add" id="add value="add">';
                    echo '<div class="mb-3">';
                    echo '<label for="startTime" class="form-label">Start Time</label>';
                    echo '<input type="time" class="form-control" id="startTime" name="startTime" required>';
                    echo '</div>';
                    echo '<div class="mb-3">';
                    echo '<label for="endTime" class="form-label">End Time</label>';
                    echo '<input type="time" class="form-control" id="endTime" name="endTime" required>';
                    echo '</div>';
                    echo '<div class="mb-3">';
                    echo '<label for="date" class="form-label">Date</label>';
                    echo '<input type="date" class="form-control" id="date" name="date" required>';
                    echo '</div>';
                    echo '<button type="submit" class="btn btn-ndsu">Add Timeslot</button>';
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
