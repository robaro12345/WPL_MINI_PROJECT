<?php
if (!isset($_SESSION['user']) || (($_SESSION['user']['Role'] ?? '') != 'Admin')) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">Access denied.</div>
          </div>';
    exit;
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST data received in approve_campaign.php: " . print_r($_POST, true));
}

// Check for success/error messages in session
if (isset($_SESSION['approval_message'])) {
    echo '<div class="container mt-5">
            <div class="alert alert-' . $_SESSION['approval_message_type'] . ' text-center">'
                . $_SESSION['approval_message'] .
            '</div>
          </div>';
    // Clear the messages
    unset($_SESSION['approval_message']);
    unset($_SESSION['approval_message_type']);
}

echo '<div class="container mt-5">';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['campaign_id'], $_POST['action'])) {
    $campID = intval($_POST['campaign_id']);
    $action = $_POST['action'];
    $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
    $adminID = intval($_SESSION['user']['UserID']);

    if (empty($comments)) {
        $_SESSION['approval_message'] = 'Comments are required for approval/rejection.';
        $_SESSION['approval_message_type'] = 'danger';
    } else {
        try {
            // Start transaction
            $conn->begin_transaction();

            // Debug information
            error_log("Processing approval - Campaign ID: $campID, Action: $action, Admin ID: $adminID");

            // Verify campaign exists and is in pending state
            $checkCampaign = $conn->prepare("SELECT c.*, COALESCE(u.Fname, o.NAME) as Creator_Name
                                           FROM CAMPAIGN c
                                           LEFT JOIN USERS u ON c.CRID_USER = u.UserID
                                           LEFT JOIN ORGANISATION o ON c.CRID_ORG = o.OrgID
                                           WHERE c.CID = ?");
            $checkCampaign->bind_param("i", $campID);
            $checkCampaign->execute();
            $result = $checkCampaign->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("Campaign not found");
            }

            $campaign = $result->fetch_assoc();

            // Debug information
            error_log("Campaign found - Current Approval Status: " . $campaign['Approval_Status']);

            if ($campaign['Approval_Status'] != 0) {
                throw new Exception("Campaign is not in pending state (Current status: " . $campaign['Approval_Status'] . ")");
            }

            // Prepare statements
            $updateCampaign = $conn->prepare("UPDATE CAMPAIGN SET Approval_Status = ? WHERE CID = ? AND Approval_Status = 0");
            $insertApproval = $conn->prepare("INSERT INTO APPROVE (AdminID, CampaignID, State, Comments, Approval_Date) VALUES (?, ?, ?, ?, NOW())");

            if ($action === 'approve') {
                $status = 1; // 1 = Approved
                $state = 'Approved';
                $_SESSION['approval_message_type'] = 'success';
                $_SESSION['approval_message'] = 'Campaign "' . htmlspecialchars($campaign['Name']) . '" has been approved successfully!';
            } elseif ($action === 'reject') {
                $status = 0; // Keep as 0 but mark as rejected in APPROVE table
                $state = 'Rejected';
                $_SESSION['approval_message_type'] = 'danger';
                $_SESSION['approval_message'] = 'Campaign "' . htmlspecialchars($campaign['Name']) . '" has been rejected.';
            } else {
                throw new Exception("Invalid action");
            }

            // Execute the updates with additional error checking
            $updateCampaign->bind_param("ii", $status, $campID);
            if (!$updateCampaign->execute()) {
                throw new Exception("Failed to update campaign status: " . $updateCampaign->error);
            }

            if ($updateCampaign->affected_rows === 0) {
                throw new Exception("Campaign status was not updated. It may have already been processed.");
            }

            $insertApproval->bind_param("iiss", $adminID, $campID, $state, $comments);
            if (!$insertApproval->execute()) {
                throw new Exception("Failed to record approval action: " . $insertApproval->error);
            }

            $conn->commit();
            error_log("Approval process completed successfully for campaign $campID");

        } catch (Exception $e) {
            $conn->rollback();
            error_log("Approval Error: " . $e->getMessage());
            $_SESSION['approval_message'] = 'Error: ' . htmlspecialchars($e->getMessage());
            $_SESSION['approval_message_type'] = 'danger';
        } finally {
            if (isset($checkCampaign)) $checkCampaign->close();
            if (isset($updateCampaign)) $updateCampaign->close();
            if (isset($insertApproval)) $insertApproval->close();
        }
    }

    // Store the redirect in a session variable to be processed after all output
    $_SESSION['redirect_after_post'] = '?page=approve_campaign';
    // We'll handle the actual redirect at the end of the script
}

