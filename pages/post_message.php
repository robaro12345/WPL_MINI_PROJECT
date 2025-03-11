<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "givewell221");

if ($conn->connect_error) {
    die('<div class="alert alert-danger text-center">Database connection failed: ' . $conn->connect_error . '</div>');
}
?>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campaign'])) {
        if (!isset($_SESSION['user'])) {
            echo '<div class="alert alert-warning text-center">
                    Please <a href="login.php" class="alert-link">login</a> to post a message.
                 </div>';
            echo '<script>setTimeout(() => { window.location.href = "login.php"; }, 3000);</script>'; // Redirect after 3 seconds
        } else {
            // Your existing code for posting a message (insert into database)
        }
    } else {
        echo '<div class="alert alert-danger text-center">
                Invalid request. <a href="home.php" class="alert-link">Go back</a>.
              </div>';
    }
    ?>
</div>

<?php include 'footer.php'; ?>

<style>
    .container {
        max-width: 600px;
    }
    .alert {
        margin-top: 20px;
    }
</style>
