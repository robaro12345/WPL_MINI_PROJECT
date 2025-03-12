<?php
      if (!isset($_SESSION['user']) || (($_SESSION['user']['Role'] ?? '') != 'Admin')) {
          echo '<div class="alert alert-danger">Access denied.</div>';
      } else {
          echo '<h1>Admin Panel</h1>';
          $result = $conn->query("SELECT * FROM CAMPAIGN");
          if ($result && $result->num_rows > 0) {
              echo '<table class="table table-bordered"><thead><tr><th>ID</th><th>Name</th><th>Status</th><th>Action</th></tr></thead><tbody>';
              while ($row = $result->fetch_assoc()) {
                  echo '<tr><td>' . $row['CID'] . '</td><td>' . $row['Name'] . '</td><td>' . ($row['Approval_Status'] ? 'Approved' : 'Pending') . '</td>';
                  if (!$row['Approval_Status']) {
                      echo '<td><a href="?page=approve_campaign&campaign=' . $row['CID'] . '" class="btn btn-success btn-sm">Approve</a></td>';
                  } else {
                      echo '<td>--</td>';
                  }
                  echo '</tr>';
              }
              echo '</tbody></table>';
          } else {
              echo '<div class="alert alert-info">No campaigns found.</div>';
          }
      }
?>