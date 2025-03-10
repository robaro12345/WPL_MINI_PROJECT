<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php include '../includes/navbar.php'; ?>
<div class="container">
<h1>Welcome to Give Well</h1>
<p>Your community donation platform. Connect with causes, donate, and make a difference.</p>
</div>
<?php include '../includes/footer.php'; ?>
