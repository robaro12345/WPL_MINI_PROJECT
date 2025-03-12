<?php
      if (!isset($_SESSION['user']) || (($_SESSION['user']['Role'] ?? '') != 'Admin')) {
          echo '<div class="alert alert-danger">Access denied.</div>';
      } else {
          if (isset($_GET['campaign'])) {
              $campID = intval($_GET['campaign']);
              $conn->query("UPDATE CAMPAIGN SET Approval_Status = 1 WHERE CID = '$campID'");
              echo '<div class="alert alert-success">Campaign approved. <a href="?page=admin_panel">Back to Admin Panel</a></div>';
          } else {
              echo '<div class="alert alert-danger">Invalid campaign.</div>';
          }
      }

?>