<?php
if (!isset($_SESSION['user'])) {
    echo '<div class="alert alert-danger text-center">Please <a href="?page=login" class="alert-link">login</a> to view your profile.</div>';
} else {
    $userID = $_SESSION['user']['UserID'];
    
    $stmt = $conn->prepare("SELECT Fname, Mname, Lname, Wallet_Address, Role FROM USERS WHERE UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        ?>

        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-lg border-0 rounded-lg p-4">
                        <div class="card-body">
                            <h2 class="text-center text-primary fw-bold">My Profile</h2>
                            <hr class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>First Name:</strong></p>
                                    <p class="text-muted"><?php echo htmlspecialchars($user['Fname']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Last Name:</strong></p>
                                    <p class="text-muted"><?php echo htmlspecialchars($user['Lname']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Middle Name:</strong></p>
                                    <p class="text-muted"><?php echo htmlspecialchars($user['Mname'] ?: 'N/A'); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Role:</strong></p>
                                    <span class="badge bg-success px-3 py-2"><?php echo htmlspecialchars($user['Role']); ?></span>
                                </div>
                                <div class="col-12 mt-3">
                                    <p class="mb-2"><strong>Wallet Address:</strong></p>
                                    <div class="wallet-box text-monospace text-muted p-2 bg-light rounded">
                                        <?php echo htmlspecialchars($user['Wallet_Address']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-center mt-3">
                            <a href="?page=edit_profile" class="btn btn-outline-primary me-2">Edit Profile</a>
                            <a href="?page=logout" class="btn btn-danger">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .wallet-box {
                font-size: 14px;
                border-left: 4px solid #007bff;
            }
            .badge {
                font-size: 14px;
            }
            .card {
                transition: 0.3s;
            }
            .card:hover {
                transform: scale(1.02);
            }
        </style>

        <?php
    } else {
        echo '<div class="alert alert-danger text-center">User not found.</div>';
    }
    $stmt->close();
}
?>
