<?php
// Check user access and determine role
if (!isset($_SESSION['user'])) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                <h4>Access Denied</h4>
                <p>Please login to access your dashboard.</p>
            </div>
          </div>';
    exit;
} elseif ($_SESSION['user']['Role'] === 'Admin') {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                <h4>Access Denied</h4>
                <p>Admins cannot access the dashboard. Please use the admin panel instead.</p>
            </div>
          </div>';
    exit;
}

$userID = $_SESSION['user']['UserID'];
$isDonor = $_SESSION['user']['Role'] === 'Donor';

// Common queries for all users
$donationsQuery = "SELECT d.*, c.Name as CampaignName, c.Description as CampaignDesc,
                   c.Current_Amount, c.Goal, c.End_Date
                   FROM DONATION d
                   JOIN CAMPAIGN c ON d.CampaignID = c.CID
                   WHERE d.UserID = ?
                   ORDER BY d.Timestamp DESC";

$stmt = $conn->prepare($donationsQuery);
$stmt->bind_param("i", $userID);
$stmt->execute();
$donations = $stmt->get_result();

// Calculate common statistics
$totalDonated = 0;
$donationCount = $donations->num_rows;
$campaignsSupported = array();

while($row = $donations->fetch_assoc()) {
    $totalDonated += $row['Amount'];
    $campaignsSupported[$row['CampaignID']] = true;
    // Store for later use
    $donationData[] = $row;
}
$uniqueCampaignsCount = count($campaignsSupported);

// Reset pointer for use in the HTML
$donations->data_seek(0);

// Donor-specific queries
if ($isDonor) {
    // Get causes/categories user has donated to
    $causeStatsQuery = "SELECT c.Category as CategoryName,
                            COUNT(d.DonationID) as DonationCount,
                            SUM(d.Amount) as TotalAmount
                        FROM DONATION d
                        JOIN CAMPAIGN c ON d.CampaignID = c.CID
                        WHERE d.UserID = ?
                        GROUP BY c.Category
                        ORDER BY TotalAmount DESC";

    // Get recommended campaigns based on user's donation history
    $recommendedQuery = "SELECT c.*, c.Category as CategoryName
                            FROM CAMPAIGN c
                            WHERE c.Approval_Status = 1
                            AND c.End_Date > NOW()
                            AND c.CID NOT IN (SELECT CampaignID FROM DONATION WHERE UserID = ?)
                            AND c.Category IN (
                                SELECT c2.Category
                                FROM DONATION d
                                JOIN CAMPAIGN c2 ON d.CampaignID = c2.CID
                                WHERE d.UserID = ?
                                GROUP BY c2.Category
                            )
                            LIMIT 4;
                            ";

    $stmt = $conn->prepare($causeStatsQuery);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $causeStats = $stmt->get_result();

    $stmt = $conn->prepare($recommendedQuery);
    $stmt->bind_param("ii", $userID, $userID);
    $stmt->execute();
    $recommendedCampaigns = $stmt->get_result();

    // Get donation history by month for chart
    $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
    $monthlyDonationsQuery = "SELECT DATE_FORMAT(Timestamp, '%Y-%m') as Month,
                             SUM(Amount) as Total
                             FROM DONATION
                             WHERE UserID = ? AND Timestamp >= ?
                             GROUP BY DATE_FORMAT(Timestamp, '%Y-%m')
                             ORDER BY Month ASC";

    $stmt = $conn->prepare($monthlyDonationsQuery);
    $stmt->bind_param("is", $userID, $sixMonthsAgo);
    $stmt->execute();
    $monthlyDonations = $stmt->get_result();

    $monthlyData = array();
    while($row = $monthlyDonations->fetch_assoc()) {
        $monthlyData[$row['Month']] = $row['Total'];
    }

    // Format for chart
    $chartLabels = array();
    $chartData = array();

    // Get last 6 months
    for($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthName = date('M Y', strtotime("-$i months"));
        $chartLabels[] = $monthName;
        $chartData[] = isset($monthlyData[$month]) ? $monthlyData[$month] : 0;
    }

    $chartLabelsJson = json_encode($chartLabels);
    $chartDataJson = json_encode($chartData);
} else {
    // Campaign Creator queries
    $campaignsQuery = "SELECT * FROM CAMPAIGN WHERE CRID_USER = ? ORDER BY Creation_Date DESC";

    $stmt = $conn->prepare($campaignsQuery);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $campaigns = $stmt->get_result();

    $campaignsCreated = $campaigns->num_rows;
}
?>

