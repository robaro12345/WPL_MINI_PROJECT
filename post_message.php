<?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campaign'])) {
          if (!isset($_SESSION['user'])) {
              echo '<div class="alert alert-danger">Please <a href="?page=login">login</a> to post a message.</div>';
          } else {
              $cid = intval($_POST['campaign']);
              $msg = sanitize($_POST['message']);
              $userID = $_SESSION['user']['UserID'];
              $sql = "INSERT INTO MESSAGE (MessageID, User_ID, Campaign_ID, Message) VALUES (NULL, '$userID', '$cid', '$msg')";
              if ($conn->query($sql) === TRUE) {
                  echo '<div class="alert alert-success">Message posted successfully. <a href="?page=campaign_detail&cid=' . $cid . '">Back to campaign</a></div>';
              } else {
                  echo '<div class="alert alert-danger">Error posting message: ' . $conn->error . '</div>';
              }
          }
      } else {
          echo '<div class="alert alert-danger">Invalid request.</div>';
      }

?>