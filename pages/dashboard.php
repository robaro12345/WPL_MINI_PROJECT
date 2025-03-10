<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php include '../includes/navbar.php'; ?>
<div class="container">
<?php
if (!isset($_SESSION['user'])) {
    echo '<div class="alert alert-danger">Please <a href="?page=login">login</a> to view your dashboard.</div>';
} else {
    // ...existing code for displaying user dashboard...
}
?>
</div>
<?php include '../includes/footer.php'; ?>
