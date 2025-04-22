<?php
session_start();
require 'connection.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $errors = [];

    // Validate password (example: at least 8 characters)
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    //form handling
    if (empty($errors)) {
        //Do some database call to get the email and password
        $stmt = $conn->prepare("SELECT id, firstName, password, role FROM User WHERE username = ?"); //prepares sql query to select id,name,password from users table
        $stmt->bind_param("s", $username); //binds username to sql query
        $stmt->execute(); //runs query
        $stmt->store_result(); //stores result for further use
        if ($stmt->num_rows > 0) { //if true, matching user found
            $stmt->bind_result($id, $firstName, $hashed_password, $role); //assigns database values id,firstName,password,role to php variables
            $stmt->fetch(); //gets result from database
            if ($password === $hashed_password) { //compares user entered password to database password
                //adding session data
                session_start();
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['user_role'] = $role;
                $_SESSION['loggedin'] = time();
                $_SESSION["user_id"] = $id; //id stored in session
                $_SESSION["first_name"] = $firstName; //name stored in session
                //redirection to the welcome page
                if (ob_get_contents())
                    ob_end_clean();
                session_write_close();
<<<<<<< Updated upstream:instructor_login.php
                header('Location: instuctor_dashboard.php');
=======

                if($role == 'Instructor'){
                    header('Location: instructor_main.php');
                }
                else{
                    header('Location: studentdashboard.php');
                }

>>>>>>> Stashed changes:login.php
                exit();
            } else {
                $error = "Invalid password."; //wrong password message
            }
        } else {
            $error = "User not found."; //wrong user message
        }
        $stmt->close(); //closes statement
        $conn->close(); //closes database connection
    }
}
?>

<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
</head>

<body>
    <?php include("./components/header.php"); ?>
    <div class="bg">
        <div class="container center-content">
            <div class="card text-center">
                <div class="card-header ndsu-green">
                    <h1 class="header">Login</h1>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                        </div>
                        <button type="submit" class="btn btn-ndsu">Login</button>
                    </form>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include("./components/footer.php"); ?>
</body>

</html>