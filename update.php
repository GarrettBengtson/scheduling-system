<?php
session_start();
require 'connection.php';
require 'StudentQuery.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: studentlogin.php");
    exit();
}

$studentID = $_SESSION['student_id'];
$query = new StudentQuery();
$statusMsg = "";

// Handle cancelation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['appointment_id']) && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $appointmentID = $_POST['appointment_id'];
    $statements = $query->cancelAppointment($appointmentID);

    if (is_array($statements)) {
        foreach ($statements as $sql) {
            if (!empty($sql) && !$conn->query($sql)) {
                $statusMsg = "<div class='alert alert-danger text-center'>Error executing query: $sql<br>" . $conn->error . "</div>";
                break;
            }
        }
        if (!$statusMsg) {
            $statusMsg = "<div class='alert alert-success text-center'>Appointment canceled successfully.</div>";
        }
    } else {
        $statusMsg = "<div class='alert alert-danger text-center'>Could not process appointment cancellation.</div>";
    }
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['appointment_id']) && isset($_POST['new_slot_id']) && $_POST['action'] === 'update') {
    $appointmentID = $_POST['appointment_id'];
    $newSlotID = $_POST['new_slot_id'];

    // First cancel the old appointment
    $cancelStmts = $query->cancelAppointment($appointmentID);
    // Then insert the new one (assumes same groupID reuse)
    $groupIDQuery = $conn->prepare("SELECT groupID FROM Appointment WHERE id = ?");
    $groupIDQuery->bind_param("i", $appointmentID);
    $groupIDQuery->execute();
    $groupIDQuery->bind_result($groupID);
    $groupIDQuery->fetch();
    $groupIDQuery->close();

    $rescheduleStmts = $query->scheduleAppointment($newSlotID, $groupID);
    $statements = array_merge($cancelStmts, $rescheduleStmts);

    foreach ($statements as $sql) {
        if (!empty($sql) && !$conn->query($sql)) {
            $statusMsg = "<div class='alert alert-danger text-center'>Error during update: $sql<br>" . $conn->error . "</div>";
            break;
        }
    }
    if (!$statusMsg) {
        $statusMsg = "<div class='alert alert-success text-center'>Appointment successfully updated.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
</head>
<body>
    <?php include './components/header.php'; ?>
    <div class="bg">
        <main class="container py-5">
            <div class="text-center mb-4">
                <h2 class="header">Manage Your Appointments</h2>
                <p class="ndsu-yellow">Welcome, <?php echo $_SESSION["student_name"]; ?>!</p>
            </div>

            <?php echo $statusMsg; ?>

            <section class="mb-5">
                <div class="card p-4 shadow">
                    <h4 class="mb-4">Your Scheduled Appointments</h4>

                    <?php
                    $result = $conn->query($query->getCurrentAppointments($studentID));

                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $date = date("l, F j, Y", strtotime($row['date']));
                            $start = date("g:i A", strtotime($row['startTime']));
                            $end = date("g:i A", strtotime($row['endTime']));
                            ?>
                    <div class="mb-4 border rounded p-3">
                        <h5>Group ID: <?php echo $row['groupID']; ?> (<?php echo $row['projectName']; ?>)</h5>
                        <p><strong>Scheduled:</strong> <?php echo "$date from $start to $end"; ?></p>
                        <p><strong>Instructor:</strong> <?php echo htmlspecialchars($row['instructorEmail']); ?></p>

                        <form method="POST" class="d-flex flex-column flex-md-row gap-2 align-items-center">
                            <input type="hidden" name="appointment_id" value="<?php echo $row['appointmentID']; ?>">
                            <input type="hidden" name="action" value="update">
                            <select name="new_slot_id" class="form-select" required>
                                <option value="">Select New Time Slot</option>
                                <?php
                                $availableSlots = $conn->query($query->getAvailableTimeSlots());
                                while ($slot = $availableSlots->fetch_assoc()):
                                    $slotDate = date("M j", strtotime($slot['date']));
                                    $slotTime = date("g:i A", strtotime($slot['startTime'])) . " - " . date("g:i A", strtotime($slot['endTime']));
                                    ?>
                                    <option value="<?php echo $slot['timeSlotID']; ?>">
                                        <?php echo "$slotDate ($slotTime)"; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                    <?php endwhile;
                    else:
                        echo "<p class='text-muted'>You don't have any upcoming appointments.</p>";
                    endif;
                    ?>
                </div>
            </section>

            <!-- Navigation Buttons -->
        <section class="d-flex flex-column flex-md-row justify-content-center gap-4">
            <a href="schedule_appointment.php" class="btn btn-ndsu px-4 py-2">Schedule an Appointment(s)!</a>
            <a href="studentdashboard.php" class="btn btn-ndsu px-4 py-2">Back to Dashboard</a>
        </section>
        </main>
    </div>
    <?php include './components/footer.php'; ?>
</body>
</html>
