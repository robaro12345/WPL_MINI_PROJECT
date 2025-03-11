<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Well</title>
    
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ✅ Custom CSS (if needed) -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff !important;
        }
        .navbar-nav .nav-link {
            font-size: 1.1rem;
            color: #333 !important;
        }
        .navbar-nav .nav-link:hover {
            color: #007bff !important;
        }
        .btn-login {
            background-color: #dc3545;
            color: white !important;
            padding: 5px 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <!-- ✅ Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
        <div class="container">
            <a class="navbar-brand" href="home.php">Give Well</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link text-primary fw-bold" href="explore.php">Explore</a></li>
                    <li class="nav-item"><a class="nav-link" href="create_campaign.php">Create Campaign</a></li>
                    <?php if (isset($_SESSION['user'])) { ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php } else { ?>
                        <li class="nav-item"><a class="nav-link btn-login" href="login.php">Login</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
