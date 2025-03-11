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
    .about-container {
        background: linear-gradient(to right, #007bff, #6610f2);
        color: white;
        padding: 60px 20px;
        border-radius: 10px;
        text-align: center;
    }
    .about-container h1 {
        font-weight: bold;
    }
    .about-container p {
        font-size: 1.2rem;
    }
</style>

<div class="container mt-5">
    <div class="about-container shadow-lg">
        <h1 class="display-4">About <span class="fw-bold">Give Well</span></h1>
        <p class="lead">Our mission is to bridge communities and support campaigns through transparent donations and engagement.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
