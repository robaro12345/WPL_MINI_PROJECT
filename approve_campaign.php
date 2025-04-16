<?php
if (!isset($_SESSION['user']) || (($_SESSION['user']['Role'] ?? '') != 'Admin')) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">Access denied.</div>
          </div>';
    exit;
}

echo '<div class="container mt-5">';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['campaign_id'], $_POST['action'])) {
    $campID = intval($_POST['campaign_id']);
    $action = $_POST['action'];
    $comments = $_POST['comments'] ?? '';
    $adminID = intval($_SESSION['user']['UserID']);
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Verify campaign exists and is in pending state
        $checkCampaign = $conn->prepare("SELECT Approval_Status FROM CAMPAIGN WHERE CID = ?");
        $checkCampaign->bind_param("i", $campID);
        $checkCampaign->execute();
        $result = $checkCampaign->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Campaign not found");
        }
        
        $campaignStatus = $result->fetch_assoc()['Approval_Status'];
        if ($campaignStatus != 0) {
            throw new Exception("Campaign is not in pending state");
        }
        
        // Prepare statements
        $updateCampaign = $conn->prepare("UPDATE CAMPAIGN SET Approval_Status = ? WHERE CID = ?");
        $insertApproval = $conn->prepare("INSERT INTO APPROVE (AdminID, CampaignID, State, Comments, Approval_Date) VALUES (?, ?, ?, ?, NOW())");
        
        if ($action === 'approve') {
            $status = 1;
            $state = 'Approved';
            $alertClass = 'success';
            $message = 'Campaign approved successfully!';
        } elseif ($action === 'reject') {
            $status = 2;
            $state = 'Rejected';
            $alertClass = 'danger';
            $message = 'Campaign rejected successfully!';
        } else {
            throw new Exception("Invalid action");
        }
        
        // Execute the updates
        $updateCampaign->bind_param("ii", $status, $campID);
        if (!$updateCampaign->execute()) {
            throw new Exception("Failed to update campaign status");
        }
        
        $insertApproval->bind_param("iiss", $adminID, $campID, $state, $comments);
        if (!$insertApproval->execute()) {
            throw new Exception("Failed to record approval action");
        }
        
        $conn->commit();
        echo '<div class="alert alert-' . $alertClass . ' text-center">' . $message . '</div>';
        
    } catch (Exception $e) {
        $conn->rollback();
        echo '<div class="alert alert-danger text-center">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    } finally {
        if (isset($checkCampaign)) $checkCampaign->close();
        if (isset($updateCampaign)) $updateCampaign->close();
        if (isset($insertApproval)) $insertApproval->close();
    }
}

// Get pending campaigns with more details
$result = $conn->query("SELECT c.CID, c.Name, c.Description, c.Goal, c.Creation_Date, 
                              COALESCE(u.Fname, o.NAME) as Creator
                       FROM CAMPAIGN c 
                       LEFT JOIN USERS u ON c.CRID_USER = u.UserID
                       LEFT JOIN ORGANISATION o ON c.CRID_ORG = o.OrgID
                       WHERE c.Approval_Status = 0
                       ORDER BY c.Creation_Date DESC");

if ($result && $result->num_rows > 0) {
    echo '<div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-tasks mr-2"></i>Pending Campaigns</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Campaign Name</th>
                                <th>Creator</th>
                                <th>Description</th>
                                <th>Goal</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . htmlspecialchars($row['Name']) . '</td>
                <td>' . htmlspecialchars($row['Creator']) . '</td>
                <td>' . htmlspecialchars(substr($row['Description'], 0, 100)) . '...</td>
                <td>$' . number_format($row['Goal'], 2) . '</td>
                <td>' . date('M d, Y', strtotime($row['Creation_Date'])) . '</td>
                <td>
                    <form method="POST" class="approval-form">
                        <input type="hidden" name="campaign_id" value="' . $row['CID'] . '">
                        <div class="form-group">
                            <textarea name="comments" class="form-control mb-2" 
                                      placeholder="Add review comments..." required></textarea>
                        </div>
                        <button type="submit" name="action" value="approve" 
                                class="btn btn-success btn-sm mr-1">
                            <i class="fas fa-check mr-1"></i>Approve
                        </button>
                        <button type="submit" name="action" value="reject" 
                                class="btn btn-danger btn-sm">
                            <i class="fas fa-times mr-1"></i>Reject
                        </button>
                    </form>
                </td>
            </tr>';
    }

    echo '</tbody></table>
        </div>
    </div>
</div>';
} else {
    echo '<div class="alert alert-info text-center">
            <i class="fas fa-info-circle mr-2"></i>No pending campaigns found.
          </div>';
}

echo '</div>';
?>
