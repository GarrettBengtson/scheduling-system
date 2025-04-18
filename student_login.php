<?php
session_start();
require 'connection.php'; // File to connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") { //script only runs on POST
    $username = $_POST["username"]; //Retrieves input from login form
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password FROM User WHERE username = ? AND role = 'Student'"); //prepares sql query to select id,name,password from users table
    $stmt->bind_param("s", $username); //binds username to sql query
    $stmt->execute(); //runs equery
    $stmt->store_result(); //stores result for further use

    if ($stmt->num_rows > 0) { //if true, matching user found
        $stmt->bind_result($id, $name, $hashed_password); //assigns database values id,name,password to php variables
        $stmt->fetch(); //gets result from database

        if ($password === $hashed_password) { //compares user entered password to database password
            $_SESSION["student_id"] = $id; //id stored in session
            $_SESSION["student_name"] = $name; //name stored in session
            header("Location: studentdashboard.php"); //redirects to studentdashboard
            exit(); //script stops running after redirect
        } else {
            $error = "Invalid password."; //wrong password message
        }
    } else {
        $error = "User not found."; //wrong user message
    }
    $stmt->close(); //closes statement
    $conn->close(); //closes database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
        }
    </style>
</head>
<body>
    <main>
        <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
            <div class="col-md-5">
                <div class="card shadow p-4">
                    <h2 class="text-center mb-4">Student Login</h2>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username:</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password:</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
