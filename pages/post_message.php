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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campaign'])) {
    if (!isset($_SESSION['user'])) {
        echo '<div class="alert alert-danger">Please <a href="?page=login">login</a> to post a message.</div>';
    } else {
        // ...existing code for posting message...
    }
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>
</div>
<?php include '../includes/footer.php'; ?>
