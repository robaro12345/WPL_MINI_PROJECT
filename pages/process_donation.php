<?php
session_start();
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
                    You must <a href="login.php" class="alert-link">login</a> to make a donation.
                 </div>';
            echo '<script>setTimeout(() => { window.location.href = "login.php"; }, 3000);</script>'; // Redirect after 3 sec
        } else {
            // ✅ Your donation processing logic here...
            echo '<div class="alert alert-success text-center">Donation processed successfully! 🎉</div>';
        }
    } else {
        echo '<div class="alert alert-danger text-center">
                Invalid donation request. <a href="donate.php" class="alert-link">Go to Donations</a>.
              </div>';
        echo '<script>setTimeout(() => { window.location.href = "donate.php"; }, 3000);</script>'; // Redirect after 3 sec
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
