<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php include 'navbar.php'; ?>

<style>
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/hero-bg.jpg') center/cover no-repeat;
        color: white;
        text-align: center;
        padding: 100px 20px;
        border-radius: 10px;
    }
    .hero-section h1 {
        font-size: 3rem;
        font-weight: bold;
    }
    .hero-section p {
        font-size: 1.2rem;
    }
    .btn-custom {
        background-color: #ff6b6b;
        color: white;
        font-size: 1.2rem;
        padding: 10px 20px;
        border-radius: 8px;
        transition: 0.3s;
    }
    .btn-custom:hover {
        background-color: #e63946;
    }
</style>

<div class="container mt-5 mb-5">
    <div class="hero-section">
        <h1>Welcome to Give Well</h1>
        <p class="lead">Your community donation platform. Connect with causes, donate, and make a difference.</p>
        <a href="explore.php" class="btn btn-custom">Explore Campaigns</a>
        <a href="create_campaign.php" class="btn btn-light mx-2">Start a Campaign</a>
    </div>
</div>

<?php include 'footer.php'; ?>
