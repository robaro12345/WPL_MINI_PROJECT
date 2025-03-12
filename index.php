
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
<body class="">
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="?page=home">Give Well</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item"><a class="nav-link" href="?page=about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="?page=create_campaign">Create Campaign</a></li>
        <li class="nav-item"><a class="nav-link" href="?page=explore">Explore</a></li>
        <?php if (isset($_SESSION['user'])): ?>
          <li class="nav-item"><a class="nav-link" href="?page=dashboard">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="?page=profile">Profile</a></li>
          <?php if (($_SESSION['user']['Role'] ?? '') == 'Admin'): ?>
            <li class="nav-item"><a class="nav-link" href="?page=admin_panel">Admin Panel</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="?page=transfer_funds">Transfer Funds</a></li>
          <li class="nav-item"><a class="nav-link" href="?page=logout">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="?page=login">Login/Register</a></li>
        <?php endif; ?>
      </ul>
      <button id="mode-toggle" class="btn btn-secondary">Toggle Dark Mode</button>
    </div>
  </nav>
  
  <div class="">
  <?php
  switch ($page) {
    case 'home':
      include 'home.php';
      break;
      
    case 'about':
      include 'about.php';
      break;
      
    case 'create_campaign':
        include 'create_campaign.php';
      break;
      
    case 'submit_campaign':
        include 'submit_campaign.php';
      break;
      
    case 'explore':
      include 'explore.php';
      break;
      
    case 'campaign_detail':
      include 'campaign_detail.php';
      break;
      
    case 'process_donation':
      include 'process_donation.php';
      break;
      
    case 'post_message':
      include 'post_message.php';
      break;
      
    case 'dashboard':
      include 'dashboard.php';
      break;
      
    case 'transfer_funds':
      include 'transfer_funds.php';
      break;
      
    case 'profile':
      include 'profile.php';
      break;
      
    case 'login':
      include 'login.php';
      break;
      
    case 'login_process':
      include 'login_process.php';
      break;
      
    case 'admin_panel':
      include 'admin_panel.php';
      break;
      
    case 'approve_campaign':
      include 'approve_campaign.php';
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
