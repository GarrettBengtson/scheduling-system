<?php
session_start();
// Dummy data for appointments
$appointments = [
    ["groupName" => "Group A", "startTime" => "10:00 AM", "endTime" => "11:00 AM"],
    ["groupName" => "Group B", "startTime" => "11:30 AM", "endTime" => "12:30 PM"],
    ["groupName" => "Group C", "startTime" => "01:00 PM", "endTime" => "02:00 PM"]
];

// Dummy data for available timeslots
$timeslots = [
    ["startTime" => "09:00 AM", "endTime" => "10:00 AM", "date" => "2025-04-12"],
    ["startTime" => "11:00 AM", "endTime" => "12:00 PM", "date" => "2025-04-12"],
    ["startTime" => "02:00 PM", "endTime" => "03:00 PM", "date" => "2025-04-12"]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Appointment Scheduler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
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
                    '<h1 class="header">Welcome</h1>' .
                    '</div>';

                // echo "<p>Hello, " . $_SESSION['email'] . "!</p>";

                // date_default_timezone_set('America/Chicago');
                // echo '<p>You have been logged in since: ' . date('g:i a', $_SESSION['loggedin']) . '</p>';

                // echo '<p><a href="logout.php" class="btn btn-ndsu">Logout</a></p>';

                echo '<div class="card-body">'; // Start card body

                // Date selection input
                echo '<div class="m-3">';
                echo '<h2>Filter Appointments</h2>';
                echo '<input type="date" class="form-control" id="appointmentDate" name="appointmentDate">';
                echo '</div>';

                // Table to display scheduled appointments
                echo '<div class="card-section">';
                echo '<h2 class="section-header">Scheduled Appointments</h2>';
                echo '<table class="table table-success">';
                echo '<thead>';
                echo '<tr>';
                echo '<th scope="col">Group Name</th>';
                echo '<th scope="col">Start Time</th>';
                echo '<th scope="col">End Time</th>';
                echo '<th scope="col">Actions</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ($appointments as $appointment) {
                    echo '<tr>';
                    echo '<td>' . $appointment["groupName"] . '</td>';
                    echo '<td>' . $appointment["startTime"] . '</td>';
                    echo '<td>' . $appointment["endTime"] . '</td>';
                    echo '<td><button class="btn btn-danger">Cancel Appointment</button></td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';

                // Buttons beneath the scheduled appointments table
                echo '<div class="mt-3 mb-3">';
                echo '<a href="instructor_setup.php" class="btn btn-ndsu">Setup Appointment</a>';
                echo '</div>';

                // Table to display available timeslots
                echo '<div class="card-section">';
                echo '<h2 class="section-header">Available Timeslots</h2>';
                echo '<table class="table table-success">';
                echo '<thead>';
                echo '<tr>';
                echo '<th scope="col">Start Time</th>';
                echo '<th scope="col">End Time</th>';
                echo '<th scope="col">Date</th>';
                echo '<th scope="col">Actions</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ($timeslots as $timeslot) {
                    echo '<tr>';
                    echo '<td>' . $timeslot["startTime"] . '</td>';
                    echo '<td>' . $timeslot["endTime"] . '</td>';
                    echo '<td>' . $timeslot["date"] . '</td>';
                    echo '<td><button class="btn btn-danger">Delete</button></td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';

                // Button beneath the available timeslots table
                echo '<div class="mt-3">';
                echo '<a href="instructor_add_timeslot.php" class="btn btn-ndsu">Add New Timeslot</a>';
                echo '</div>';

                echo '</div>'; // End card body
                // }
                ?>
            </div>
        </div>
    </div>
    <?php include("./components/footer.php"); ?>
</body>

</html>