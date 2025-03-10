<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php include '../includes/navbar.php'; ?>
<div class="container">
<h1>About Give Well</h1>
<p>Our mission is to bridge communities and support campaigns through transparent donations and engagement.</p>
</div>
<?php include '../includes/footer.php'; ?>
