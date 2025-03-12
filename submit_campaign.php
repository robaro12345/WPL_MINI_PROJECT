<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
    $name = sanitize($_POST['campName']);
    $desc = sanitize($_POST['campDesc']);
    $goal = floatval($_POST['campGoal']);
    $start = sanitize($_POST['campStart']);
    $end = sanitize($_POST['campEnd']);
    $userID = $_SESSION['user']['UserID'];

    $sql = "INSERT INTO CAMPAIGN (CRID_USER, CRID_ORG, Current_Amount, Name, Start_Date, End_Date, Description, Goal, Approval_Status) 
            VALUES (?, NULL, 0, ?, ?, ?, ?, ?, 0)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("issssd", $userID, $name, $start, $end, $desc, $goal);
        if ($stmt->execute()) {
            echo '<div class="alert alert-success text-center">🎉 Campaign created successfully! <a href="?page=my_campaigns" class="alert-link">View Campaigns</a></div>';
        } else {
            echo '<div class="alert alert-danger text-center">⚠️ Error creating campaign: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    } else {
        echo '<div class="alert alert-danger text-center">❌ Database error. Please try again later.</div>';
    }
} else {
    echo '<div class="alert alert-danger text-center">⛔ Unauthorized access.</div>';
}
?>
