<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
    // ...existing code for submitting campaign...
} else {
    echo '<div class="alert alert-danger">Unauthorized access.</div>';
}
?>
