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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $goal_amount = $_POST['goal_amount'];
    $user_id = $_SESSION['user']['UserID'];

    $conn->query("SET foreign_key_checks = 0");
    $stmt = $conn->prepare("INSERT INTO CAMPAIGN (title, description, goal_amount, user_id, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssdi", $title, $description, $goal_amount, $user_id);

    if ($stmt->execute()) {
        echo '<div class="alert alert-success">Campaign created successfully! Redirecting...</div>';
        echo '<script>setTimeout(function(){ window.location = "dashboard.php"; }, 2000);</script>';
    } else {
        echo '<div class="alert alert-danger">Error creating campaign: ' . $stmt->error . '</div>';
    }

    $stmt->close();
    $conn->query("SET foreign_key_checks = 1");
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}

$conn->close();
?>
