<?php
session_start();

// Handle logout before any output
if (isset($_GET['page']) && $_GET['page'] === 'logout') {
    session_destroy();
    header("Location: ?page=home");
    exit;
}

$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

function checkAccess($allowedRoles) {
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['Role'], $allowedRoles)) {
        echo '<div class="container mt-5">
                <div class="alert alert-danger text-center">Access denied. You do not have permission to view this page.</div>
              </div>';
        exit;
    }
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
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Include web3.js from CDN -->
  <script src="https://cdn.jsdelivr.net/npm/web3/dist/web3.min.js"></script>
  <style>
    body { 
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease;
      background-color: #f8f9fa;
    }
    .dark-mode { 
      background-color: #121212; 
      color: #e0e0e0; 
    }
    .dark-mode .card {
      background-color: #1e1e1e;
      border-color: #333;
    }
    .dark-mode .navbar {
      background-color: #1e1e1e !important;
      border-color: #333;
    }
    .dark-mode .navbar-light .navbar-brand,
    .dark-mode .navbar-light .navbar-nav .nav-link {
      color: #fff;
    }
    .dark-mode .nav-link:hover, 
    .dark-mode .nav-link.active {
      background-color: rgba(255,255,255,.1);
      color: #fff !important;
    }
    .dark-mode .dropdown-menu {
      background-color: #1e1e1e;
      border-color: #333;
    }
    .dark-mode .dropdown-item {
      color: #e0e0e0;
    }
    .dark-mode .dropdown-item:hover {
      background-color: rgba(255,255,255,.1);
      color: #fff;
    }
    .dark-mode .form-control {
      background-color: #2d2d2d;
      border-color: #404040;
      color: #e0e0e0;
    }
    .dark-mode .form-control:focus {
      background-color: #333;
      border-color: #4361ee;
      color: #fff;
    }
    .dark-mode .table {
      color: #e0e0e0;
    }
    .dark-mode .table thead th {
      background-color: #1e1e1e;
      border-color: #333;
    }
    .dark-mode .table-hover tbody tr:hover {
      background-color: rgba(255,255,255,.05);
    }
    .dark-mode .modal-content {
      background-color: #1e1e1e;
      border-color: #333;
    }
    .dark-mode .modal-header {
      border-color: #333;
    }
    .dark-mode .modal-footer {
      border-color: #333;
    }
    .dark-mode .text-muted {
      color: #888 !important;
    }
    .dark-mode .progress {
      background-color: #2d2d2d;
    }
    .navbar { 
      box-shadow: 0 2px 4px rgba(0,0,0,.1);
      padding: 0.5rem 1rem;
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
    }
    .container { 
      padding-bottom: 50px; 
    }
    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 6px rgba(0,0,0,.1);
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .btn {
      border-radius: 8px;
      padding: 0.5rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .btn-primary {
      background-color: #4361ee;
      border-color: #4361ee;
    }
    .btn-primary:hover {
      background-color: #3046eb;
      border-color: #3046eb;
      transform: translateY(-2px);
    }
    .form-control {
      border-radius: 8px;
      padding: 0.75rem 1rem;
      border: 1px solid #dee2e6;
    }
    .form-control:focus {
      box-shadow: 0 0 0 0.2rem rgba(67,97,238,.25);
    }
    .alert {
      border-radius: 10px;
      border: none;
    }
    .tab { 
      cursor: pointer; 
      padding: 12px 24px;
      border-radius: 8px;
      border: 1px solid #dee2e6;
      display: inline-block;
      margin: 0 5px;
      transition: all 0.3s ease;
    }
    .tab.active { 
      background-color: #4361ee;
      color: white;
      border-color: #4361ee;
    }
    #mode-toggle {
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-size: 0.9rem;
    }
    /* Navbar Styles */
    .navbar {
        padding: 0.5rem 0;
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
    }

    .navbar-brand {
        font-size: 1.5rem;
    }

    .nav-link {
        padding: 0.5rem 1rem !important;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .nav-link:hover, .nav-link.active {
        background-color: rgba(67,97,238,.1);
        color: #4361ee !important;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 0 20px rgba(0,0,0,.1);
        border-radius: 10px;
    }

    .dropdown-item {
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: rgba(67,97,238,.1);
        color: #4361ee;
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        background-color: #4361ee;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    #mode-toggle {
        width: 38px;
        height: 38px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .dark-mode .nav-link:hover, 
    .dark-mode .nav-link.active {
        background-color: rgba(255,255,255,.1);
        color: #fff !important;
    }

    .dark-mode .dropdown-menu {
        background-color: #1e1e1e;
        border-color: #333;
    }

    .dark-mode .dropdown-item {
        color: #fff;
    }

    .dark-mode .dropdown-item:hover {
        background-color: rgba(255,255,255,.1);
        color: #fff;
    }

    .dark-mode .dropdown-divider {
        border-color: #333;
    }

    .navbar .container {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .navbar-nav {
        display: flex;
        align-items: center;
    }

    #navbarNav {
        justify-content: center;
    }
  </style>
</head>
<body class="">
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="?page=home">
            <i class="fas fa-hand-holding-heart text-primary mr-2"></i>
            <span class="font-weight-bold">Give Well</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>" href="?page=home">
                        <i class="fas fa-home mr-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'explore' ? 'active' : ''; ?>" href="?page=explore">
                        <i class="fas fa-search mr-1"></i>Explore
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'about' ? 'active' : ''; ?>" href="?page=about">
                        <i class="fas fa-info-circle mr-1"></i>About
                    </a>
                </li>
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($_SESSION['user']['Role'] === 'Campaigner'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="campaignDropdown" role="button" 
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bullhorn mr-1"></i>Campaigns
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="?page=create_campaign">
                                    <i class="fas fa-plus-circle mr-2"></i>Create Campaign
                                </a>
                                <a class="dropdown-item" href="?page=transfer_funds">
                                    <i class="fas fa-exchange-alt mr-2"></i>Transfer Funds
                                </a>
                            </div>
                        </li>
                    <?php endif; ?>
                    <?php if ($_SESSION['user']['Role'] === 'Admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'admin_panel' ? 'active' : ''; ?>" href="?page=admin_panel">
                                <i class="fas fa-user-shield mr-1"></i>Admin Panel
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ml-auto align-items-center">
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($_SESSION['user']['Role'] !== 'Admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>" href="?page=dashboard">
                                <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
                           id="userDropdown" role="button" data-toggle="dropdown">
                            <div class="avatar-circle mr-2">
                                <?php echo substr($_SESSION['user']['Fname'], 0, 1); ?>
                            </div>
                            <span><?php echo htmlspecialchars($_SESSION['user']['Fname']); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="?page=profile">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="?page=logout">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="?page=login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login / Register
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item ml-2">
                    <button id="mode-toggle" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>
            </ul>
        </div>
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
    case 'edit_profile':
      include 'edit_profile.php';
      break;
    default:
      echo '<h1>404 - Page Not Found</h1>';
  }
  ?>
  </div>

  <script>
    document.getElementById("mode-toggle").addEventListener("click", function(){
      document.body.classList.toggle("dark-mode");
      const icon = this.querySelector('i');
      if (document.body.classList.contains("dark-mode")) {
          icon.className = "fas fa-sun";
      } else {
          icon.className = "fas fa-moon";
      }
    });
  </script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
