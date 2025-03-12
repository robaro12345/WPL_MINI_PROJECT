<?php 
      echo '<h1>Explore Campaigns</h1>';
      $result = $conn->query("SELECT * FROM CAMPAIGN");
      if ($result && $result->num_rows > 0) {
          echo '<div class="row">';
          while ($row = $result->fetch_assoc()) {
              echo '<div class="col-md-4"><div class="card mb-4"><div class="card-body">';
              echo '<h5 class="card-title">' . $row['Name'] . '</h5>';
              echo '<p class="card-text">' . $row['Description'] . '</p>';
              echo '<p>Goal: $' . $row['Goal'] . ' | Current: $' . $row['Current_Amount'] . '</p>';
              echo '<a href="?page=campaign_detail&cid=' . $row['CID'] . '" class="btn btn-primary">View Details</a>';
              echo '</div></div></div>';
          }
          echo '</div>';
      } else {
          echo '<div class="alert alert-info">No campaigns available.</div>';
      }



?>