<?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campaign'])) {
          $campaignID = intval($_POST['campaign']);
          $amount = floatval($_POST['amount']);
          if (isset($_SESSION['user'])) {
              $userID = $_SESSION['user']['UserID'];
              $wallet = $_SESSION['user']['Wallet_Address'];
          } else {
              $userID = 0;
              $wallet = "Guest";
          }
          // (In a real integration, obtain a transaction hash from MetaMask after sending funds)
          $txHash = "WEB3_TX_HASH_PLACEHOLDER";
          $sql = "INSERT INTO DONATION (DonationID, UserID, CampaignID, Wallet_Add, Trans_Hash, Timestamp, Amount)
                  VALUES (NULL, '$userID', '$campaignID', '$wallet', '$txHash', NOW(), '$amount')";
          if ($conn->query($sql) === TRUE) {
              $conn->query("UPDATE CAMPAIGN SET Current_Amount = Current_Amount + $amount WHERE CID = '$campaignID'");
              echo '<div class="alert alert-success">Donation successful. Thank you for your contribution!</div>';
          } else {
              echo '<div class="alert alert-danger">Error processing donation: ' . $conn->error . '</div>';
          }
      } else {
          echo '<div class="alert alert-danger">Invalid donation request.</div>';
      }

?>