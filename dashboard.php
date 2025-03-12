<?php
      if (!isset($_SESSION['user'])) {
          echo '<div class="alert alert-danger">Please <a href="?page=login">login</a> to view your dashboard.</div>';
      } else {
          echo '<h1>Dashboard</h1>';
          $userID = $_SESSION['user']['UserID'];
          $result = $conn->query("SELECT * FROM CAMPAIGN WHERE CRID_USER = '$userID'");
          if ($result && $result->num_rows > 0) {
              echo '<h3>Your Campaigns:</h3><ul>';
              while ($row = $result->fetch_assoc()) {
                  echo '<li><a href="?page=campaign_detail&cid=' . $row['CID'] . '">' . $row['Name'] . '</a> - Goal: $' . $row['Goal'] . ' - Current: $' . $row['Current_Amount'] . '</li>';
              }
              echo '</ul>';
          } else {
              echo '<div class="alert alert-info">You have not created any campaigns.</div>';
          }
      }
?>