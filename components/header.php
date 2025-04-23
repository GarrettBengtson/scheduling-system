<?php
//check if session exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg ndsu-green">
    <div class="container-fluid">
        <!-- Logo -->
        <a href="https://www.ndsu.edu" class="navbar-brand d-flex align-items-center">
            <img src="https://www.ndsu.edu/themes/ndsu/logo.svg"
                alt="NDSU Logo"
                style="height: 50px;"
                class="me-2">
        </a>
        <!-- Navbar Toggler -->
        <button class="navbar-toggler btn-ndsu" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if ($_SESSION['user_role'] == 'Student'){
                    <li class="nav-item">
                        echo '<div class="name" >Student/' . $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . '</div>';
                    </li>
                }
                <?php endif; ?>

                <?php if ($_SESSION['user_role'] == 'Instructor'){
                    <li class="nav-item">
                        echo '<div class="name" >Instructor/' . $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . '</div>';
                    </li>
                }
                <?php endif; ?>
                        
                <!-- If user is logged in, display logout button -->
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-ndsu" href="logout.php">Logout</a>
                    </li>
                <!-- Otherwise, display logout button-->
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-ndsu" href="login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
