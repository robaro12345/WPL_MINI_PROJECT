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
    }
    .campaign-title {
        font-weight: bold;
        color: #007bff;
    }
    .alert {
        text-align: center;
        font-weight: bold;
    }
</style>

<div class="container mt-5">
    <div class="campaign-container">
        <?php
        if (!isset($_GET['cid']) || !is_numeric($_GET['cid'])) {
            echo '<div class="alert alert-danger">Invalid campaign.</div>';
        } else {
            $cid = intval($_GET['cid']);
            $stmt = $conn->prepare("SELECT * FROM CAMPAIGN WHERE CID = ?");
            $stmt->bind_param("i", $cid);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $campaign = $result->fetch_assoc();
                ?>
                <h1 class="campaign-title"><?= htmlspecialchars($campaign['title']); ?></h1>
                <p class="lead"><?= htmlspecialchars($campaign['description']); ?></p>
                <p><strong>Goal:</strong> $<?= number_format($campaign['goal_amount'], 2); ?></p>
                <p><strong>Raised:</strong> $<?= number_format($campaign['raised_amount'], 2); ?></p>
                
                <a href="donate.php?cid=<?= $cid; ?>" class="btn btn-primary mt-3">Donate Now</a>
                <?php
            } else {
                echo '<div class="alert alert-danger">Campaign not found.</div>';
            }
            $stmt->close();
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>
