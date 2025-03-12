<?php
if (!isset($_SESSION['user'])) {
    echo '<div class="alert alert-danger text-center">⛔ Please <a href="?page=login" class="alert-link">login</a> to transfer funds.</div>';
} else {
    echo '<h1 class="mb-4">Transfer Funds</h1>';

    $userID = $_SESSION['user']['UserID'];
    $stmt = $conn->prepare("SELECT CID, Name, Current_Amount, CRID_USER FROM CAMPAIGN WHERE CRID_USER = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        echo '<div class="row">';

        while ($row = $result->fetch_assoc()) {
            $stmtUser = $conn->prepare("SELECT Wallet_Address FROM USERS WHERE UserID = ?");
            $stmtUser->bind_param("i", $row['CRID_USER']);
            $stmtUser->execute();
            $userRes = $stmtUser->get_result();
            $creatorWallet = $userRes->fetch_assoc()['Wallet_Address'];

            echo '<div class="col-md-6">';
            echo '<div class="card shadow-sm mb-3">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($row['Name']) . '</h5>';
            echo '<p class="card-text">💰 <strong>Current Amount:</strong> $' . number_format($row['Current_Amount'], 2) . '</p>';
            echo '<button class="btn btn-primary btn-sm" onclick="transferFunds(' . $row['CID'] . ', ' . $row['Current_Amount'] . ', ' . json_encode($creatorWallet) . ')">Transfer Funds</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

        ?>
        <script>
        const senderWallet = <?php echo json_encode($_SESSION['user']['Wallet_Address']); ?>;

        async function transferFunds(cid, amount, recipientWallet) {
            if (typeof window.ethereum !== 'undefined') {
                window.web3 = new Web3(window.ethereum);
                try {
                    await window.ethereum.request({ method: 'eth_requestAccounts' });

                    const params = [{
                        from: senderWallet,
                        to: recipientWallet,
                        value: '0x' + (amount * 1e18).toString(16),
                        gas: '21000' 
                    }];

                    const txHash = await window.ethereum.request({ method: 'eth_sendTransaction', params });
                    alert('✅ Funds transferred successfully! Transaction hash: ' + txHash);
                } catch (error) {
                    alert('⚠️ Transfer failed: ' + error.message);
                }
            } else {
                alert('⚠️ MetaMask is required for fund transfers. Please install it.');
            }
        }
        </script>
        <?php
    } else {
        echo '<div class="alert alert-info text-center">ℹ️ No campaigns available for fund transfer.</div>';
    }
}
?>
