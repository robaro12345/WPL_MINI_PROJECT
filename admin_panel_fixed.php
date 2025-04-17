<?php
checkAccess(['Admin']);

echo '<div class="container-fluid mt-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">
                            <i class="fas fa-user-shield mr-2"></i>Admin Dashboard
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Campaign Statistics -->
            <div class="col-md-12">
                <div class="row mb-4">';

                // Get campaign statistics
                $total = $conn->query("SELECT COUNT(*) as total FROM CAMPAIGN")->fetch_assoc()['total'];
                $pending = $conn->query("SELECT COUNT(*) as pending FROM CAMPAIGN WHERE Approval_Status = 0")->fetch_assoc()['pending'];
                $approved = $conn->query("SELECT COUNT(*) as approved FROM CAMPAIGN WHERE Approval_Status = 1")->fetch_assoc()['approved'];

                echo '<div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                                <h3 class="counter">' . $total . '</h3>
                                <p class="text-muted mb-0">Total Campaigns</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                <h3 class="counter">' . $pending . '</h3>
                                <p class="text-muted mb-0">Pending Approval</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h3 class="counter">' . $approved . '</h3>
                                <p class="text-muted mb-0">Approved Campaigns</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Management -->
            <div class="col-md-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-white">
                        <h3 class="mb-0">
                            <i class="fas fa-tasks mr-2"></i>Campaign Management
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">';

                        $result = $conn->query("SELECT c.*,
                                             COALESCE(u.Fname, o.NAME) as Creator,
                                             (SELECT COUNT(*) FROM DONATION d WHERE d.CampaignID = c.CID) as DonationCount
                                             FROM CAMPAIGN c
                                             LEFT JOIN USERS u ON c.CRID_USER = u.UserID
                                             LEFT JOIN ORGANISATION o ON c.CRID_ORG = o.OrgID
                                             ORDER BY c.Creation_Date DESC");

                        if ($result && $result->num_rows > 0) {
                            echo '<table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Campaign</th>
                                            <th>Creator</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                            while ($row = $result->fetch_assoc()) {
                                $progress = ($row['Current_Amount'] / $row['Goal']) * 100;
                                $statusClass = 'warning';
                                $statusText = 'Pending';

                                // Check if there's an approval record for this campaign
                                $approvalQuery = "SELECT State FROM APPROVE WHERE CampaignID = {$row['CID']} ORDER BY Approval_Date DESC LIMIT 1";
                                $approvalResult = $conn->query($approvalQuery);

                                if ($row['Approval_Status'] == 1) {
                                    $statusClass = 'success';
                                    $statusText = 'Approved';
                                } else if ($approvalResult && $approvalResult->num_rows > 0) {
                                    $approvalState = $approvalResult->fetch_assoc()['State'];
                                    if ($approvalState == 'Rejected') {
                                        $statusClass = 'danger';
                                        $statusText = 'Rejected';
                                    }
                                }

                                echo '<tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <h6 class="mb-0">' . htmlspecialchars($row['Name']) . '</h6>
                                                    <small class="text-muted">ID: ' . $row['CID'] . '</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>' . htmlspecialchars($row['Creator']) . '</td>
                                        <td>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar bg-success" style="width: ' . $progress . '%"></div>
                                            </div>
                                            <small class="text-muted">$' . number_format($row['Current_Amount'], 2) . ' of $' . number_format($row['Goal'], 2) . '</small>
                                        </td>
                                        <td><span class="badge badge-' . $statusClass . ' px-3 py-2">' . $statusText . '</span></td>
                                        <td>' . date('M d, Y', strtotime($row['Creation_Date'])) . '</td>
                                        <td>';
                                // Only show review button for pending campaigns that haven't been rejected
                                if (!$row['Approval_Status'] && $statusText !== 'Rejected') {
                                    echo '<button type="button" class="btn btn-primary btn-sm mr-2"
                                                onclick="openReviewModal(' . $row['CID'] . ')">
                                                <i class="fas fa-check-circle mr-1"></i>Review
                                            </button>';
                                }
                                echo '<a href="?page=campaign_detail&cid=' . $row['CID'] . '"
                                         class="btn btn-info btn-sm">
                                         <i class="fas fa-eye mr-1"></i>View
                                     </a>
                                    </td>
                                  </tr>';
                            }

                            echo '</tbody></table>';
                        } else {
                            echo '<div class="text-center p-5">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                    <h4>No Campaigns Found</h4>
                                    <p class="text-muted">There are no campaigns in the system yet.</p>
                                  </div>';
                        }

                        echo '</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Campaign</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="reviewForm" method="POST" action="?page=approve_campaign">
                <div class="modal-body">
                    <input type="hidden" name="campaign_id" id="modalCampaignId">
                    <div class="form-group">
                        <label>Comments</label>
                        <textarea name="comments" class="form-control" rows="3"
                                  placeholder="Add your review comments..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="approve"
                            class="btn btn-success">
                        <i class="fas fa-check mr-1"></i>Approve
                    </button>
                    <button type="submit" name="action" value="reject"
                            class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i>Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.counter {
    font-size: 2.5rem;
    font-weight: 600;
    color: #4361ee;
}
.progress {
    overflow: visible;
}
.card {
    transition: transform 0.2s ease;
}
.card:hover {
    transform: translateY(-5px);
}
.badge {
    font-weight: 500;
}
.modal-content {
    border: none;
    border-radius: 15px;
}
.modal-header {
    border-radius: 15px 15px 0 0;
}

/* Dark mode specific styles for admin panel */
.dark-mode .counter {
    color: #6d8eff;
}
.dark-mode .card-header.bg-primary {
    background-color: #3046eb !important;
}
.dark-mode .card-header.bg-white {
    background-color: #1e1e1e !important;
    color: #e0e0e0;
}
.dark-mode .table-hover tbody tr:hover {
    background-color: rgba(67,97,238,.1);
}
.dark-mode .bg-light {
    background-color: #2d2d2d !important;
}
.dark-mode .table thead th {
    color: #aaa;
}
</style>';
?>

<script>
// Modal implementation with fallback to jQuery if available
function openReviewModal(campaignId) {
    // Set the campaign ID in the hidden input
    document.getElementById("modalCampaignId").value = campaignId;

    // Try to use jQuery if it's available
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
        try {
            jQuery("#reviewModal").modal("show");
            return; // Exit if jQuery works
        } catch (e) {
            console.log("jQuery modal failed, falling back to vanilla JS");
        }
    }

    // Fallback to vanilla JavaScript
    var modal = document.getElementById("reviewModal");
    modal.classList.add("show");
    modal.style.display = "block";
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");

    // Add backdrop
    var backdrop = document.createElement("div");
    backdrop.className = "modal-backdrop fade show";
    document.body.appendChild(backdrop);
}

