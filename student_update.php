<?php
session_start();
require 'connection.php';
require __DIR__ . '/queries/StudentQuery.php';

use StudentQuery\StudentQuery;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$studentID = $_SESSION['user_id'];
$query = new StudentQuery();
$statusMsg = "";

// success message
if (isset($_GET['updated']) && $_GET['updated'] == 1) {
    $statusMsg = "<div class='alert alert-success text-center'>Appointment successfully updated.</div>";
}

// get appointment ID
$appointmentID = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : null;

if (!$appointmentID) {
    die("<div class='alert alert-danger text-center'>No appointment selected for update.</div>");
}

// handle update form from submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['appointment_id']) && isset($_POST['new_slot_id']) && $_POST['action'] === 'update') {
    $oldAppointmentID = intval($_POST['appointment_id']);
    $newSlotID = intval($_POST['new_slot_id']);

    // get old appointment group ID
    $groupIDQuery = $conn->prepare("SELECT groupID FROM Appointment WHERE id = ?");
    $groupIDQuery->bind_param("i", $oldAppointmentID);
    $groupIDQuery->execute();
    $groupIDQuery->bind_result($groupID);
    $groupIDQuery->fetch();
    $groupIDQuery->close();

    // cancel old appointment
    $conn->query("UPDATE Time_Slots AS ts JOIN Appointment AS a ON ts.id = a.timeSlotID SET ts.isAvailable = 1 WHERE a.id = $oldAppointmentID;");
    $conn->query("DELETE FROM Appointment WHERE id = $oldAppointmentID;");

    // use new appointment
    $insertStmt = $conn->prepare("INSERT INTO Appointment (timeSlotID, groupID) VALUES (?, ?)");
    $insertStmt->bind_param("ii", $newSlotID, $groupID);
    $insertStmt->execute();
    $newAppointmentID = $insertStmt->insert_id;
    $insertStmt->close();

    // update new time slot availability
    $conn->query("UPDATE Time_Slots SET isAvailable = 0 WHERE id = $newSlotID;");

    // redirect to new appointment ID and show success
    header("Location: student_update.php?appointment_id=$newAppointmentID&updated=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
</head>
<body>
<?php include './components/header.php'; ?>
<div class="bg">
    <main class="container py-5">
        <div class="text-center mb-4">
            <h2 class="header">Update Your Appointment</h2>
        </div>

        <?php echo $statusMsg; ?>

        <section class="mb-5">
            <div class="card p-4 shadow">
                <h4 class="mb-4">Your Current Appointment Info</h4>
                <?php
                // get updated appointment details
                $sql = rtrim($query->getCurrentAppointments($studentID), ';');
                $sql .= " AND a.id = $appointmentID";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0):
                    $row = $result->fetch_assoc();
                    $date = date("l, F j, Y", strtotime($row['date']));
                    $start = date("g:i A", strtotime($row['startTime']));
                    $end = date("g:i A", strtotime($row['endTime']));
                    ?>
                <div class="mb-4 border rounded p-3">
                    <h5>Group ID: <?php echo $row['groupID']; ?> (<?php echo htmlspecialchars($row['projectName']); ?>)</h5>
                    <p><strong>Currently Scheduled:</strong> <?php echo "$date from $start to $end"; ?></p>
                    <p><strong>Instructor:</strong> <?php echo htmlspecialchars($row['instructorEmail']); ?></p>

                    <form method="POST" class="mt-4">
                        <input type="hidden" name="appointment_id" value="<?php echo $appointmentID; ?>">
                        <input type="hidden" name="action" value="update">
                        <label for="new_slot_id" class="form-label">Select New Time Slot:</label>
                        <select name="new_slot_id" class="form-select mb-3" required>
                            <option value="">-- Select a New Time Slot --</option>
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
                        <button type="submit" class="btn btn-ndsu px-4 py-2">Update Appointment</button>
                    </form>
                </div>
                <?php else: ?>
                    <p class="text-warning text-center">Unable to load appointment details. It may have been canceled.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="d-flex justify-content-center gap-4">
            <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </section>
    </main>
</div>
<?php include './components/footer.php'; ?>
</body>
</html>
