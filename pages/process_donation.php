<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campaign'])) {
    // ...existing code for processing donation...
} else {
    echo '<div class="alert alert-danger">Invalid donation request.</div>';
}
?>
