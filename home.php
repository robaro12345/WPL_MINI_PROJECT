<?php
// Get recent campaigns
$recentCampaigns = $conn->query("SELECT * FROM CAMPAIGN WHERE Approval_Status = 1 ORDER BY Creation_Date DESC LIMIT 3");

// Hero Section
?>

<div class="hero-section text-white position-relative overflow-hidden">
    <div class="hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6">
                <h1 class="display-4 font-weight-bold mb-4">
                    Make a Difference with Your Community
                </h1>
                <p class="lead mb-4" style="color: #7ed7ff;">
                    Join our platform to support meaningful causes and create positive change.
                    Start a campaign or contribute to existing ones today.
                </p>
                <div class="d-flex flex-wrap">
                    <a href="?page=explore" class="btn btn-primary btn-lg mr-3 mb-3">
                        <i class="fas fa-search mr-2"></i>Explore Campaigns
                    </a>
                    <?php if (!isset($_SESSION['user'])): ?>
                        <a href="?page=login" class="btn btn-outline-light btn-lg mb-3">
                            <i class="fas fa-user-plus mr-2"></i>Join Now
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     class="img-fluid rounded-lg shadow-lg" alt="Community Support">
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="container">
    <div class="row justify-content-center mt-n5">
        <div class="col-md-10">
            <div class="card border-0 shadow-lg">
                <div class="card-body py-4">
                    <div class="row text-center">
                        <?php
                        $totalCampaigns = $conn->query("SELECT COUNT(*) as total FROM CAMPAIGN WHERE Approval_Status = 1")->fetch_assoc()['total'];
                        $totalDonations = $conn->query("SELECT SUM(Amount) as total FROM DONATION")->fetch_assoc()['total'];
                        $totalDonors = $conn->query("SELECT COUNT(DISTINCT UserID) as total FROM DONATION")->fetch_assoc()['total'];
                        ?>
                        <div class="col-md-4 stat-item">
                            <i class="fas fa-bullhorn fa-2x text-primary mb-3"></i>
                            <h3 class="font-weight-bold"><?php echo number_format($totalCampaigns); ?></h3>
                            <p class="text-muted mb-0">Active Campaigns</p>
                        </div>
                        <div class="col-md-4 stat-item">
                            <i class="fas fa-hand-holding-heart fa-2x text-success mb-3"></i>
                            <h3 class="font-weight-bold">$<?php echo number_format($totalDonations, 2); ?></h3>
                            <p class="text-muted mb-0">Total Donations</p>
                        </div>
                        <div class="col-md-4 stat-item">
                            <i class="fas fa-users fa-2x text-info mb-3"></i>
                            <h3 class="font-weight-bold"><?php echo number_format($totalDonors); ?></h3>
                            <p class="text-muted mb-0">Community Members</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="font-weight-bold">Why Choose Give Well?</h2>
            <p class="text-muted">Discover the benefits of our community donation platform</p>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary text-white rounded-circle mb-3">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Secure Transactions</h4>
                        <p class="text-muted mb-0">All donations are processed securely through blockchain technology.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success text-white rounded-circle mb-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Real-time Progress</h4>
                        <p class="text-muted mb-0">Track campaign progress and impact in real-time.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-info text-white rounded-circle mb-3">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <h4>Community Driven</h4>
                        <p class="text-muted mb-0">Connect with like-minded individuals and make a difference together.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Campaigns Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold mb-0">Recent Campaigns</h2>
            <a href="?page=explore" class="btn btn-primary">
                View All <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        <div class="row">
            <?php if ($recentCampaigns && $recentCampaigns->num_rows > 0): ?>
                <?php while ($campaign = $recentCampaigns->fetch_assoc()):
                    $progress = ($campaign['Current_Amount'] / $campaign['Goal']) * 100;
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm hover-card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <?php echo htmlspecialchars($campaign['Name']); ?>
                                </h5>
                                <p class="text-muted">
                                    <?php echo substr(htmlspecialchars($campaign['Description']), 0, 100) . '...'; ?>
                                </p>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                         role="progressbar" style="width: <?php echo $progress; ?>%">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-muted mb-3">
                                    <span>
                                        <i class="fas fa-chart-pie mr-1"></i><?php echo round($progress); ?>%
                                    </span>
                                    <span>
                                        <i class="fas fa-dollar-sign mr-1"></i><?php echo number_format($campaign['Current_Amount'], 2); ?> raised
                                    </span>
                                </div>
                                <a href="?page=campaign_detail&cid=<?php echo $campaign['CID']; ?>"
                                   class="btn btn-outline-primary btn-block">
                                    Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No campaigns available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Testimonials -->
