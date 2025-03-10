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
if (!isset($_GET['cid'])) {
    echo '<div class="alert alert-danger">Invalid campaign.</div>';
} else {
    $cid = intval($_GET['cid']);
    $result = $conn->query("SELECT * FROM CAMPAIGN WHERE CID = '$cid'");
    if ($result && $result->num_rows > 0) {
        $campaign = $result->fetch_assoc();
        // ...existing code for displaying campaign details and donation form...
    } else {
        echo '<div class="alert alert-danger">Campaign not found.</div>';
    }
}
?>
</div>
<?php include '../includes/footer.php'; ?>
