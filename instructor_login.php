<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $errors = [];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Validate password (example: at least 8 characters)
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    //form handling
    if (empty($errors)) {
        //Do some database call to get the email and password
        if ((strtolower($_POST['email']) == 'ndsu@ndsu.edu') && ($_POST['password'] == 'testpass')) {
            //correct credentials

            //adding session data
            session_start();
            $_SESSION['email'] = $_POST['email'];
            $_SESSION['loggedin'] = time();

            //redirection to the welcome page
            if (ob_get_contents()) ob_end_clean();
            session_write_close();
            header('Location: main.php');
            exit();
        } else {
            //incorrect credentials
            print '<p class="text--error">The submitted email address and password do not match those on file!<br>Go back and try again.</p>';
        }
    } else {
        //field forgotten
        print '<p class="text--error">Please make sure you enter both an email address and a password!<br>Go back and try again.</p>';
    }
}
?>

<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <title></title>
</head>

<body>
    <?php include("./components/header.php"); ?>
    <div class="bg">
        <div class="container center-content">
            <div class="card text-center">
                <div class="card-header ndsu-green">
                    <h1 class="header">Instructor Login</h1>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                        </div>
                        <button type="submit" class="btn btn-ndsu">Login</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</body>

</html>