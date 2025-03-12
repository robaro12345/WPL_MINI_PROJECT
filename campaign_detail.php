<?php
      if (!isset($_GET['cid'])) {
          echo '<div class="alert alert-danger">Invalid campaign.</div>';
      } else {
          $cid = intval($_GET['cid']);
          $result = $conn->query("SELECT * FROM CAMPAIGN WHERE CID = '$cid'");
          if ($result && $result->num_rows > 0) {
              $campaign = $result->fetch_assoc();
              echo '<h1>' . $campaign['Name'] . '</h1>';
              echo '<p>' . $campaign['Description'] . '</p>';
              echo '<p>Goal: $' . $campaign['Goal'] . ' | Current: $' . $campaign['Current_Amount'] . '</p>';
              ?>
              <h3>Donate</h3>
              <form action="?page=process_donation" method="POST">
                <input type="hidden" name="campaign" value="<?php echo $campaign['CID']; ?>">
                <div class="form-group">
                  <label for="amount">Donation Amount</label>
                  <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                </div>
                <button type="button" id="connectWalletDonation" class="btn btn-secondary">Connect Wallet</button>
                <button type="submit" class="btn btn-primary" id="donateBtn" disabled>Donate</button>
              </form>
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
              // Message Board for Campaign
              echo '<h3>Messages</h3>';
              $msgResult = $conn->query("SELECT M.*, U.Fname, U.Lname FROM MESSAGE M JOIN USERS U ON M.User_ID = U.UserID WHERE M.Campaign_ID = '$cid' ORDER BY M.MessageID DESC");
              if ($msgResult && $msgResult->num_rows > 0) {
                  while ($msg = $msgResult->fetch_assoc()) {
                      echo '<div class="border p-2 mb-2">';
                      echo '<strong>' . $msg['Fname'] . ' ' . $msg['Lname'] . ':</strong> ' . $msg['Message'];
                      echo '</div>';
                  }
              } else {
                  echo '<p>No messages yet. Be the first to comment!</p>';
              }
              ?>
              <h4>Post a Message</h4>
              <form action="?page=post_message" method="POST">
                <input type="hidden" name="campaign" value="<?php echo $cid; ?>">
                <div class="form-group">
                  <textarea name="message" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Message</button>
              </form>
              <?php
          } else {
              echo '<div class="alert alert-danger">Campaign not found.</div>';
          }
      }

?>