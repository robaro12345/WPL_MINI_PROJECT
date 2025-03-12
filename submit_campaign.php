<?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
          $name = sanitize($_POST['campName']);
          $desc = sanitize($_POST['campDesc']);
          $goal = floatval($_POST['campGoal']);
          $start = sanitize($_POST['campStart']);
          $end = sanitize($_POST['campEnd']);
          $userID = $_SESSION['user']['UserID'];
          $sql = "INSERT INTO CAMPAIGN (CID, CRID_USER, CRID_ORG, Current_Amount, Name, Start_Date, End_Date, Description, Goal, Approval_Status)
                  VALUES (NULL, '$userID', NULL, 0, '$name', '$start', '$end', '$desc', '$goal', 0)";
          if ($conn->query($sql) === TRUE) {
              echo '<div class="alert alert-success">Campaign created successfully.</div>';
          } else {
              echo '<div class="alert alert-danger">Error creating campaign: ' . $conn->error . '</div>';
          }
      } else {
          echo '<div class="alert alert-danger">Unauthorized access.</div>';
      }

?>