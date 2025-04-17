<?php
if (!isset($_GET['cid'])) {
    header('Location: ?page=explore');
    exit;
}

$cid = $_GET['cid'];
$query = "SELECT c.*,
                 COALESCE(u.Fname, o.NAME) as Creator_Name,
                 COALESCE(e.Email, o.Wallet_ID) as Creator_Email,
                 COUNT(DISTINCT d.DonationID) as Donor_Count,
                 SUM(d.Amount) as Total_Raised,
                 (SELECT COUNT(*) FROM MESSAGE WHERE Campaign_ID = c.CID) as Message_Count,
                 (SELECT URL FROM RESOURCES WHERE CampID = c.CID AND TYPE = 'image' LIMIT 1) as Image_URL
          FROM CAMPAIGN c
          LEFT JOIN USERS u ON c.CRID_USER = u.UserID
          LEFT JOIN EMAIL e ON u.UserID = e.UserID AND e.Primary_Email = TRUE
          LEFT JOIN ORGANISATION o ON c.CRID_ORG = o.OrgID
          LEFT JOIN DONATION d ON c.CID = d.CampaignID
          WHERE c.CID = ?
          GROUP BY c.CID";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $cid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">
                <h4>Campaign Not Found</h4>
                <p>The campaign you\'re looking for doesn\'t exist or has been removed.</p>
                <a href="?page=explore" class="btn btn-primary">
                    <i class="fas fa-search mr-2"></i>Explore Campaigns
                </a>
            </div>
          </div>';
    exit;
}

$campaign = $result->fetch_assoc();
$progress = ($campaign['Current_Amount'] / $campaign['Goal']) * 100;
$daysLeft = ceil((strtotime($campaign['End_Date']) - time()) / (60 * 60 * 24));
$isEnded = $daysLeft <= 0;

// Fetch recent donors
$donorsQuery = "SELECT d.*,
                       COALESCE(u.Fname, 'Anonymous') as Donor_Name,
                       COALESCE(r.URL, 'default-avatar.png') as Donor_Image
                FROM DONATION d
                LEFT JOIN USERS u ON d.UserID = u.UserID
                LEFT JOIN RESOURCES r ON u.UserID = r.CampID AND r.TYPE = 'profile'
                WHERE d.CampaignID = ?
                ORDER BY d.Timestamp DESC
                LIMIT 5";
$stmt = $conn->prepare($donorsQuery);
$stmt->bind_param('i', $cid);
$stmt->execute();
$donors = $stmt->get_result();

// Fetch recent messages
$messagesQuery = "SELECT m.*,
                         COALESCE(u.Fname, 'Anonymous') as Sender_Name,
                         COALESCE(r.URL, 'default-avatar.png') as Sender_Image
                  FROM MESSAGE m
                  LEFT JOIN USERS u ON m.User_ID = u.UserID
                  LEFT JOIN RESOURCES r ON u.UserID = r.CampID AND r.TYPE = 'profile'
                  WHERE m.Campaign_ID = ?
                  ORDER BY m.Created_At DESC
                  LIMIT 5";
