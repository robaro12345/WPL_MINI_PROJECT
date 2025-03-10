<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Funds</title>
    <link rel="stylesheet" href="path/to/your/css/file.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <?php
        if (!isset($_SESSION['user'])) {
            echo '<div class="alert alert-danger">Please <a href="?page=login">login</a> to transfer funds.</div>';
        } else {
            // ...existing code for transferring funds...
        }
        ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
