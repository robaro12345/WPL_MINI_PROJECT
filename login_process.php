<?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $type = $_GET['type'] ?? 'login';
          
          if ($type === 'login') {
              $email = sanitize($_POST['email']);
              $password = sanitize($_POST['password']);
              
              // First get the user from email
              $query = "SELECT U.*, E.Email 
                     FROM USERS U 
                     INNER JOIN EMAIL E ON U.UserID = E.UserID 
                     WHERE E.Email = ? AND E.Primary_Email = TRUE";
              
              if ($stmt = $conn->prepare($query)) {
                  $stmt->bind_param("s", $email);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  
                  if ($result && $result->num_rows > 0) {
                      $user = $result->fetch_assoc();
                      // Verify password using password_verify instead of md5
                      if (password_verify($password, $user['Password'])) {
                          // Set session variables
                          $_SESSION['user'] = array(
                              'UserID' => $user['UserID'],
                              'Fname' => $user['Fname'],
                              'Lname' => $user['Lname'],
                              'Email' => $user['Email'],
                              'Role' => $user['Role'],
                              'Wallet_Address' => $user['Wallet_Address']
                          );
                          echo '<div class="alert alert-success">Login successful! Redirecting...</div>';
                          echo '<script>setTimeout(function(){ window.location = "?page=dashboard"; }, 2000);</script>';
                      } else {
                          echo '<div class="alert alert-danger">Invalid password. Please try again.</div>';
                      }
                  } else {
                      echo '<div class="alert alert-danger">Email not found. Please register first.</div>';
                  }
                  $stmt->close();
              }
          } else { // register
              $fname = sanitize($_POST['fname']);
              $mname = sanitize($_POST['mname']) ?: null;
              $lname = sanitize($_POST['lname']);
              $email = sanitize($_POST['email']);
              $password = password_hash(sanitize($_POST['password']), PASSWORD_DEFAULT); // Use proper password hashing
              $role = sanitize($_POST['role']);
              $wallet = sanitize($_POST['wallet']);
              
              // Start transaction
              $conn->begin_transaction();
              
              try {
                  // Check for existing email or wallet
                  $check_query = "SELECT U.UserID FROM USERS U 
                              LEFT JOIN EMAIL E ON U.UserID = E.UserID 
                              WHERE U.Wallet_Address = ? OR E.Email = ?";
                              
                  $stmt = $conn->prepare($check_query);
                  $stmt->bind_param("ss", $wallet, $email);
                  $stmt->execute();
                  $check_result = $stmt->get_result();
                  
                  if ($check_result->num_rows > 0) {
                      throw new Exception("User with this email or wallet already exists");
                  }
                  
                  // Insert user
                  $user_query = "INSERT INTO USERS (Fname, Mname, Lname, Wallet_Address, Creation_Date, Role, Password) 
                              VALUES (?, ?, ?, ?, CURDATE(), ?, ?)";
                              
                  $stmt = $conn->prepare($user_query);
                  $stmt->bind_param("ssssss", $fname, $mname, $lname, $wallet, $role, $password);
                  $stmt->execute();
                  $userID = $conn->insert_id;
                  
                  // Insert email
                  $email_query = "INSERT INTO EMAIL (UserID, Email, Primary_Email) VALUES (?, ?, TRUE)";
                  $stmt = $conn->prepare($email_query);
                  $stmt->bind_param("is", $userID, $email);
                  $stmt->execute();
                  
                  // Commit transaction
                  $conn->commit();
                  
                  // Set session
                  $_SESSION['user'] = array(
                      'UserID' => $userID,
                      'Fname' => $fname,
                      'Lname' => $lname,
                      'Email' => $email,
                      'Role' => $role,
                      'Wallet_Address' => $wallet
                  );
                  
                  echo '<div class="alert alert-success">Registration successful! Redirecting...</div>';
                  echo '<script>setTimeout(function(){ window.location = "?page=dashboard"; }, 2000);</script>';
                  
              } catch (Exception $e) {
                  $conn->rollback();
                  echo '<div class="alert alert-danger">Registration failed: ' . $e->getMessage() . '</div>';
              }
          }
      } else {
          echo '<div class="alert alert-danger">Invalid request.</div>';
      }

?>