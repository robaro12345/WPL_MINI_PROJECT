<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include 'navbar.php';
?>

<style>
    .campaign-card {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }
    .campaign-card:hover {
        transform: scale(1.03);
    }
    .campaign-title {
        font-weight: bold;
        color: #007bff;
    }
    .campaign-description {
        color: #555;
    }
</style>

<div class="container mt-5">
    <h1 class="display-4 text-center">Explore Campaigns</h1>
    
    <?php
    $query = "SELECT CID, Name, Description, Goal, Current_Amount FROM CAMPAIGN";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        echo '<div class="row row-cols-1 row-cols-md-3 g-4">';
        while ($row = $result->fetch_assoc()) { 
            ?>
            <div class="col">
                <div class="campaign-card p-3">
                    <h3 class="campaign-title"><?= htmlspecialchars($row['Name'] ?? "Untitled"); ?></h3>
                    <p class="campaign-description">
                        <?= htmlspecialchars(substr($row['Description'] ?? "No description available", 0, 100)) . '...'; ?>
                    </p>
                    <p><strong>Goal:</strong> $<?= number_format($row['Goal'] ?? 0, 2); ?></p>
                    <p><strong>Raised:</strong> $<?= number_format($row['Current_Amount'] ?? 0, 2); ?></p>
                    <a href="campaign.php?cid=<?= $row['CID'] ?? 0; ?>" class="btn btn-primary btn-sm">View Campaign</a>
                </div>
            </div>
        <?php }
        echo '</div>';
    } else {
        echo '<div class="alert alert-info text-center">No campaigns available.</div>';
    }
    
    $stmt->close();
    ?>
</div>

<?php include 'footer.php'; ?>