$stmt = $conn->prepare($messagesQuery);
$stmt->bind_param('i', $cid);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!-- Campaign Header -->
<div class="campaign-header py-5" style="background-image: url('uploads/<?php echo htmlspecialchars($campaign['Image_URL'] ?? 'default-campaign.jpg'); ?>')">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-4">
                        <li class="breadcrumb-item">
                            <a href="?page=explore" class="text-white">Explore</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="?page=explore&category=<?php echo urlencode($campaign['Category']); ?>"
                               class="text-white">
                                <?php echo htmlspecialchars($campaign['Category']); ?>
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Campaign</li>
                    </ol>
                </nav>

                <span class="badge badge-primary px-3 py-2 mb-3">
                    <?php echo htmlspecialchars($campaign['Category']); ?>
                </span>

                <h1 class="display-4 text-white font-weight-bold mb-4">
                    <?php echo htmlspecialchars($campaign['Name']); ?>
                </h1>

                <div class="d-flex align-items-center text-white mb-4">
                    <img src="uploads/<?php echo htmlspecialchars($campaign['Image_URL']); ?>"
                         alt="Creator" class="rounded-circle mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                    <div>
                        <p class="mb-0">Created by <strong><?php echo htmlspecialchars($campaign['Creator_Name']); ?></strong></p>
                        <small>
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <?php echo date('F j, Y', strtotime($campaign['Creation_Date'])); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-n5 mb-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-4">
                    <!-- Campaign Description -->
                    <div class="campaign-description mb-5">
                        <h4 class="mb-4">About this Campaign</h4>
                        <div class="formatted-description">
                            <?php echo nl2br(htmlspecialchars($campaign['Description'])); ?>
                        </div>
                    </div>

                    <!-- Updates Section -->
                    <div class="campaign-updates mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">Recent Updates</h4>
                            <span class="badge badge-light">
                                <?php echo $campaign['Message_Count']; ?> updates
                            </span>
                        </div>

                        <?php if ($messages->num_rows > 0): ?>
                            <div class="timeline">
                                <?php while ($message = $messages->fetch_assoc()): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-content">
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="uploads/<?php echo htmlspecialchars($message['Sender_Image']); ?>"
                                                     alt="<?php echo htmlspecialchars($message['Sender_Name']); ?>"
                                                     class="rounded-circle mr-3"
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0">
                                                        <?php echo htmlspecialchars($message['Sender_Name']); ?>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <?php echo date('M j, Y g:i A', strtotime($message['Created_At'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <p class="mb-0">
                                                <?php echo nl2br(htmlspecialchars($message['Message'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No updates yet</p>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user'])): ?>
                            <form action="?page=post_message" method="POST" class="mt-4">
                                <input type="hidden" name="campaign_id" value="<?php echo $campaign['CID']; ?>">
                                <div class="form-group">
                                    <textarea name="message" class="form-control" rows="3"
                                              placeholder="Leave a message of support..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane mr-2"></i>Post Update
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="campaign-sidebar">
                <!-- Progress Card -->
                <div class="card border-0 shadow-lg mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <h3 class="text-primary mb-0">
                                $<?php echo number_format($campaign['Current_Amount'], 2); ?>
                            </h3>
                            <span class="text-muted">
                                raised of $<?php echo number_format($campaign['Goal'], 2); ?>
                            </span>
                        </div>

                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                 role="progressbar" style="width: <?php echo $progress; ?>%">
                            </div>
                        </div>

                        <div class="campaign-stats d-flex justify-content-between text-center mb-4">
                            <div>
                                <h5 class="mb-0"><?php echo $campaign['Donor_Count']; ?></h5>
                                <small class="text-muted">Donors</small>
                            </div>
                            <div>
                                <h5 class="mb-0"><?php echo round($progress); ?>%</h5>
                                <small class="text-muted">Funded</small>
                            </div>
                            <div>
                                <h5 class="mb-0">
                                    <?php echo $isEnded ? 'Ended' : $daysLeft . ' Days'; ?>
                                </h5>
                                <small class="text-muted">
                                    <?php echo $isEnded ? 'Campaign closed' : 'To go'; ?>
                                </small>
                            </div>
                        </div>

                        <?php if (!$isEnded): ?>
                            <?php if (isset($_SESSION['user'])): ?>
                                <?php if ($_SESSION['user']['Role'] === 'Donor'): ?>
                                    <!-- User is logged in as Donor, show donate modal button -->
                                    <button type="button" class="btn btn-primary btn-lg btn-block mb-3"
                                            data-toggle="modal" data-target="#donateModal">
                                        <i class="fas fa-heart mr-2"></i>Donate Now
                                    </button>
                                <?php else: ?>
                                    <!-- User is logged in but not as Donor (Admin or Campaigner) -->
                                    <div class="alert alert-warning mb-3">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Only users with the Donor role can make donations. Your current role is <strong><?php echo $_SESSION['user']['Role']; ?></strong>.
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- User is not logged in, show login redirect button -->
                                <a href="?page=login" class="btn btn-primary btn-lg btn-block mb-3">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Login to Donate
                                </a>
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    You need to be logged in as a Donor to make a donation.
                                </div>
                            <?php endif; ?>
                            <button type="button" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-share mr-2"></i>Share Campaign
                            </button>
                        <?php else: ?>
                            <div class="alert alert-secondary mb-0">
                                <i class="fas fa-clock mr-2"></i>
                                This campaign has ended
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Donors Card -->
                <div class="card border-0 shadow-lg mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Recent Donors</h5>

                        <?php if ($donors->num_rows > 0): ?>
                            <div class="donors-list">
                                <?php while ($donor = $donors->fetch_assoc()): ?>
                                    <div class="donor-item d-flex align-items-center mb-3">
                                        <img src="uploads/<?php echo htmlspecialchars($donor['Donor_Image']); ?>"
                                             alt="<?php echo htmlspecialchars($donor['Donor_Name']); ?>"
                                             class="rounded-circle mr-3"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0">
                                                <?php echo htmlspecialchars($donor['Donor_Name']); ?>
                                            </h6>
                                            <small class="text-muted">
                                                $<?php echo number_format($donor['Amount'], 2); ?> •
                                                <?php echo date('M j, Y', strtotime($donor['Timestamp'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Be the first to donate!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Campaign Info Card -->
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Campaign Details</h5>

                        <div class="campaign-info">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-calendar-alt fa-fw text-primary mr-3"></i>
                                <div>
                                    <small class="text-muted d-block">Created on</small>
                                    <?php echo date('F j, Y', strtotime($campaign['Creation_Date'])); ?>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-clock fa-fw text-primary mr-3"></i>
                                <div>
                                    <small class="text-muted d-block">End Date</small>
                                    <?php echo date('F j, Y', strtotime($campaign['End_Date'])); ?>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user fa-fw text-primary mr-3"></i>
                                <div>
                                    <small class="text-muted d-block">Campaign Creator</small>
                                    <?php echo htmlspecialchars($campaign['Creator_Name']); ?>
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <i class="fas fa-tag fa-fw text-primary mr-3"></i>
                                <div>
                                    <small class="text-muted d-block">Category</small>
                                    <?php echo htmlspecialchars($campaign['Category']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Donate Modal -->
<?php if (isset($_SESSION['user']) && $_SESSION['user']['Role'] === 'Donor'): ?>
<div class="modal fade" id="donateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Make a Donation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="?page=process_donation" method="POST" id="donationForm">
                    <input type="hidden" name="campaign_id" value="<?php echo $campaign['CID']; ?>">

                    <div class="form-group">
                        <label>Amount (USD)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" name="amount" class="form-control" required
                                   min="1" step="0.01" placeholder="Enter amount">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Message (Optional)</label>
                        <textarea name="message" class="form-control" rows="3"
                                  placeholder="Leave a message of support..."></textarea>
                    </div>

                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="anonymous" name="anonymous">
                        <label class="custom-control-label" for="anonymous">
                            Make this donation anonymous
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-heart mr-2"></i>Complete Donation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.campaign-header {
    background-size: cover;
    background-position: center;
    position: relative;
    color: white;
}
.campaign-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8));
}
.campaign-header .container {
    position: relative;
    z-index: 1;
}
.campaign-sidebar {
    position: sticky;
    top: 20px;
}
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
.timeline-item {
    position: relative;
    padding-bottom: 30px;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -34px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #4361ee;
    border: 2px solid #fff;
}
.formatted-description {
    white-space: pre-line;
    line-height: 1.6;
}
.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}
.progress-bar {
    border-radius: 10px;
}
.donor-item:last-child {
    margin-bottom: 0 !important;
}

/* Dark Mode Styles */
.dark-mode .timeline::before {
    background: #333;
}

.dark-mode .timeline-item::before {
    background: #4361ee;
    border: 2px solid #1e1e1e;
}

.dark-mode .badge-light {
    background-color: #2d2d2d;
    color: #e0e0e0;
}

.dark-mode .text-primary {
    color: #6d8eff !important;
}

.dark-mode .text-muted {
    color: #aaa !important;
}

.dark-mode .formatted-description {
    color: #e0e0e0;
}

.dark-mode .campaign-info i.text-primary {
    color: #6d8eff !important;
}

.dark-mode .modal-content {
    background-color: #1e1e1e;
}

.dark-mode .modal-header {
    border-color: #333;
}

.dark-mode .close {
    color: #e0e0e0;
}

.dark-mode .custom-control-label {
    color: #e0e0e0;
}

.dark-mode .input-group-text {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e0e0e0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Share functionality
    const shareBtn = document.querySelector('.btn-outline-primary');
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo addslashes($campaign['Name']); ?>',
                    text: 'Check out this campaign: <?php echo addslashes($campaign['Name']); ?>',
                    url: window.location.href
                });
            } else {
                // Fallback
                const dummy = document.createElement('input');
                document.body.appendChild(dummy);
                dummy.value = window.location.href;
                dummy.select();
                document.execCommand('copy');
                document.body.removeChild(dummy);
                alert('Campaign link copied to clipboard!');
            }
        });
    }

    // Donation form validation
    const donationForm = document.getElementById('donationForm');
    if (donationForm) {
        donationForm.addEventListener('submit', function(e) {
            const amount = this.querySelector('[name="amount"]').value;
            if (amount <= 0) {
                e.preventDefault();
                alert('Please enter a valid donation amount');
            }
        });
    }
});
</script>
