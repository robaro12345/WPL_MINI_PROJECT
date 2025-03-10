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
    echo '<p>Please <a href="?page=login">login</a> to create a campaign.</p>';
} else { ?>
    <h1>Create a Campaign</h1>
    <form action="?page=submit_campaign" method="POST">
        <!-- ...existing form fields... -->
        <button type="submit" class="btn btn-primary">Create Campaign</button>
    </form>
<?php }
?>
</div>
<?php include '../includes/footer.php'; ?>
