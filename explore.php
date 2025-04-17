<?php
// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$status = isset($_GET['status']) ? $_GET['status'] : 'active';

// Build query to handle both old and new image paths
$query = "SELECT c.*,
                 COALESCE(u.Fname, o.NAME) as Creator_Name,
                 COUNT(DISTINCT d.DonationID) as Donor_Count,
                 r.URL as Image_URL
          FROM CAMPAIGN c
          LEFT JOIN USERS u ON c.CRID_USER = u.UserID
          LEFT JOIN ORGANISATION o ON c.CRID_ORG = o.OrgID
          LEFT JOIN DONATION d ON c.CID = d.CampaignID
          LEFT JOIN RESOURCES r ON c.CID = r.CampID AND r.TYPE = 'image'
          WHERE c.Approval_Status = 1";

// Apply filters
if ($search) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (c.Name LIKE '%$search%' OR c.Description LIKE '%$search%')";
}

if ($category !== 'all') {
    $category = $conn->real_escape_string($category);
    $query .= " AND c.Category = '$category'";
}

if ($status === 'active') {
    $query .= " AND c.End_Date > NOW()";
} elseif ($status === 'completed') {
    $query .= " AND c.End_Date <= NOW()";
}

$query .= " GROUP BY c.CID";

// Apply sorting
switch ($sort) {
    case 'most_funded':
        $query .= " ORDER BY c.Current_Amount DESC";
        break;
    case 'most_donors':
        $query .= " ORDER BY Donor_Count DESC";
        break;
    case 'ending_soon':
        $query .= " ORDER BY c.End_Date ASC";
        break;
    case 'latest':
    default:
        $query .= " ORDER BY c.Creation_Date DESC";
}

$result = $conn->query($query);
?>

<!-- Explore Header -->
<div class="explore-header bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 font-weight-bold mb-4">Explore Campaigns</h1>
                <p class="lead mb-4">Discover meaningful causes and make a difference in your community</p>

                <!-- Search Form -->
                <form method="GET" action="" class="search-form mb-0">
                    <input type="hidden" name="page" value="explore">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-lg"
                               placeholder="Search campaigns..." value="<?php echo htmlspecialchars($search); ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-light px-4">
                                <i class="fas fa-search mr-2"></i>Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="container mt-n5 mb-5">
    <div class="card border-0 shadow-lg">
        <div class="card-body p-4">
            <form method="GET" action="" class="row align-items-end">
                <input type="hidden" name="page" value="explore">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Sort By</label>
                    <select name="sort" class="form-control" onchange="this.form.submit()">
                        <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>Latest</option>
                        <option value="most_funded" <?php echo $sort === 'most_funded' ? 'selected' : ''; ?>>Most Funded</option>
                        <option value="most_donors" <?php echo $sort === 'most_donors' ? 'selected' : ''; ?>>Most Donors</option>
                        <option value="ending_soon" <?php echo $sort === 'ending_soon' ? 'selected' : ''; ?>>Ending Soon</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Category</label>
                    <select name="category" class="form-control" onchange="this.form.submit()">
                        <option value="all" <?php echo $category === 'all' ? 'selected' : ''; ?>>All Categories</option>
                        <option value="Education" <?php echo $category === 'Education' ? 'selected' : ''; ?>>Education</option>
                        <option value="Healthcare" <?php echo $category === 'Healthcare' ? 'selected' : ''; ?>>Healthcare</option>
                        <option value="Environment" <?php echo $category === 'Environment' ? 'selected' : ''; ?>>Environment</option>
                        <option value="Community" <?php echo $category === 'Community' ? 'selected' : ''; ?>>Community</option>
                        <option value="Technology" <?php echo $category === 'Technology' ? 'selected' : ''; ?>>Technology</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Status</label>
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-block" onclick="resetFilters()">
                        <i class="fas fa-undo mr-2"></i>Reset Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Campaigns Grid -->
