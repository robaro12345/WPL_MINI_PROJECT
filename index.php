<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Give Well - Community Donation Platform</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Include web3.js from CDN -->
  <script src="https://cdn.jsdelivr.net/npm/web3/dist/web3.min.js"></script>
  <style>
    body { transition: background-color 0.3s, color 0.3s; }
    .dark-mode { background-color: #121212; color: #ffffff; }
    .navbar { margin-bottom: 20px; }
    .container { padding-bottom: 50px; }
    .tab { cursor: pointer; padding: 10px; border: 1px solid #ddd; display: inline-block; }
    .tab.active { background-color: #f8f9fa; }
  </style>
</head>
<body class="p-3">
<?php include 'includes/navbar.php'; ?>
<div class="container">
  <?php
  switch ($page) {
    case 'home':
      echo '<h1>Welcome to Give Well</h1>';
      echo '<p>Your community donation platform. Connect with causes, donate, and make a difference.</p>';
      break;
      
    case 'about':
      echo '<h1>About Give Well</h1>';
      echo '<p>Our mission is to bridge communities and support campaigns through transparent donations and engagement.</p>';
      break;
      
    case 'create_campaign':
      if (!isset($_SESSION['user'])) {
          echo '<p>Please <a href="?page=login">login</a> to create a campaign.</p>';
      } else { ?>
          <h1>Create a Campaign</h1>
          <form action="?page=submit_campaign" method="POST">
            <div class="form-group">
              <label for="campName">Campaign Name</label>
              <input type="text" name="campName" id="campName" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="campDesc">Description</label>
              <textarea name="campDesc" id="campDesc" rows="3" class="form-control" required></textarea>
            </div>
            <div class="form-group">
              <label for="campGoal">Goal Amount</label>
              <input type="number" step="0.01" name="campGoal" id="campGoal" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="campStart">Start Date</label>
              <input type="date" name="campStart" id="campStart" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="campEnd">End Date</label>
              <input type="date" name="campEnd" id="campEnd" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Campaign</button>
          </form>
      <?php }
      break;
      
    case 'submit_campaign':
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
      break;
      
    case 'explore':
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
      break;
      
    case 'campaign_detail':
      if (!isset($_GET['cid'])) {
          echo '<div class="alert alert-danger">Invalid campaign.</div>';
      } else {
          $cid = intval($_GET['cid']);
          $result = $conn->query("SELECT * FROM CAMPAIGN WHERE CID = '$cid'");
          if ($result && $result->num_rows > 0) {
              $campaign = $result->fetch_assoc();
              echo '<h1>' . $campaign['Name'] . '</h1>';
              echo '<p>' . $campaign['Description'] . '</p>';
              echo '<p>Goal: $' . $campaign['Goal'] . ' | Current: $' . $campaign['Current_Amount'] . '</p>';
              ?>
              <h3>Donate</h3>
              <form action="?page=process_donation" method="POST">
                <input type="hidden" name="campaign" value="<?php echo $campaign['CID']; ?>">
                <div class="form-group">
                  <label for="amount">Donation Amount</label>
                  <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                </div>
                <button type="button" id="connectWalletDonation" class="btn btn-secondary">Connect Wallet</button>
                <button type="submit" class="btn btn-primary" id="donateBtn" disabled>Donate</button>
              </form>
              <script>
                async function connectWalletDonation() {
                  if (typeof window.ethereum !== 'undefined') {
                    window.web3 = new Web3(window.ethereum);
                    try {
                      await window.ethereum.request({ method: 'eth_requestAccounts' });
                      document.getElementById("donateBtn").disabled = false;
                      alert('Wallet connected for donation');
                    } catch (error) {
                      alert('Wallet connection failed: ' + error.message);
                    }
                  } else {
                    alert('Please install MetaMask');
                  }
                }
                document.getElementById("connectWalletDonation").addEventListener("click", connectWalletDonation);
              </script>
              <?php
              // Message Board for Campaign
              echo '<h3>Messages</h3>';
              $msgResult = $conn->query("SELECT M.*, U.Fname, U.Lname FROM MESSAGE M JOIN USERS U ON M.User_ID = U.UserID WHERE M.Campaign_ID = '$cid' ORDER BY M.MessageID DESC");
              if ($msgResult && $msgResult->num_rows > 0) {
                  while ($msg = $msgResult->fetch_assoc()) {
                      echo '<div class="border p-2 mb-2">';
                      echo '<strong>' . $msg['Fname'] . ' ' . $msg['Lname'] . ':</strong> ' . $msg['Message'];
                      echo '</div>';
                  }
              } else {
                  echo '<p>No messages yet. Be the first to comment!</p>';
              }
              ?>
              <h4>Post a Message</h4>
              <form action="?page=post_message" method="POST">
                <input type="hidden" name="campaign" value="<?php echo $cid; ?>">
                <div class="form-group">
                  <textarea name="message" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Message</button>
              </form>
              <?php
          } else {
              echo '<div class="alert alert-danger">Campaign not found.</div>';
          }
      }
      break;
      
    case 'process_donation':
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
      break;
      
    case 'post_message':
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
      break;
      
    case 'dashboard':
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
      break;
      
    case 'transfer_funds':
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
      break;
      
    case 'profile':
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
      break;
      
    case 'login':
      if (isset($_SESSION['user'])) {
          header("Location: ?page=dashboard");
          exit;
      }
      ?>
      <h1>Login / Register</h1>
      <div>
        <div class="tab-container">
          <span class="tab active" id="tab-login" onclick="showTab('login')">Login</span>
          <span class="tab" id="tab-register" onclick="showTab('register')">Register</span>
        </div>
        <div id="login-form">
          <form action="?page=login_process&type=login" method="POST">
            <div class="form-group">
              <label for="login_email">Email</label>
              <input type="email" name="email" id="login_email" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="login_password">Password</label>
              <input type="password" name="password" id="login_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
          </form>
        </div>
        <div id="register-form" style="display:none;">
          <form action="?page=login_process&type=register" method="POST">
            <div class="form-group">
              <label for="reg_fname">First Name</label>
              <input type="text" name="fname" id="reg_fname" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="reg_mname">Middle Name</label>
              <input type="text" name="mname" id="reg_mname" class="form-control">
            </div>
            <div class="form-group">
              <label for="reg_lname">Last Name</label>
              <input type="text" name="lname" id="reg_lname" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="reg_email">Email</label>
              <input type="email" name="email" id="reg_email" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="reg_password">Password</label>
              <input type="password" name="password" id="reg_password" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="reg_role">Role</label>
              <select name="role" id="reg_role" class="form-control" required>
                <option value="Donor">Donor</option>
                <option value="Campaigner">Campaigner</option>
              </select>
            </div>
            <div class="form-group">
              <label for="reg_wallet">Wallet Address</label>
              <input type="text" name="wallet" id="reg_wallet" class="form-control" required>
              <button type="button" id="connectWalletRegister" class="btn btn-secondary mt-2">Connect Wallet via MetaMask</button>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
          </form>
          <script>
            async function connectWalletRegister(){
              if (typeof window.ethereum !== 'undefined') {
                window.web3 = new Web3(window.ethereum);
                try {
                  const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                  document.getElementById("reg_wallet").value = accounts[0];
                  alert('Wallet connected: ' + accounts[0]);
                } catch (error) {
                  alert('Wallet connection failed: ' + error.message);
                }
              } else {
                alert('Please install MetaMask');
              }
            }
            document.getElementById("connectWalletRegister").addEventListener("click", connectWalletRegister);
          </script>
        </div>
      </div>
      <script>
        function showTab(tab){
          if (tab === 'login') {
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('tab-login').classList.add('active');
            document.getElementById('tab-register').classList.remove('active');
          } else {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
            document.getElementById('tab-register').classList.add('active');
            document.getElementById('tab-login').classList.remove('active');
          }
        }
      </script>
      <?php
      break;
      
    case 'login_process':
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
      break;
      
    case 'admin_panel':
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
      break;
      
    case 'approve_campaign':
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
      break;
      
    case 'logout':
      session_destroy();
      header("Location: ?page=home");
      exit;
      
    default:
      echo '<h1>404 - Page Not Found</h1>';
  }
  ?>
</div>
<?php include 'includes/footer.php'; ?>
<script>
  document.getElementById("mode-toggle").addEventListener("click", function(){
    document.body.classList.toggle("dark-mode");
  });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
