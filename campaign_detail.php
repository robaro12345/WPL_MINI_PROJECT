<?php
if (!isset($_GET['cid'])) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">Invalid campaign.</div>
          </div>';
} else {
    $cid = intval($_GET['cid']);
    $result = $conn->query("SELECT * FROM CAMPAIGN WHERE CID = '$cid'");

    if ($result && $result->num_rows > 0) {
        $campaign = $result->fetch_assoc();
        
        echo '<div class="container mt-5">';
        echo '<h1 class="text-center">' . htmlspecialchars($campaign['Name']) . '</h1>';
        echo '<p class="text-muted text-center">' . htmlspecialchars($campaign['Description']) . '</p>';
        
        echo '<div class="card shadow-sm p-4 mb-4">
                <h4 class="text-primary">Campaign Progress</h4>
                <p><strong>Goal:</strong> $' . number_format($campaign['Goal'], 2) . '</p>
                <p><strong>Current:</strong> $' . number_format($campaign['Current_Amount'], 2) . '</p>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: ' . (($campaign['Current_Amount'] / $campaign['Goal']) * 100) . '%"></div>
                </div>
              </div>';
        
        echo '<div class="card shadow-sm p-4 mb-4">
                <h3 class="text-primary">Donate</h3>
                <form action="?page=process_donation" method="POST">
                  <input type="hidden" name="campaign" value="' . $campaign['CID'] . '">
                  <div class="form-group">
                    <label for="amount"><strong>Donation Amount</strong></label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                  </div>
                  <button type="button" id="connectWalletDonation" class="btn btn-secondary mt-2">Connect Wallet</button>
                  <button type="submit" class="btn btn-primary mt-2" id="donateBtn" disabled>Donate</button>
                </form>
              </div>';
        ?>
        
        <script>
            async function connectWalletDonation() {
                if (typeof window.ethereum !== 'undefined') {
                    window.web3 = new Web3(window.ethereum);
                    try {
                        await window.ethereum.request({ method: 'eth_requestAccounts' });
                        document.getElementById("donateBtn").disabled = false;
                        alert('Wallet connected for donation');
                    } catch (error) {
                        alert('Wallet connection failed: ' + error.message);
                    }
                } else {
                    alert('Please install MetaMask');
                }
            }
            document.getElementById("connectWalletDonation").addEventListener("click", connectWalletDonation);
        </script>

        <?php
        echo '<div class="card shadow-sm p-4 mt-4">
                <h3 class="text-primary">Messages</h3>';
        
        $msgResult = $conn->query("SELECT M.*, U.Fname, U.Lname FROM MESSAGE M JOIN USERS U ON M.User_ID = U.UserID WHERE M.Campaign_ID = '$cid' ORDER BY M.MessageID DESC");
        
        if ($msgResult && $msgResult->num_rows > 0) {
            while ($msg = $msgResult->fetch_assoc()) {
                echo '<div class="border p-3 mb-2 rounded">
                        <strong>' . htmlspecialchars($msg['Fname']) . ' ' . htmlspecialchars($msg['Lname']) . ':</strong> ' . htmlspecialchars($msg['Message']) . '
                      </div>';
            }
        } else {
            echo '<p class="text-muted">No messages yet. Be the first to comment!</p>';
        }
        
        echo '<h4 class="mt-3">Post a Message</h4>
                <form action="?page=post_message" method="POST">
                  <input type="hidden" name="campaign" value="' . $cid . '">
                  <div class="form-group">
                    <textarea name="message" class="form-control" rows="3" required></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary mt-2">Post Message</button>
                </form>
              </div>';
        
        echo '</div>'; // Closing container
    } else {
        echo '<div class="container mt-5">
                <div class="alert alert-danger text-center">Campaign not found.</div>
              </div>';
    }
}
?>