<div class="container mb-5">
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row">
            <?php while ($campaign = $result->fetch_assoc()):
                $progress = ($campaign['Current_Amount'] / $campaign['Goal']) * 100;
                $daysLeft = ceil((strtotime($campaign['End_Date']) - time()) / (60 * 60 * 24));
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card campaign-card border-0 shadow-sm h-100">
                        <?php if (!empty($campaign['Image_URL'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($campaign['Image_URL']); ?>"
                                 class="card-img-top campaign-img" alt="Campaign Image">
                        <?php else: ?>
                            <div class="card-img-top campaign-img d-flex align-items-center justify-content-center bg-light">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge badge-primary px-3 py-2">
                                    <?php echo htmlspecialchars($campaign['Category']); ?>
                                </span>
                                <span class="text-muted">
                                    <i class="fas fa-clock mr-1"></i>
                                    <?php echo $daysLeft > 0 ? $daysLeft . ' days left' : 'Ended'; ?>
                                </span>
                            </div>

                            <h5 class="card-title mb-3">
                                <a href="?page=campaign_detail&cid=<?php echo $campaign['CID']; ?>"
                                   class="text-dark text-decoration-none campaign-title">
                                    <?php echo htmlspecialchars($campaign['Name']); ?>
                                </a>
                            </h5>

                            <p class="text-muted campaign-description">
                                <?php echo substr(htmlspecialchars($campaign['Description']), 0, 100) . '...'; ?>
                            </p>

                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                     role="progressbar" style="width: <?php echo $progress; ?>%">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between text-muted mb-3">
                                <span>
                                    <i class="fas fa-chart-pie mr-1"></i>
                                    <?php echo round($progress); ?>% funded
                                </span>
                                <span>
                                    <i class="fas fa-user-friends mr-1"></i>
                                    <?php echo $campaign['Donor_Count']; ?> donors
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-primary">
                                        $<?php echo number_format($campaign['Current_Amount'], 2); ?>
                                    </strong>
                                    <small class="text-muted">
                                        raised of $<?php echo number_format($campaign['Goal'], 2); ?>
                                    </small>
                                </div>
                                <a href="?page=campaign_detail&cid=<?php echo $campaign['CID']; ?>"
                                   class="btn btn-outline-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search fa-3x text-muted"></i>
            </div>
            <h3>No campaigns found</h3>
            <p class="text-muted">Try adjusting your search or filters to find what you're looking for.</p>
            <button onclick="resetFilters()" class="btn btn-primary">
                <i class="fas fa-undo mr-2"></i>Reset Filters
            </button>
        </div>
    <?php endif; ?>
</div>

<style>
.explore-header {
    background: linear-gradient(135deg, #4361ee 0%, #3046eb 100%);
    position: relative;
}
.search-form .form-control {
    border: none;
    padding: 1.2rem;
    font-size: 1.1rem;
}
.search-form .btn {
    padding-left: 2rem;
    padding-right: 2rem;
}
.campaign-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.campaign-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.campaign-img {
    height: 200px;
    object-fit: cover;
}
.campaign-title {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 2.4em;
}
.campaign-description {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 4.5em;
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
    font-size: 0.85rem;
}
.form-control {
    height: auto;
    padding: 0.5rem 1rem;
}
select.form-control {
    padding-right: 2rem;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}
.col-md-3 {
    min-width: 200px;
}

/* Dark mode specific styles for explore page */
.dark-mode .explore-header {
    background: linear-gradient(135deg, #3046eb 0%, #1e2a8f 100%);
}
.dark-mode .campaign-card {
    background-color: #1e1e1e;
    border-color: #333;
}
.dark-mode .campaign-card:hover {
    box-shadow: 0 10px 20px rgba(0,0,0,0.3) !important;
}
.dark-mode .campaign-img.d-flex {
    background-color: #2d2d2d !important;
}
.dark-mode .campaign-title a {
    color: #e0e0e0;
    text-decoration: none;
}
.dark-mode .campaign-title a:hover {
    color: #4361ee;
}
.dark-mode .btn-outline-primary {
    color: #4361ee;
    border-color: #4361ee;
}
.dark-mode .btn-outline-primary:hover {
    background-color: #4361ee;
    color: #fff;
}
.dark-mode .btn-outline-secondary {
    color: #aaa;
    border-color: #555;
}
.dark-mode .btn-outline-secondary:hover {
    background-color: #333;
    color: #fff;
}

/* Additional dark mode fixes */
.dark-mode .bg-light {
    background-color: #1e1e1e !important;
}

.dark-mode .card {
    background-color: #2d2d2d;
}

.dark-mode .card-img-top.campaign-img.d-flex {
    background-color: #333 !important;
}

.dark-mode .card-img-top.campaign-img.d-flex i {
    color: #555;
}

.dark-mode .text-dark {
    color: #e0e0e0 !important;
}

.dark-mode h2,
.dark-mode h3,
.dark-mode h4,
.dark-mode h5,
.dark-mode h6 {
    color: #e0e0e0;
}

.dark-mode label.text-muted {
    color: #aaa !important;
}
</style>

<script>
function resetFilters() {
    window.location.href = '?page=explore';
}
</script>