// Function to close the modal with jQuery fallback
function closeModal() {
    // Try to use jQuery if it's available
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
        try {
            jQuery("#reviewModal").modal("hide");
            return; // Exit if jQuery works
        } catch (e) {
            console.log("jQuery modal hide failed, falling back to vanilla JS");
        }
    }

    // Fallback to vanilla JavaScript
    var modal = document.getElementById("reviewModal");
    modal.classList.remove("show");
    modal.style.display = "none";
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");

    // Remove backdrop
    var backdrop = document.querySelector(".modal-backdrop");
    if (backdrop) {
        document.body.removeChild(backdrop);
    }
}

// Add event listeners when the window is fully loaded (after all scripts)
window.addEventListener("load", function() {
    // Try to use jQuery if it's available
    if (typeof jQuery !== 'undefined') {
        try {
            // Add event listeners using jQuery
            jQuery("[data-dismiss='modal']").on("click", closeModal);

            // Handle form submission
            jQuery("#reviewForm").on("submit", function() {
                // Allow the form to submit normally
                return true;
            });

            // Clear form when modal is closed
            jQuery("#reviewModal").on("hidden.bs.modal", function() {
                document.getElementById("reviewForm").reset();
            });

            console.log("jQuery event listeners attached");
            return; // Exit if jQuery works
        } catch (e) {
            console.log("jQuery event binding failed, falling back to vanilla JS");
        }
    }

    // Fallback to vanilla JavaScript
    // Add event listeners to close buttons
    var closeButtons = document.querySelectorAll("[data-dismiss='modal']");
    for (var i = 0; i < closeButtons.length; i++) {
        closeButtons[i].addEventListener("click", closeModal);
    }

    // Handle form submission
    var form = document.getElementById("reviewForm");
    if (form) {
        form.addEventListener("submit", function() {
            // Allow the form to submit normally
            return true;
        });
    }
});
</script>
