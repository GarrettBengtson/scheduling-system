<?php
session_start();
?>

<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <title></title>
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
                    print '<div class="card-header ndsu-green">' .
                        '<h1 class="header">Not Logged In</h1>' .
                        '</div>';
                    print "<p>Please return home.</p>";
                    print '<p><a href="logout.php" class="btn btn-ndsu">Go Home</a></p>';
                } else {
                    print '<div class="card-header ndsu-green">' .
                        '<h1 class="header">Welcome</h1>' .
                        '</div>';

                    print "<p>Hello, " . $_SESSION['email'] . "!</p>";

                    date_default_timezone_set('America/Chicago');
                    print '<p>You have been logged in since: ' . date('g:i a', $_SESSION['loggedin']) . '</p>';

                    print '<p><a href="logout.php" class="btn btn-ndsu">Logout</a></p>';
                }
                ?>

            </div>
        </div>
    </div>
    <?php include("./components/footer.php"); ?>
</body>

</html>