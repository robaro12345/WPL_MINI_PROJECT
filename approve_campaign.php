<?php
if (!isset($_SESSION['user']) || (($_SESSION['user']['Role'] ?? '') != 'Admin')) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">Access denied.</div>
          </div>';
} else {
    echo '<div class="container mt-5">';
    
    if (isset($_GET['campaign'])) {
        $campID = intval($_GET['campaign']);
        $conn->query("UPDATE CAMPAIGN SET Approval_Status = 1 WHERE CID = '$campID'");
        
        echo '<div class="alert alert-success text-center">
                <strong>Campaign approved successfully!</strong><br>
                <a href="?page=admin_panel" class="btn btn-primary mt-3">
                    <i class="fas fa-arrow-left"></i> Back to Admin Panel
                </a>
              </div>';
    } else {
        echo '<div class="alert alert-danger text-center">
                <strong>Invalid campaign.</strong><br>
                <a href="?page=admin_panel" class="btn btn-secondary mt-3">
                    <i class="fas fa-arrow-left"></i> Back to Admin Panel
                </a>
              </div>';
    }
    
    echo '</div>';
}
?>
