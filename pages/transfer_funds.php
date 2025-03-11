<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");

if ($conn->connect_error) {
    die('<div class="alert alert-danger text-center">Database connection failed: ' . $conn->connect_error . '</div>');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Funds | Give Well</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .alert {
            text-align: center;
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <?php
        if (!isset($_SESSION['user'])) {
            echo '<div class="alert alert-danger">
                    🚨 Please <a href="login.php" class="alert-link">login</a> to transfer funds.
                  </div>';
            echo '<script>setTimeout(() => { window.location.href = "login.php"; }, 3000);</script>'; // Redirect after 3 sec
        } else {
            echo '<h2 class="text-center mb-4">💸 Transfer Funds</h2>';
            echo '<p class="text-muted text-center">Securely transfer your funds within Give Well.</p>';
            ?>
            
            <form method="POST" action="process_transfer.php" class="p-4 border rounded shadow">
                <div class="mb-3">
                    <label for="recipient" class="form-label">Recipient Email</label>
                    <input type="email" id="recipient" name="recipient" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (₹)</label>
                    <input type="number" id="amount" name="amount" class="form-control" min="1" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Transfer Now</button>
            </form>
        <?php
        }
        ?>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
