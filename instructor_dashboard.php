<?php


// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require __DIR__ . '/queries/InstructorQuery.php';

use InstructorQuery\InstructorQuery;

$queries = new InstructorQuery($conn);

session_start();

$SELECTED_DATE = date('Y-m-d');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['appointmentDate'])) {
        $SELECTED_DATE = $_POST['appointmentDate'];
    }
    if (isset($_POST['seeAll'])) {
        echo "See All";
        $SELECTED_DATE = "All Future";
        $appointments =  $queries->getAllFutureScheduledAppointments($_SESSION["instructor_id"]);

        $timeslots =  $queries->getAllFutureAvailableTimeSlots($_SESSION["instructor_id"]);
    } else if (isset($_POST['filter'])) {
        echo "Filter";
        $appointments =  $queries->getScheduledAppointmentsByDate($_SESSION["instructor_id"], $SELECTED_DATE);

        $timeslots =  $queries->getAvailableTimeSlotsByDate($_SESSION["instructor_id"], $SELECTED_DATE);
    } else if (isset($_POST['cancelAppointmentId'])) {
        echo "Canceling Appointment";
        $SELECTED_DATE = $_POST['selectedDate'];
        $result = $queries->cancelGroupAppointment($_POST['cancelAppointmentId']);
        echo $result;
        if ($SELECTED_DATE == "All Future") {
            echo "All Future";
            $appointments =  $queries->getAllFutureScheduledAppointments($_SESSION["instructor_id"]);

            $timeslots =  $queries->getAllFutureAvailableTimeSlots($_SESSION["instructor_id"]);
        } else {
            echo "Specific Date";
            $appointments =  $queries->getScheduledAppointmentsByDate($_SESSION["instructor_id"], $SELECTED_DATE);

            $timeslots =  $queries->getAvailableTimeSlotsByDate($_SESSION["instructor_id"], $SELECTED_DATE);
        }
    } else if (isset($_POST['deleteTimeslotId'])) {
        echo "Deleting Timeslot";
        $SELECTED_DATE = $_POST['selectedDate'];
        $result = $queries->deleteTimeSlot($_POST['deleteTimeslotId']);
        echo $result;
        if ($SELECTED_DATE == "All Future") {
            echo "All Future";
            $appointments =  $queries->getAllFutureScheduledAppointments($_SESSION["instructor_id"]);

            $timeslots =  $queries->getAllFutureAvailableTimeSlots($_SESSION["instructor_id"]);
        } else {
            echo "Specific Date";
            $appointments =  $queries->getScheduledAppointmentsByDate($_SESSION["instructor_id"], $SELECTED_DATE);

            $timeslots =  $queries->getAvailableTimeSlotsByDate($_SESSION["instructor_id"], $SELECTED_DATE);
        }
    } else if (isset($_POST['groupId'])) {
        echo "Viewing group";
        $_SESSION['groupId'] = $_POST['groupId'];
        if (ob_get_contents()) ob_end_clean();
        session_write_close();
        header('Location: instructor_view.php');
        exit();
    }
}
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
                if (!isset($_SESSION["loggedin"])) {
                    echo '<div class="card-header ndsu-green">' .
                        '<h1 class="header">Not Logged In</h1>' .
                        '</div>';
                    echo "<p>Please return home.</p>";
                    echo '<p><a href="logout.php" class="btn btn-ndsu">Go Home</a></p>';
                } else {
                    echo '<div class="card-header ndsu-green">' .
                        '<h1 class="header">Welcome</h1>' .
                        '</div>';

                    echo '<div class="card-body">'; // Start card body
                    // Display the Filter
                    echo '<div class="m-3">';
                    echo '<h2>Filter Appointments</h2>';
                    echo '<form method="POST" action="">';
                    echo '<input type="hidden" name="filter" id="filter" value="filter">';
                    echo '<input type="date" class="form-control" id="appointmentDate" name="appointmentDate" value="' . $SELECTED_DATE . '">';
                    echo '<input type="submit" class="btn btn-ndsu mt-2" value="Filter">';
                    echo '</form>';
                    //Form for seeing all appointments
                    echo '<form method="POST" action="">';
                    echo '<input type="hidden" name="seeAll" id="seeAll" value="seeAll">';
                    echo '<input type="submit" class="btn btn-ndsu mt-2" value="See All Future">';
                    echo '</form>';
                    echo '</div>';
                    // Display the selected date
                    echo '<p>Selected Date: ' . $SELECTED_DATE . '</p>';


                    // Table to display scheduled appointments
                    echo '<div class="card-section">';
                    echo '<h2 class="section-header">Scheduled Appointments</h2>';
                    if (is_array($appointments) && count($appointments) > 0 && isset($appointments)) {
                        echo '<table class="table table-success table-bottom">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th scope="col">Group Name</th>';
                        echo '<th scope="col">Start Time</th>';
                        echo '<th scope="col">End Time</th>';
                        echo '<th scope="col">Date</th>';
                        echo '<th scope="col">Actions</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        foreach ($appointments as $appointment) {
                            echo '<tr>';
                            echo '<td>' . $appointment["projectName"] . '</td>';
                            echo '<td>' . $appointment["startTime"] . '</td>';
                            echo '<td>' . $appointment["endTime"] . '</td>';
                            echo '<td>' . htmlspecialchars($appointment["date"]) . '</td>';
                            echo '<td><form method="POST" action="">';
                            echo '<input type="hidden" name="cancelAppointmentId" value="' . $appointment['appointmentID'] . '">';
                            echo '<input type="hidden" name="selectedDate" value="' . $SELECTED_DATE . '">';
                            echo '<button type="submit" class="btn btn-danger">Cancel Appointment</button>';
                            echo '</form>';
                            echo '<form method="POST" action="">';
                            echo '<input type="hidden" name="groupId" value="' . $appointment['groupID'] . '">';
                            echo '<button type="submit" class="btn btn-primary">See Group Info</button>';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<div class="mx-auto mb-4 no-results-cont text-center"><p class="no-results">There are no scheduled appointments on this day</p></div>';
                    }
                    echo '</div>';


                    // Buttons beneath the scheduled appointments table
                    echo '<div class="mt-3 mb-3">';
                    echo '<a href="instructor_setup.php" class="btn btn-ndsu">Setup Appointment</a>';
                    echo '</div>';

                    // Table to display available timeslots
                    echo '<div class="card-section">';
                    echo '<h2 class="section-header">Future Available Timeslots</h2>';
                    if (is_array($timeslots) && count($timeslots) > 0 && isset($timeslots)) {

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
                            echo '<td><form method="POST" action="">';
                            echo '<input type="hidden" name="deleteTimeslotId" value="' . $timeslot['timeslotId'] . '">';
                            echo '<input type="hidden" name="selectedDate" value="' . $SELECTED_DATE . '">';
                            echo '<button type="submit" class="btn btn-danger">Delete Timeslot</button>';
                            echo '</form></td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<div class="mx-auto mb-4 no-results-cont text-center"><p class="no-results">There are no available timeslots on this day</p></div>';
                    }
                    echo '</div>';

                    // Button beneath the available timeslots table
                    echo '<div class="mt-3">';
                    echo '<a href="instructor_add_timeslot.php" class="btn btn-ndsu">Add New Timeslot</a>';
                    echo '</div>';

                    echo '</div>'; // End card body
                }
                ?>
            </div>
        </div>
    </div>
    <?php include("./components/footer.php"); ?>
</body>

</html>