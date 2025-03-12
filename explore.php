<?php 
echo '<div class="container mt-5">
        <h1 class="text-center text-primary mb-4">
            <i class="fas fa-globe"></i> Explore Campaigns
        </h1>';

$result = $conn->query("SELECT * FROM CAMPAIGN");

if ($result && $result->num_rows > 0) {
    echo '<div class="row">';
    
    while ($row = $result->fetch_assoc()) {
        $progress = ($row['Current_Amount'] / $row['Goal']) * 100;

        echo '<div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title text-dark font-weight-bold">' . htmlspecialchars($row['Name']) . '</h5>
                        <p class="text-muted small">Campaign ID: ' . $row['CID'] . '</p>
                        
                        <p class="card-text text-secondary">' . htmlspecialchars(substr($row['Description'], 0, 100)) . '...</p>
                        
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
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
              </div>';
    }
    
    echo '</div>'; // Close row
} else {
    echo '<div class="alert alert-info text-center p-4">
            <h4 class="text-secondary"><i class="fas fa-folder-open"></i> No Campaigns Available</h4>
            <p>There are no active campaigns at the moment. Check back later or start your own!</p>
            <a href="?page=create_campaign" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Start a Campaign
            </a>
          </div>';
}

echo '</div>'; // Close container
?>
