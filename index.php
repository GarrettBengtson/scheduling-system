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
                <div class="card-header ndsu-green">
                    <h1 class="header">Scheduling Assistant</h1>
                </div>
                <div class="card-body">
                    <div class="login-buttons">
                        <a href="login.php" class="btn btn-ndsu">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include("./components/footer.php"); ?>
</body>

</html>