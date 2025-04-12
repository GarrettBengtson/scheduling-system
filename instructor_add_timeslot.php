<?php
session_start();

// Dummy data for available timeslots
$timeslots = [
    ["startTime" => "09:00 AM", "endTime" => "10:00 AM", "date" => "2025-04-12"],
    ["startTime" => "11:00 AM", "endTime" => "12:00 PM", "date" => "2025-04-12"],
    ["startTime" => "02:00 PM", "endTime" => "03:00 PM", "date" => "2025-04-12"]
];

// Dummy data for groups
$groups = ["Group A", "Group B", "Group C"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startTime = htmlspecialchars($_POST["startTime"]);
    $endTime = htmlspecialchars($_POST["endTime"]);
    $date = htmlspecialchars($_POST["date"]);

    if (empty($startTime) || empty($endTime) || empty($date)) {
        echo "Please fill in all fields and select a valid group.";
    } else {
        //CAll database
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
                // if (!isset($_SESSION["loggedin"])) {
                //     echo '<div class="card-header ndsu-green">' .
                //         '<h1 class="header">Not Logged In</h1>' .
                //         '</div>';
                //     echo "<p>Please return home.</p>";
                //     echo '<p><a href="logout.php" class="btn btn-ndsu">Go Home</a></p>';
                // } else {
                echo '<div class="card-header ndsu-green">' .
                    '<h1 class="header">Add Timeslot</h1>' .
                    '</div>';
                echo '<div class="card-body">';
                // Table to display available timeslots
                echo '<div class="card-section">';
                print '<h2 class="section-header">Taken Timeslots</h2>';
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
                    print '<td>' . htmlspecialchars($timeslot["date"]) . '</td>';
                    print '</tr>';
                }
                print '</tbody>';
                print '</table>';
                echo '</div>';

                echo '<h2>Make New Timeslot</h2>';
                echo '<form method="post" action="instructor_dashboard.php" onsubmit="return validateForm()">';
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
                echo '</form>';
                echo '</div>';
                // }
                ?>
            </div>
        </div>
    </div>
    <?php include("./components/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.js"></script>
</body>

</html>