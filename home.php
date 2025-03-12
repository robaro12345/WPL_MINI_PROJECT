<?php
echo '
<style>
/* General Styles */
body { font-family: "Poppins", sans-serif; }
h1, h2, h5 { font-weight: bold; }
p { color: #555; }

/* Hero Section */
.hero-section {
    background: #1a1e2e; /* Dark Blue */
    min-height: 80vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
    padding: 20px;
}
.hero-section h1 { font-size: 3rem; }
.hero-section p { font-size: 1.2rem; max-width: 600px; }
.hero-section .btn { font-size: 1.2rem; padding: 12px 30px; }

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
</style>

<!-- Hero Section -->
<div class="hero-section">
    <h1>Empower Change with Give Well</h1>
    <p>Join a global community dedicated to making an impact. Discover and support meaningful causes effortlessly.</p>
    <a href="?page=explore" class="btn btn-primary">Explore Campaigns</a>
</div>

<!-- Featured Campaigns -->
<div class="container featured-campaigns">
    <h2 class="text-center mb-4">🌟 Featured Campaigns</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <img src="assets/campaign1.jpg" class="card-img-top" alt="Campaign 1">
                <div class="card-body">
                    <h5 class="card-title">Clean Water for Africa</h5>
                    <p class="card-text">Help provide clean drinking water to thousands of families in need.</p>
                    <div class="progress mb-2">
                        <div class="progress-bar" style="width: 70%;"></div>
                    </div>
                    <p><strong>$14,000</strong> raised of $20,000</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">View Campaign</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <img src="assets/campaign2.jpg" class="card-img-top" alt="Campaign 2">
                <div class="card-body">
                    <h5 class="card-title">Education for All</h5>
                    <p class="card-text">Help fund schools and provide education for underprivileged children.</p>
                    <div class="progress mb-2">
                        <div class="progress-bar" style="width: 50%;"></div>
                    </div>
                    <p><strong>$10,000</strong> raised of $20,000</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">View Campaign</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <img src="assets/campaign3.jpg" class="card-img-top" alt="Campaign 3">
                <div class="card-body">
                    <h5 class="card-title">Emergency Relief Fund</h5>
                    <p class="card-text">Support immediate relief efforts for disaster-affected communities.</p>
                    <div class="progress mb-2">
                        <div class="progress-bar" style="width: 90%;"></div>
                    </div>
                    <p><strong>$18,000</strong> raised of $20,000</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">View Campaign</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How It Works -->
<div class="container how-it-works">
    <h2 class="text-center mb-4">🚀 How It Works</h2>
    <div class="row">
        <div class="col-md-4">
            <i class="fas fa-hands-helping"></i>
            <h5>Find a Cause</h5>
            <p>Browse through campaigns that need your help and choose one that speaks to you.</p>
        </div>
        <div class="col-md-4">
            <i class="fas fa-donate"></i>
            <h5>Donate Securely</h5>
            <p>Make a contribution using secure payment methods, including crypto donations.</p>
        </div>
        <div class="col-md-4">
            <i class="fas fa-heart"></i>
            <h5>See the Impact</h5>
            <p>Get updates and see how your donation is making a difference.</p>
        </div>
    </div>
</div>

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
';
?>
