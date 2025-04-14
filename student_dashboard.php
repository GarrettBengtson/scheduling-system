<?php
session_start();
require 'connection.php';
require 'StudentQuery.php';

if (!isset($_SESSION["student_id"])) {
    header("Location: studentlogin.php");
    exit();
}

$studentID = $_SESSION['student_id'];
$query = new StudentQuery();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
</head>
<body>
<?php include('./components/header.php'); ?>
<div class="bg">
    <main class="container py-5">
        <div class="text-center mb-4">
            <h2 class="header">Welcome to your Dashboard, <?php echo $_SESSION["student_name"]; ?>!</h2>

        </div>

<section class="mb-5">
    <div class="card p-4 shadow">
        <h4 class="mb-4">Your Group(s) and Appointment Info</h4>

        <?php
        $result = $conn->query($query->getCurrentAppointments($studentID));


        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
                $date = date("l, F j, Y", strtotime($row['date']));
                $start = date("g:i A", strtotime($row['startTime']));
                $end = date("g:i A", strtotime($row['endTime']));
                ?>
            <div class="mb-4 border rounded p-3">
                <h5 class="mb-2">Group ID: <?php echo $row['groupID']; ?> (<?php echo $row['projectName']; ?>)</h5>
                <p class="mb-1"><strong>Scheduled:</strong> <?php echo "$date from $start to $end"; ?></p>
                <p><strong>Instructor:</strong> <?php echo htmlspecialchars($row['instructorEmail']); ?></p>

                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');" class="mb-2">
                            <input type="hidden" name="appointment_id" value="<?php echo $row['appointmentID']; ?>">
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                        </form>
            </div>
        <?php
            endwhile;
        else:
            echo "<p class='text-warning'>You are not assigned to any group or have no scheduled appointments.</p>";
        endif;
        ?>
    </div>
</section>

        <section class="d-flex flex-column flex-md-row justify-content-center gap-4">
            <a href="schedule_appointment.php" class="btn btn-ndsu px-4 py-2">Schedule New Appointment</a>
            <a href="update.php" class="btn btn-ndsu px-4 py-2">Update Appointment</a>
        </section>
    </main>
</div>
<?php include('./components/footer.php'); ?>
</body>
</html>