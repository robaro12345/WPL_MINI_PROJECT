<?php
if (!isset($_SESSION['user'])) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                <h4>Access Denied</h4>
                <p>Please <a href="?page=login" class="alert-link">login</a> to view your profile.</p>
            </div>
          </div>';
} else {
    $userID = $_SESSION['user']['UserID'];

    $stmt = $conn->prepare("SELECT U.*, E.Email FROM USERS U
                           LEFT JOIN EMAIL E ON U.UserID = E.UserID AND E.Primary_Email = TRUE
                           WHERE U.UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        ?>

        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-primary text-white p-4">
                            <div class="d-flex align-items-center">
                                <div class="profile-avatar mr-4">
                                    <div class="avatar-circle-lg">
                                        <?php echo strtoupper(substr($user['Fname'], 0, 1) . substr($user['Lname'], 0, 1)); ?>
                                    </div>
                                </div>
                                <div>
                                    <h2 class="mb-1"><?php echo htmlspecialchars($user['Fname'] . ' ' . $user['Lname']); ?></h2>
                                    <p class="mb-0">
                                        <span class="badge badge-light">
                                            <i class="fas fa-user-tag mr-1"></i><?php echo htmlspecialchars($user['Role']); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <!-- Personal Information -->
                            <div class="profile-section mb-4">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-user mr-2"></i>Personal Information
                                </h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted">First Name</label>
                                            <p class="mb-0"><?php echo htmlspecialchars($user['Fname']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted">Last Name</label>
                                            <p class="mb-0"><?php echo htmlspecialchars($user['Lname']); ?></p>
                                        </div>
                                    </div>
                                    <?php if ($user['Mname']): ?>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted">Middle Name</label>
                                            <p class="mb-0"><?php echo htmlspecialchars($user['Mname']); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted">Email</label>
                                            <p class="mb-0"><?php echo htmlspecialchars($user['Email']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Wallet Information -->
                            <div class="profile-section mb-4">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-wallet mr-2"></i>Wallet Information
                                </h4>
                                <div class="wallet-box bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="text-muted mb-0">Wallet Address</label>
                                        <button class="btn btn-sm btn-outline-primary copy-address"
                                                data-address="<?php echo htmlspecialchars($user['Wallet_Address']); ?>">
                                            <i class="fas fa-copy mr-1"></i>Copy
                                        </button>
                                    </div>
                                    <p class="text-break mb-0 font-monospace">
                                        <?php echo htmlspecialchars($user['Wallet_Address']); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Account Actions -->
                            <div class="profile-section">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-cog mr-2"></i>Account Actions
                                </h4>
                                <div class="d-flex">
                                    <a href="?page=edit_profile" class="btn btn-primary mr-2">
                                        <i class="fas fa-edit mr-1"></i>Edit Profile
                                    </a>
                                    <a href="?page=logout" class="btn btn-danger">
                                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
        .avatar-circle-lg {
            width: 80px;
            height: 80px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: white;
        }
        .profile-section {
            position: relative;
            padding-bottom: 1.5rem;
        }
        .profile-section:not(:last-child)::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(0,0,0,.1);
        }
        .info-item label {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }
        .wallet-box {
            border-left: 4px solid #4361ee;
        }
        .font-monospace {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }
        .badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        /* Dark Mode Styles */
        .dark-mode .card-header.bg-primary {
            background-color: #3046eb !important;
        }

        .dark-mode .text-primary {
            color: #6d8eff !important;
        }

        .dark-mode .badge-light {
            background-color: rgba(255, 255, 255, 0.1);
            color: #e0e0e0;
        }

        .dark-mode .profile-section:not(:last-child)::after {
            background: rgba(255, 255, 255, 0.1);
        }

        .dark-mode .wallet-box {
            background-color: #2d2d2d !important;
            border-left: 4px solid #4361ee;
        }

        .dark-mode .text-muted {
            color: #aaa !important;
        }

        .dark-mode .btn-outline-primary {
            color: #6d8eff;
            border-color: #6d8eff;
        }

        .dark-mode .btn-outline-primary:hover {
            background-color: #3046eb;
            color: #fff;
            border-color: #3046eb;
        }
        </style>

        <script>
        // Copy wallet address functionality
        document.querySelector('.copy-address').addEventListener('click', function() {
            const address = this.dataset.address;
            navigator.clipboard.writeText(address).then(() => {
                this.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-copy mr-1"></i>Copy';
                }, 2000);
            });
        });
        </script>

        <?php
    } else {
        echo '<div class="container mt-5">
                <div class="alert alert-danger text-center">User not found.</div>
              </div>';
    }
    $stmt->close();
}
?>
