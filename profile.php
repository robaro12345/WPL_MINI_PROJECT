<?php
      if (!isset($_SESSION['user'])) {
          echo '<div class="alert alert-danger">Please <a href="?page=login">login</a> to view your profile.</div>';
      } else {
          echo '<h1>Profile</h1>';
          $userID = $_SESSION['user']['UserID'];
          $result = $conn->query("SELECT * FROM USERS WHERE UserID = '$userID'");
          if ($result && $result->num_rows > 0) {
              $user = $result->fetch_assoc();
              echo '<p>First Name: ' . $user['Fname'] . '</p>';
              echo '<p>Middle Name: ' . $user['Mname'] . '</p>';
              echo '<p>Last Name: ' . $user['Lname'] . '</p>';
              echo '<p>Wallet Address: ' . $user['Wallet_Address'] . '</p>';
              echo '<p>Role: ' . $user['Role'] . '</p>';
          } else {
              echo '<div class="alert alert-danger">User not found.</div>';
          }
      }


?>