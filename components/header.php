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
        <?php
        echo '<div class="collapse navbar-collapse" id="navbarNav">';
        echo '<ul class="navbar-nav ms-auto">';
        if (isset($_SESSION['student_name'])) {
            echo '<li class="nav-item">';
            echo '<div class="name" >Student/' . $_SESSION['student_name'] . '</div>';
            echo '</li>';
        }
        if (isset($_SESSION['instructor_name'])) {
            echo '<li class="nav-item">';
            echo '<div class="name" >Instuctor/' . $_SESSION['instructor_name'] . '</div>';
            echo '</li>';
        }
        echo '<li class="nav-item">';
        if (isset($_SESSION["loggedin"])) {
            echo '<a class="nav-link btn btn-ndsu" href="logout.php">Logout</a>';
        } else {
            echo '<a class="nav-link btn btn-ndsu" href="index.php">Login</a>';
        }
        echo '</li>';
        echo '</ul>';
        echo '</div>';
        ?>

    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>