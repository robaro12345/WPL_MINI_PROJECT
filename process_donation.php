<?php
// Require user to be logged in to make a donation
if (!isset($_SESSION['user'])) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                <h4>Access Denied</h4>
                <p>You must be logged in to make a donation.</p>
                <a href="?page=login" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
          </div>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['campaign_id'])) {
    $campaignID = intval($_POST['campaign_id']);
    $amount = floatval($_POST['amount']);

    if ($amount <= 0) {
        echo '<div class="alert alert-danger">Invalid donation amount.</div>';
        exit;
    }

    // Check if user is Admin or Campaigner - neither can donate
    if ($_SESSION['user']['Role'] === 'Admin' || $_SESSION['user']['Role'] === 'Campaigner') {
        $role = $_SESSION['user']['Role'];
        echo '<div class="container mt-5">
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                    <h4>Access Denied</h4>
                    <p>' . $role . 's are not allowed to make donations. Only users with the Donor role can donate.</p>
                    <a href="?page=explore" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i>Explore Campaigns
                    </a>
                </div>
              </div>';
        exit;
    }

    $userID = $_SESSION['user']['UserID'];
    $wallet = $_SESSION['user']['Wallet_Address'];

    // Placeholder for a real blockchain transaction hash (to be replaced with actual hash)
    $txHash = "WEB3_TX_HASH_PLACEHOLDER";

    // Prepare and execute donation insert
    $sql = "INSERT INTO DONATION (UserID, CampaignID, Wallet_Add, Trans_Hash, Timestamp, Amount)
            VALUES (?, ?, ?, ?, NOW(), ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("iissd", $userID, $campaignID, $wallet, $txHash, $amount);

        if ($stmt->execute()) {
            // Update campaign's current amount
            $update_sql = "UPDATE CAMPAIGN SET Current_Amount = Current_Amount + ? WHERE CID = ?";
            $update_stmt = $conn->prepare($update_sql);

            if ($update_stmt) {
                $update_stmt->bind_param("di", $amount, $campaignID);
                $update_stmt->execute();
                $update_stmt->close();
            }

            echo '<div class="alert alert-success">Donation successful. Thank you for your contribution!</div>';
        } else {
            echo '<div class="alert alert-danger">Error processing donation: ' . $stmt->error . '</div>';
        }

        $stmt->close();
    } else {
        echo '<div class="alert alert-danger">Database error: ' . $conn->error . '</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid donation request.</div>';
}
?>
