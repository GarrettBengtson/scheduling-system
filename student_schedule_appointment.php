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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
</head>
<body>
<?php include './components/header.php'; ?>
<div class="bg">
    <main class="container py-5">
        <div class="text-center mb-4">
            <h2 class="header">Schedule Appointment</h2>
        </div>

        <section class="mb-5">
            <div class="card p-4 shadow">
                <h4 class="mb-4">Available Time Slots</h4>

                <?php
                // fetch all group IDs for individual student user
                $groupStmt = $conn->prepare("
                SELECT g.id AS groupID, g.projectName
                FROM Group_Association ga
                INNER JOIN `Group` g ON ga.groupID = g.id
                WHERE ga.userID = ?
                ");
                $groupStmt->bind_param("i", $studentID);
                $groupStmt->execute();
                $groupResult = $groupStmt->get_result();

                $groupOptions = "";
                while ($group = $groupResult->fetch_assoc()) {
                    $gid = $group['groupID'];
                    $gname = htmlspecialchars($group['projectName']); // prevent XSS
                    $groupOptions .= "<option value='$gid'>$gname (ID: $gid)</option>";
                }
                $groupStmt->close();
                ?>

                <?php if (!empty($groupOptions)): ?>
                    <div class="row g-3">
                        <?php
                        $result = $conn->query($query->getAvailableTimeSlots());

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id = $row['timeSlotID'];
                                $date = date("l, M j", strtotime($row['date']));
                                $time = date("g:i A", strtotime($row['startTime'])) . " - " . date("g:i A", strtotime($row['endTime']));
                                echo "<div class='col-md-6 col-lg-4'>";
                                echo "<div class='card p-3 shadow-sm text-center'>";
                                echo "<h5 class='mb-2'>$date</h5>";
                                echo "<p class='mb-3'>$time</p>";
                                echo "<form method='POST' action='student_book_appointment.php'>";
                                echo "<input type='hidden' name='slot_id' value='$id'>";
                                echo "<div class='mb-2'>";
                                echo "<select name='group_id' class='form-select' required>";
                                echo "<option value=''>Select Group</option>";
                                echo $groupOptions;
                                echo "</select>";
                                echo "</div>";
                                echo "<button type='submit' class='btn btn-ndsu w-100'>Book Slot</button>";
                                echo "</form>";
                                echo "</div></div>";
                            }
                        } else {
                            echo "<p class='text-muted'>No available time slots at this time.</p>";
                        }
                        ?>
                    </div>
                <?php else: ?>
                    <p class='text-warning'>You are not assigned to any group.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- back to dashboard -->
        <section class="d-flex flex-column flex-md-row justify-content-center gap-4">
            <a href="student_dashboard.php" class="btn btn-ndsu px-4 py-2">Back to Dashboard</a>
        </section>
    </main>
</div>
<?php include './components/footer.php'; ?>
</body>
</html>
