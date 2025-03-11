<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php include 'navbar.php'; ?>

<style>
    .dashboard-container {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        margin: auto;
        text-align: center;
    }
    .alert a {
        font-weight: bold;
        text-decoration: none;
        color: #007bff;
    }
    .alert a:hover {
        text-decoration: underline;
    }
</style>

<div class="container mt-5">
    <div class="dashboard-container">
        <?php if (!isset($_SESSION['user'])) { ?>
            <div class="alert alert-danger">
                Please <a href="?page=login">login</a> to view your dashboard.
            </div>
        <?php } else { ?>
            <h1 class="display-4">Welcome to Your Dashboard</h1>
            <p class="lead">Manage your campaigns, track donations, and engage with the community.</p>
            
            <!-- Example Dashboard Content -->
            <a href="create_campaign.php" class="btn btn-primary mt-3">Create a New Campaign</a>
            <a href="my_campaigns.php" class="btn btn-secondary mt-3">View My Campaigns</a>
        <?php } ?>
    </div>
</div>

<?php include 'footer.php'; ?>
