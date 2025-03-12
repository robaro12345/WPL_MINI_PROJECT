<?php
if (!isset($_SESSION['user'])) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">
                🚫 Please <a href="?page=login" class="alert-link">login</a> to view your dashboard.
            </div>
          </div>';
} else {
    $userID = $_SESSION['user']['UserID'];
    $result = $conn->query("SELECT * FROM CAMPAIGN WHERE CRID_USER = '$userID'");

    echo '<div class="container mt-5">
            <div class="card shadow-lg p-4">
                <h1 class="text-center text-primary mb-4">
                    <i class="fas fa-tachometer-alt"></i> Your Dashboard
                </h1>';

    if ($result && $result->num_rows > 0) {
        echo '<div class="row">
                <div class="col-md-12">
                    <h3 class="text-secondary mb-3">Your Campaigns</h3>
                </div>';
        
        while ($row = $result->fetch_assoc()) {
            $progress = ($row['Current_Amount'] / $row['Goal']) * 100;
            echo '<div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-dark font-weight-bold">' . htmlspecialchars($row['Name']) . '</h5>
                            <p class="text-muted small">Campaign ID: ' . $row['CID'] . '</p>
                            
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: ' . $progress . '%" 
                                     aria-valuenow="' . $progress . '" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <span class="badge badge-primary p-2">Goal: $' . number_format($row['Goal'], 2) . '</span>
                                <span class="badge badge-success p-2">Raised: $' . number_format($row['Current_Amount'], 2) . '</span>
                            </div>

                            <a href="?page=campaign_detail&cid=' . $row['CID'] . '" class="btn btn-outline-primary btn-block mt-3">
                                <i class="fas fa-eye"></i> View Campaign
                            </a>
                        </div>
                    </div>
                  </div>';
        }
        
        echo '</div>'; // Close row
    } else {
        echo '<div class="alert alert-info text-center p-4">
                <h4 class="text-secondary"><i class="fas fa-folder-open"></i> No Campaigns Found</h4>
                <p>You have not created any campaigns yet. Start your first campaign today!</p>
                <a href="?page=create_campaign" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Create Campaign
                </a>
              </div>';
    }

    echo '  </div>
          </div>';
}
?>