<div class="container-fluid mt-4">
    <!-- Welcome Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white shadow border-0">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white text-primary p-3 mr-4">
                            <i class="fas <?php echo $isDonor ? 'fa-heart' : 'fa-user'; ?> fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['Fname']); ?>!</h2>
                            <p class="mb-0">
                                <?php echo $isDonor ?
                                    "Thank you for your generosity and support. Your donations are making a difference." :
                                    "Here's what's happening with your campaigns and donations."; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="fas <?php echo $isDonor ? 'fa-hand-holding-usd' : 'fa-hand-holding-heart'; ?> fa-3x <?php echo $isDonor ? 'text-success' : 'text-primary'; ?> mb-3"></i>
                    <h3 class="display-4">$<?php echo number_format($totalDonated, 2); ?></h3>
                    <p class="text-muted mb-0"><?php echo $isDonor ? 'Total Amount Donated' : 'Total Donations Made'; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="fas <?php echo $isDonor ? 'fa-project-diagram' : 'fa-bullhorn'; ?> fa-3x <?php echo $isDonor ? 'text-primary' : 'text-success'; ?> mb-3"></i>
                    <h3 class="display-4"><?php echo $isDonor ? $uniqueCampaignsCount : $campaignsCreated; ?></h3>
                    <p class="text-muted mb-0"><?php echo $isDonor ? 'Campaigns Supported' : 'Campaigns Created'; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-donate fa-3x text-info mb-3"></i>
                    <h3 class="display-4"><?php echo $donationCount; ?></h3>
                    <p class="text-muted mb-0">Total Donations Made</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php if ($isDonor): ?>
            <!-- DONOR VIEW: Left Column (Chart + Recent Donations) -->
            <div class="col-lg-8">
                <!-- Donation History Chart -->
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-line mr-2"></i>Your Donation History
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="donation-chart-container">
                            <canvas id="donationChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Donations with Impact -->
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">
                            <i class="fas fa-history mr-2"></i>Recent Donations
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <?php if ($donations->num_rows > 0): ?>
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Campaign</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Campaign Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 0;
                                        while ($donation = $donations->fetch_assoc()):
                                            if ($count >= 5) break; // Show only 5 recent donations
                                            $count++;
                                            $progress = ($donation['Current_Amount'] / $donation['Goal']) * 100;
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($donation['CampaignName']); ?></h6>
                                                        <small class="text-muted"><?php echo substr(htmlspecialchars($donation['CampaignDesc']), 0, 60) . '...'; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span><?php echo date('M d, Y', strtotime($donation['Timestamp'])); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-success px-3 py-2">$<?php echo number_format($donation['Amount'], 2); ?></span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 5px; width: 100%;">
                                                    <div class="progress-bar bg-primary" style="width: <?php echo $progress; ?>%"></div>
                                                </div>
                                                <small class="text-muted"><?php echo number_format($progress, 1); ?>% Complete</small>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center p-5">
                                    <i class="fas fa-hand-holding-heart fa-3x text-muted mb-3"></i>
                                    <h4>No Donations Yet</h4>
                                    <p class="text-muted">Browse campaigns and make your first donation today!</p>
                                    <a href="?page=browse_campaigns" class="btn btn-primary">
                                        <i class="fas fa-search mr-2"></i>Browse Campaigns
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($donations->num_rows > 5): ?>
                    <div class="card-footer bg-white text-center">
                        <a href="?page=donation_history" class="btn btn-outline-primary">View All Donations</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- DONOR VIEW: Right Column (Causes + Recommendations) -->
            <div class="col-lg-4">
                <!-- Causes You Support -->
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">
                            <i class="fas fa-tags mr-2"></i>Causes You Support
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($causeStats->num_rows > 0): ?>
                            <?php while ($cause = $causeStats->fetch_assoc()): ?>
                                <div class="cause-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($cause['CategoryName']); ?></h6>
                                        <span class="badge badge-info px-3 py-2">$<?php echo number_format($cause['TotalAmount'], 2); ?></span>
                                    </div>
                                    <small class="text-muted"><?php echo $cause['DonationCount']; ?> donation(s)</small>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-info" style="width: 100%"></div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center p-4">
                                <i class="fas fa-tag fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No categories supported yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recommended Campaigns -->
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">
                            <i class="fas fa-lightbulb mr-2"></i>Recommended For You
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($recommendedCampaigns && $recommendedCampaigns->num_rows > 0): ?>
                            <div class="recommended-campaigns">
                                <?php while ($campaign = $recommendedCampaigns->fetch_assoc()): ?>
                                    <?php
                                    $progress = ($campaign['Current_Amount'] / $campaign['Goal']) * 100;
                                    $daysLeft = ceil((strtotime($campaign['End_Date']) - time()) / (60 * 60 * 24));
                                    ?>
                                    <div class="recommended-item mb-4">
                                        <div class="mb-2">
                                            <!-- Using a placeholder for campaign image -->
                                            <div class="campaign-thumbnail-placeholder rounded bg-light text-center">
                                                <i class="fas fa-image text-muted mt-4"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($campaign['Name']); ?></h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge badge-light"><?php echo htmlspecialchars($campaign['CategoryName']); ?></span>
                                            <small class="text-danger"><?php echo $daysLeft; ?> days left</small>
                                        </div>
                                        <div class="progress mb-2" style="height: 5px;">
                                            <div class="progress-bar bg-success" style="width: <?php echo $progress; ?>%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted"><?php echo number_format($progress, 1); ?>% of $<?php echo number_format($campaign['Goal'], 0); ?></small>
                                            <small class="text-muted">$<?php echo number_format($campaign['Current_Amount'], 0); ?> raised</small>
                                        </div>
                                        <a href="?page=campaign_detail&cid=<?php echo $campaign['CID']; ?>" class="btn btn-sm btn-outline-primary btn-block">View Campaign</a>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-4">
                                <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Make a donation to see personalized recommendations!</p>
                                <a href="?page=explore" class="btn btn-primary">
                                    <i class="fas fa-search mr-2"></i>Browse Campaigns
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- CAMPAIGN CREATOR VIEW: Left Column (Your Campaigns) -->
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="fas fa-bullhorn mr-2"></i>Your Campaigns
                            </h3>
                            <a href="?page=create_campaign" class="btn btn-primary">
                                <i class="fas fa-plus-circle mr-2"></i>Create Campaign
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <?php if ($campaigns->num_rows > 0) : ?>
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Campaign</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($campaign = $campaigns->fetch_assoc()): ?>
                                            <?php
                                            $progress = ($campaign['Current_Amount'] / $campaign['Goal']) * 100;
                                            $statusClass = 'warning';
                                            $statusText = 'Pending';
                                            if ($campaign['Approval_Status'] == 1) {
                                                $statusClass = 'success';
                                                $statusText = 'Approved';
                                            } else if ($campaign['Approval_Status'] == 2) {
                                                $statusClass = 'danger';
                                                $statusText = 'Rejected';
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h6 class="mb-0"><?php echo htmlspecialchars($campaign['Name']); ?></h6>
                                                            <small class="text-muted">Created: <?php echo date('M d, Y', strtotime($campaign['Creation_Date'])); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 5px; width: 150px;">
                                                        <div class="progress-bar bg-success" style="width: <?php echo $progress; ?>%"></div>
                                                    </div>
                                                    <small class="text-muted"><?php echo number_format($progress, 1); ?>% Complete</small>
                                                </td>
                                                <td><span class="badge badge-<?php echo $statusClass; ?> px-3 py-2"><?php echo $statusText; ?></span></td>
                                                <td>
                                                    <a href="?page=campaign_detail&cid=<?php echo $campaign['CID']; ?>" class="btn btn-info btn-sm mr-2">
                                                        <i class="fas fa-eye mr-1"></i>View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center p-5">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                    <h4>No Campaigns Yet</h4>
                                    <p class="text-muted">Start your first campaign today!</p>
                                    <a href="?page=create_campaign" class="btn btn-primary">
                                        <i class="fas fa-plus-circle mr-2"></i>Create Campaign
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAMPAIGN CREATOR VIEW: Right Column (Recent Donations) -->
            <div class="col-lg-4">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-white">
                        <h3 class="mb-0">
                            <i class="fas fa-history mr-2"></i>Recent Donations
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="donation-list p-3">
                            <?php if ($donations->num_rows > 0): ?>
                                <?php while ($donation = $donations->fetch_assoc()): ?>
                                    <div class="donation-item mb-3 p-3 bg-light rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($donation['CampaignName']); ?></h6>
                                            <span class="badge badge-success">$<?php echo number_format($donation['Amount'], 2); ?></span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock mr-1"></i><?php echo date('M d, Y H:i', strtotime($donation['Timestamp'])); ?>
                                        </small>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center p-4">
                                    <i class="fas fa-hand-holding-heart fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No donations made yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<?php if ($isDonor): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Donation history chart
    var ctx = document.getElementById('donationChart').getContext('2d');
    var donationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $chartLabelsJson; ?>,
            datasets: [{
                label: 'Monthly Donations ($)',
                data: <?php echo $chartDataJson; ?>,
                backgroundColor: 'rgba(67, 97, 238, 0.5)',
                borderColor: '#4361ee',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return '$' + value;
                        }
                    },
                    gridLines: {
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            },
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        return '$' + Number(tooltipItem.yLabel).toFixed(2);
                    }
                }
            }
        }
    });
});
</script>
<?php endif; ?>

