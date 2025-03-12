<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['campaign'])) {
    $campaignID = intval($_POST['campaign']);
    $amount = floatval($_POST['amount']);

    if ($amount <= 0) {
        echo '<div class="alert alert-danger">Invalid donation amount.</div>';
        exit;
    }

    if (isset($_SESSION['user'])) {
        $userID = $_SESSION['user']['UserID'];
        $wallet = $_SESSION['user']['Wallet_Address'];
    } else {
        $userID = 0; // Guest User
        $wallet = "Guest";
    }

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
