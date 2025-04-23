<?php


// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require __DIR__ . '/queries/InstructorQuery.php';

use InstructorQuery\InstructorQuery;

$queries = new InstructorQuery($conn);

session_start();

$groupMembers =  $queries->getGroupInfo($_SESSION["groupId"]);

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
                        '<h1 class="header">View Group</h1>' .
                        '</div>';
                    echo '<div class="card-body">'; // Start card body
                    // Table to display scheduled appointments
                    echo '<div class="card-section">';
                    echo '<h2 class="section-header">Group Information</h2>';
                    if (is_array($groupMembers) && count($groupMembers) > 0 && isset($groupMembers)) {
                        echo '<table class="table table-success table-bottom">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th scope="col">Group Name</th>';
                        echo '<th scope="col">Username</th>';
                        echo '<th scope="col">Email</th>';
                        echo '<th scope="col">Is Leader</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        foreach ($groupMembers as $member) {
                            echo '<tr>';
                            echo '<td>' . $member["projectName"] . '</td>';
                            echo '<td>' . $member["username"] . '</td>';
                            echo '<td>' . $member["email"] . '</td>';
                            echo '<td>' . $member["isGroupLeader"] . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<div class="mx-auto mb-4 no-results-cont text-center"><p class="no-results">There are no group members in this group</p></div>';
                    }
                    echo '</div>';


                    // Buttons beneath the scheduled appointments table
                    echo '<div class="mt-3 mb-3">';
                    echo '<a href="instructor_dashboard.php" class="btn btn-ndsu">Go Back</a>';
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
