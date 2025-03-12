<?php
if (!isset($_SESSION['user'])) {
          echo '<div class="alert alert-danger">Please <a href="?page=login">login</a> to transfer funds.</div>';
      } else {
          echo '<h1>Transfer Funds</h1>';
          $userID = $_SESSION['user']['UserID'];
          $result = $conn->query("SELECT * FROM CAMPAIGN WHERE CRID_USER = '$userID'");
          if ($result && $result->num_rows > 0) {
              echo '<ul>';
              while ($row = $result->fetch_assoc()) {
                  // Retrieve campaign creator’s wallet address from USERS table using CRID_USER
                  $userRes = $conn->query("SELECT Wallet_Address FROM USERS WHERE UserID = " . $row['CRID_USER']);
                  $creatorWallet = $userRes->fetch_assoc()['Wallet_Address'];
                  echo '<li>';
                  echo '<strong>' . $row['Name'] . '</strong> - Current Amount: $' . $row['Current_Amount'];
                  echo ' <button class="btn btn-info btn-sm" onclick="transferFunds(' . $row['CID'] . ', ' . $row['Current_Amount'] . ', ' . json_encode($creatorWallet) . ')">Transfer Funds</button>';
                  echo '</li>';
              }
              echo '</ul>';
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
                        value: '0x' + (amount * 1e18).toString(16)
                      }];
                      const txHash = await window.ethereum.request({ method: 'eth_sendTransaction', params });
                      alert('Funds transferred. Transaction hash: ' + txHash);
                    } catch (error) {
                      alert('Transfer failed: ' + error.message);
                    }
                  } else {
                    alert('Please install MetaMask');
                  }
                }
              </script>
              <?php
          } else {
              echo '<div class="alert alert-info">No campaigns available for fund transfer.</div>';
          }
      }

?>