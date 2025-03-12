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
    min-height: 60vh;
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

/* Mission & Vision */
.mission-section { padding: 60px 0; text-align: center; }
.mission-section i { font-size: 50px; color: #007bff; margin-bottom: 15px; }

/* Why Choose Us */
.why-choose-us { background: #f8f9fa; padding: 60px 0; }
.why-choose-us .icon-box { text-align: center; padding: 20px; }
.why-choose-us i { font-size: 40px; color: #28a745; }

/* Call to Action */
.cta { background: #007bff; color: white; padding: 40px 0; text-align: center; }
.cta h2 { font-size: 2rem; }
.cta .btn { background: white; color: #007bff; font-weight: bold; padding: 12px 30px; }

</style>

<!-- Hero Section -->
<div class="hero-section">
    <h1>About Give Well</h1>
    <p>Connecting communities with causes, one donation at a time.</p>
</div>

<!-- Mission & Vision -->
<div class="container mission-section">
    <div class="row">
        <div class="col-md-6">
            <i class="fas fa-bullseye"></i>
            <h2>Our Mission</h2>
            <p>We aim to bridge communities and support campaigns through transparent donations and active engagement.</p>
        </div>
        <div class="col-md-6">
            <i class="fas fa-lightbulb"></i>
            <h2>Our Vision</h2>
            <p>We envision a world where generosity is effortless, and every campaign gets the support it deserves.</p>
        </div>
    </div>
</div>

<!-- Why Choose Us -->
<div class="why-choose-us">
    <div class="container">
        <h2 class="text-center mb-4">🌍 Why Choose Give Well?</h2>
        <div class="row">
            <div class="col-md-4 icon-box">
                <i class="fas fa-hand-holding-heart"></i>
                <h5>Transparent Giving</h5>
                <p>Every donation is trackable, ensuring funds reach the right place.</p>
            </div>
            <div class="col-md-4 icon-box">
                <i class="fas fa-users"></i>
                <h5>Community-Driven</h5>
                <p>Our platform thrives on trust, support, and real impact.</p>
            </div>
            <div class="col-md-4 icon-box">
                <i class="fas fa-lock"></i>
                <h5>Secure & Easy</h5>
                <p>Seamless and safe transactions, including crypto donations.</p>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="cta">
    <h2>Be a Part of the Change</h2>
    <p>Support a campaign today and make a real difference.</p>
    <a href="?page=explore" class="btn">Explore Campaigns</a>
</div>
';
?>