<style>
.card {
    border-radius: 15px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}
.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}
.progress-bar {
    border-radius: 10px;
}
.badge {
    font-weight: 500;
    border-radius: 30px;
}
.table td {
    vertical-align: middle;
}
.donation-chart-container {
    position: relative;
    height: 300px;
}
.cause-item {
    padding: 15px;
    border-radius: 10px;
    background-color: #f8f9fa;
    transition: transform 0.2s ease;
}
.cause-item:hover {
    transform: translateX(5px);
    background-color: #f1f3fa;
}
.recommended-item {
    padding: 15px;
    border-radius: 10px;
    background-color: #f8f9fa;
    transition: transform 0.2s ease;
}
.recommended-item:hover {
    transform: translateY(-5px);
    background-color: #f1f3fa;
}
.campaign-thumbnail, .campaign-thumbnail-placeholder {
    height: 120px;
    width: 100%;
    object-fit: cover;
}
.campaign-thumbnail-placeholder {
    display: flex;
    justify-content: center;
    align-items: center;
}
.btn-outline-primary, .btn-primary {
    border-radius: 30px;
}
.donation-item {
    transition: transform 0.2s ease;
    border-left: 4px solid #4361ee;
}
.donation-item:hover {
    transform: translateX(5px);
}
.donation-list {
    max-height: 600px;
    overflow-y: auto;
}
.donation-list::-webkit-scrollbar {
    width: 6px;
}
.donation-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.donation-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

/* Dark Mode Styles */
.dark-mode .card-header.bg-white {
    background-color: #1e1e1e !important;
    color: #e0e0e0;
}

.dark-mode .bg-light {
    background-color: #2d2d2d !important;
}

.dark-mode .text-muted {
    color: #aaa !important;
}

.dark-mode .cause-item,
.dark-mode .recommended-item,
.dark-mode .donation-item {
    background-color: #2d2d2d;
}

.dark-mode .cause-item:hover,
.dark-mode .recommended-item:hover {
    background-color: #333;
}

.dark-mode .campaign-thumbnail-placeholder {
    background-color: #333 !important;
}

.dark-mode .campaign-thumbnail-placeholder i {
    color: #555;
}

.dark-mode .table thead.bg-light {
    background-color: #2d2d2d !important;
}

.dark-mode .table thead th {
    color: #aaa;
}

.dark-mode .donation-list::-webkit-scrollbar-track {
    background: #2d2d2d;
}

.dark-mode .donation-list::-webkit-scrollbar-thumb {
    background: #555;
}

/* Chart.js dark mode adjustments */
.dark-mode .chart-container canvas {
    filter: invert(0.8) hue-rotate(180deg);
}
</style>