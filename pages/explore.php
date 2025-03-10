<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php include '../includes/navbar.php'; ?>
<div class="container">
<h1>Explore Campaigns</h1>
<?php
$result = $conn->query("SELECT * FROM CAMPAIGN");
if ($result && $result->num_rows > 0) {
    echo '<div class="row">';
    while ($row = $result->fetch_assoc()) {
        // ...existing code for displaying campaigns...
    }
    echo '</div>';
} else {
    echo '<div class="alert alert-info">No campaigns available.</div>';
}
?>
</div>
<?php include '../includes/footer.php'; ?>
