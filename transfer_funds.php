<?php
checkAccess(['Campaigner']);

if (!isset($_SESSION['user'])) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                <h4>Access Denied</h4>
                <p>Please <a href="?page=login" class="alert-link">login</a> to transfer funds.</p>
            </div>
          </div>';
} else {
    $userID = $_SESSION['user']['UserID'];
    $stmt = $conn->prepare("SELECT CID, Name, Current_Amount, Goal, Start_Date, End_Date FROM CAMPAIGN WHERE CRID_USER = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-lg border-0 mb-4">
                        <div class="card-header bg-primary text-white p-4">
                            <h2 class="mb-0">
                                <i class="fas fa-exchange-alt mr-2"></i>Transfer Funds
                            </h2>
                        </div>
                        <div class="card-body p-4">';

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $progress = ($row['Current_Amount'] / $row['Goal']) * 100;
            $daysLeft = ceil((strtotime($row['End_Date']) - time()) / (60 * 60 * 24));
            
            echo '<div class="campaign-card mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h4 class="text-primary mb-3">' . htmlspecialchars($row['Name']) . '</h4>
                                    <div class="campaign-stats mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Progress</span>
                                            <span class="font-weight-bold">' . number_format($progress, 1) . '%</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" style="width: ' . $progress . '%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted mb-3">
                                        <span><i class="fas fa-coins mr-1"></i>Current: $' . number_format($row['Current_Amount'], 2) . '</span>
                                        <span><i class="fas fa-flag-checkered mr-1"></i>Goal: $' . number_format($row['Goal'], 2) . '</span>
                                    </div>
                                    <div class="text-muted">
                                        <small><i class="fas fa-calendar-alt mr-1"></i>' . $daysLeft . ' days remaining</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="transfer-section text-center">
                                        <div class="wallet-info mb-3">
                                            <span class="d-block text-muted mb-2">Your Wallet Address</span>
                                            <code class="bg-light p-2 rounded d-inline-block text-break">
                                                ' . htmlspecialchars($_SESSION['user']['Wallet_Address']) . '
                                            </code>
                                        </div>
                                        <button class="btn btn-primary btn-lg" 
                                                onclick="transferFunds(' . $row['CID'] . ', ' . $row['Current_Amount'] . ', \'' . $_SESSION['user']['Wallet_Address'] . '\')">
                                            <i class="fas fa-paper-plane mr-2"></i>Transfer Funds
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
        }
        ?>
        <script>
        async function transferFunds(cid, amount, recipientWallet) {
            if (typeof window.ethereum !== 'undefined') {
                window.web3 = new Web3(window.ethereum);
                try {
                    await window.ethereum.request({ method: 'eth_requestAccounts' });
                    
                    const params = [{
                        from: recipientWallet,
                        to: recipientWallet,
                        value: '0x' + (amount * 1e18).toString(16),
                        gas: '21000'
                    }];

                    const txHash = await window.ethereum.request({ 
                        method: 'eth_sendTransaction', 
                        params 
                    });
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Transfer Successful!',
                        text: 'Transaction Hash: ' + txHash,
                        confirmButtonColor: '#4361ee'
                    });
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Transfer Failed',
                        text: error.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'MetaMask Required',
                    text: 'Please install MetaMask to transfer funds.',
                    confirmButtonColor: '#ffc107'
                });
            }
        }
        </script>

        <!-- Include SweetAlert2 for better alerts -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
        .campaign-card .card {
            transition: transform 0.2s ease;
        }
        .campaign-card .card:hover {
            transform: translateY(-5px);
        }
        .wallet-info code {
            font-size: 0.85rem;
            max-width: 100%;
            word-break: break-all;
        }
        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }
        .progress-bar {
            border-radius: 10px;
        }
        </style>
        <?php
    } else {
        echo '<div class="text-center">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h4>No Campaigns Available</h4>
                <p class="text-muted">You don\'t have any campaigns available for fund transfer.</p>
                <a href="?page=create_campaign" class="btn btn-primary">
                    <i class="fas fa-plus-circle mr-2"></i>Create a Campaign
                </a>
              </div>';
    }

    echo '</div></div></div></div></div>';
}
?>