// Get pending campaigns with more details (excluding those already rejected)
$query = "SELECT c.CID, c.Name, c.Description, c.Goal, c.Creation_Date, c.Category,
                 COALESCE(u.Fname, o.NAME) as Creator,
                 c.Current_Amount,
                 (SELECT COUNT(*) FROM DONATION d WHERE d.CampaignID = c.CID) as Donor_Count
          FROM CAMPAIGN c
          LEFT JOIN USERS u ON c.CRID_USER = u.UserID
          LEFT JOIN ORGANISATION o ON c.CRID_ORG = o.OrgID
          WHERE c.Approval_Status = 0
          AND NOT EXISTS (
              SELECT 1 FROM APPROVE a
              WHERE a.CampaignID = c.CID
              AND a.State = 'Rejected'
          )
          ORDER BY c.Creation_Date DESC";

$result = $conn->query($query);

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
                                <th>Campaign</th>
                                <th>Category</th>
                                <th>Creator</th>
                                <th>Goal</th>
                                <th>Created</th>
                                <th width="300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>
                    <strong>' . htmlspecialchars($row['Name']) . '</strong>
                    <p class="text-muted mb-0 small">' . htmlspecialchars(substr($row['Description'], 0, 100)) . '...</p>
                </td>
                <td><span class="badge badge-info">' . htmlspecialchars($row['Category'] ?: 'Uncategorized') . '</span></td>
                <td>' . htmlspecialchars($row['Creator']) . '</td>
                <td>$' . number_format($row['Goal'], 2) . '</td>
                <td>' . date('M d, Y', strtotime($row['Creation_Date'])) . '</td>
                <td>
                    <form method="POST" action="?page=approve_campaign" class="approval-form">
                        <input type="hidden" name="campaign_id" value="' . $row['CID'] . '">
                        <div class="form-group">
                            <textarea name="comments" class="form-control mb-2"
                                      placeholder="Add review comments (required)..." required></textarea>
                        </div>
                        <div class="btn-group">
                            <button type="submit" name="action" value="approve"
                                    class="btn btn-success btn-sm">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                            <button type="submit" name="action" value="reject"
                                    class="btn btn-danger btn-sm">
                                <i class="fas fa-times mr-1"></i>Reject
                            </button>
                        </div>
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

// Add some custom styles
echo '<style>
.approval-form textarea {
    resize: vertical;
    min-height: 60px;
}
.badge {
    padding: 0.5em 1em;
}

/* Dark mode specific styles for approval page */
.dark-mode .card-header.bg-primary {
    background-color: #3046eb !important;
}
.dark-mode .table-hover tbody tr:hover {
    background-color: rgba(67,97,238,.1);
}
.dark-mode .approval-form textarea {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e0e0e0;
}
.dark-mode .approval-form textarea:focus {
    background-color: #333;
    border-color: #4361ee;
    color: #fff;
    box-shadow: 0 0 0 0.2rem rgba(67,97,238,.25);
}
</style>';

// Handle redirect after all output
if (isset($_SESSION['redirect_after_post'])) {
    echo '<script>window.location.href = "' . $_SESSION['redirect_after_post'] . '";</script>';
    unset($_SESSION['redirect_after_post']);
}
?>