<div class="testimonials">
    <div class="container">
        <h2 class="text-center mb-4">💬 What Our Donors Say</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="testimonial text-center">
                    <p>"Give Well made it so easy to support a cause I care about. The process was smooth, and I love seeing the impact of my donations!"</p>
                    <h5>– Sarah M.</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial text-center">
                    <p>"A fantastic platform! I was able to quickly raise funds for a community project. Thank you for empowering changemakers!"</p>
                    <h5>– John D.</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial text-center">
                    <p>"I love how transparent Give Well is. Seeing the progress bars and updates from campaigners keeps me engaged."</p>
                    <h5>– Lisa R.</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hero-section {
    background: linear-gradient(135deg, #4361ee 0%, #3046eb 100%);
    position: relative;
    overflow: hidden;
}
/* General Styles */
body { font-family: "Poppins", sans-serif; }
h1, h2, h5 { font-weight: bold; }
p { color: #555; }

/* Hero Section */
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg"><rect width="20" height="20" fill="none"/><circle cx="3" cy="3" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
}
.min-vh-75 {
    min-height: 75vh;
}
.feature-icon {
    width: 60px;
    height: 60px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
}
.stat-item {
    position: relative;
}
.stat-item:not(:last-child)::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    height: 50%;
    width: 1px;
    background: rgba(0,0,0,0.1);
}
.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}
.progress-bar {
    border-radius: 10px;
}

/* Featured Campaigns */
.featured-campaigns { padding: 60px 0; }
.card { border: none; border-radius: 10px; overflow: hidden; transition: transform 0.3s; }
.card:hover { transform: translateY(-5px); }
.card img { height: 200px; object-fit: cover; }
.progress { height: 8px; border-radius: 5px; }
.progress-bar { background: #28a745; }

/* Testimonials */
.testimonials { background: #f8f9fa; padding: 60px 0; }
.testimonial { background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
.testimonial p { font-style: italic; }
.testimonial h5 { margin-top: 10px; font-weight: bold; }

/* How It Works */
.how-it-works { padding: 60px 0; text-align: center; }
.how-it-works i { font-size: 50px; color: #007bff; margin-bottom: 15px; }

/* Dark Mode Styles */
.dark-mode p { color: #e0e0e0; }

.dark-mode .bg-light {
    background-color: #1e1e1e !important;
}

.dark-mode .testimonials {
    background-color: #1e1e1e;
}

.dark-mode .testimonial {
    background-color: #2d2d2d;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
}

.dark-mode .testimonial p {
    color: #e0e0e0;
}

.dark-mode .testimonial h5 {
    color: #e0e0e0;
}

.dark-mode .card-title.text-primary {
    color: #6d8eff !important;
}

.dark-mode .stat-item:not(:last-child)::after {
    background: rgba(255,255,255,0.1);
}

.dark-mode .feature-icon.bg-primary {
    background-color: #3046eb !important;
}

.dark-mode .feature-icon.bg-success {
    background-color: #28a745 !important;
}

.dark-mode .feature-icon.bg-info {
    background-color: #17a2b8 !important;
}
</style>
