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
    .campaign-container {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: auto;
    }
    .campaign-container h1 {
        font-weight: bold;
        color: #007bff;
    }
    .login-message {
        text-align: center;
        font-size: 1.2rem;
    }
    .login-message a {
        color: #007bff;
        font-weight: bold;
    }
</style>

<div class="container mt-5">
    <div class="campaign-container">
        <?php if (!isset($_SESSION['user'])) { ?>
            <p class="login-message">Please <a href="login.php">login</a> to create a campaign.</p>
        <?php } else { ?>
            <h1 class="display-4 text-center">Create a Campaign</h1>
            <form action="submit_campaign.php" method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Campaign Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="goal_amount" class="form-label">Goal Amount ($)</label>
                    <input type="number" class="form-control" id="goal_amount" name="goal_amount" min="1" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Create Campaign</button>
            </form>
        <?php } ?>
    </div>
</div>

<?php include 'footer.php'; ?>
