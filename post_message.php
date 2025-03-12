<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['campaign'])) {
    if (!isset($_SESSION['user'])) {
        echo '<div class="alert alert-danger">Please <a href="?page=login">login</a> to post a message.</div>';
        exit;
    }

    $cid = intval($_POST['campaign']);
    $msg = sanitize($_POST['message']);
    $userID = $_SESSION['user']['UserID'];

    $sql = "INSERT INTO MESSAGE (User_ID, Campaign_ID, Message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("iis", $userID, $cid, $msg);
        if ($stmt->execute()) {
            echo '<div class="alert alert-success">Message posted successfully. <a href="?page=campaign_detail&cid=' . $cid . '">Back to campaign</a></div>';
        } else {
            echo '<div class="alert alert-danger">Error posting message: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    } else {
        echo '<div class="alert alert-danger">Database error: ' . $conn->error . '</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>
