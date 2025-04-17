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

.about-hero {
    background: linear-gradient(135deg, #4361ee 0%, #3046eb 100%);
    padding: 100px 0;
}
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url(\'data:image/svg+xml,<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg"><rect width="20" height="20" fill="none"/><circle cx="3" cy="3" r="1" fill="rgba(255,255,255,0.1)"/></svg>\');
}
.mission-icon {
    width: 100px;
    height: 100px;
    background: rgba(67,97,238,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
.mission-icon i {
    font-size: 40px;
    color: #4361ee;
}
.value-icon {
    width: 60px;
    height: 60px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.step-circle {
    width: 50px;
    height: 50px;
    background: #4361ee;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0 auto;
}
.team-avatar img {
    width: 120px;
    height: 120px;
    object-fit: cover;
}
.contact-item {
    padding: 20px;
    background: white;
    border-radius: 10px;
    transition: transform 0.3s ease;
}
.contact-item:hover {
    transform: translateY(-5px);
}
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
}

/* Dark Mode Styles */
.dark-mode .about-hero {
    background: linear-gradient(135deg, #3046eb 0%, #1e2a8f 100%);
}

.dark-mode .bg-light {
    background-color: #1e1e1e !important;
}

.dark-mode .how-it-works-section {
    background-color: #1e1e1e !important;
}

.dark-mode .how-it-works-section h2,
.dark-mode .how-it-works-section h5 {
    color: #e0e0e0;
}

.dark-mode .how-it-works-section .text-muted {
    color: #aaa !important;
}

.dark-mode .step-circle {
    background: #4361ee;
    box-shadow: 0 0 15px rgba(67, 97, 238, 0.5);
}

.dark-mode .card {
    background-color: #2d2d2d;
    border-color: #333;
}

.dark-mode .contact-item {
    background-color: #2d2d2d;
}

.dark-mode .text-muted {
    color: #aaa !important;
}

.dark-mode .text-primary {
    color: #6d8eff !important;
}

.dark-mode .value-icon.bg-primary {
    background-color: #3046eb !important;
}

.dark-mode .value-icon.bg-success {
    background-color: #28a745 !important;
}

.dark-mode .value-icon.bg-info {
    background-color: #17a2b8 !important;
}
</style>

<!-- About Hero Section -->
<div class="about-hero text-white position-relative overflow-hidden">
    <div class="hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center py-5">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 font-weight-bold mb-4">About Give Well</h1>
                <p class="lead mb-0" style="color: #ffd700;">Empowering communities through transparent and secure blockchain-based donations</p>
            </div>
        </div>
    </div>
</div>

<!-- Mission Section -->
<div class="container">
    <div class="row justify-content-center mt-n5 mb-5">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div class="mission-icon mb-3">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h4 class="text-primary">Our Mission</h4>
                        </div>
                        <div class="col-md-8">
                            <p class="lead text-muted mb-0">
                                To create a transparent, secure, and efficient platform that connects donors with meaningful causes,
                                leveraging blockchain technology to ensure trust and accountability in charitable giving.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Values Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Our Core Values</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="value-icon bg-primary text-white rounded-circle mb-3">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4>Security</h4>
                        <p class="text-muted">Utilizing blockchain technology to ensure secure and transparent transactions for all donations.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="value-icon bg-success text-white rounded-circle mb-3">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4>Trust</h4>
                        <p class="text-muted">Building trust through transparent processes and verified campaigns.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="value-icon bg-info text-white rounded-circle mb-3">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h4>Community</h4>
                        <p class="text-muted">Fostering a global community of generous donors and impactful campaigners.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-light how-it-works-section">
    <div class="container">
        <h2 class="text-center mb-5">How It Works</h2>
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <div class="step-circle mb-3">1</div>
                <h5>Create Account</h5>
                <p class="text-muted">Sign up and verify your identity to start creating or supporting campaigns.</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="step-circle mb-3">2</div>
                <h5>Connect Wallet</h5>
                <p class="text-muted">Link your cryptocurrency wallet to enable secure transactions.</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="step-circle mb-3">3</div>
                <h5>Choose Campaign</h5>
                <p class="text-muted">Browse and select campaigns that align with your values.</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="step-circle mb-3">4</div>
                <h5>Make Impact</h5>
                <p class="text-muted">Donate securely and track your contribution\'s impact.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Meet Our Team</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm hover-card">
                    <div class="card-body text-center p-4">
                        <div class="team-avatar mb-3">
                            <img src="https://ui-avatars.com/api/?name=alt+f4&background=4361ee&color=fff"
                                 alt="altf4" class="rounded-circle">
                        </div>
                        <h4>altf4</h4>
                        <p class="text-primary mb-2">Founder & CEO</p>
                        <p class="text-muted">Blockchain expert with 10+ years in fintech</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm hover-card">
                    <div class="card-body text-center p-4">
                        <div class="team-avatar mb-3">
                            <img src="https://ui-avatars.com/api/?name=Hemlock+Dropworth&background=4361ee&color=fff"
                                 alt="Hemlock Dropworth" class="rounded-circle">
                        </div>
                        <h4>Hemlock Dropworth</h4>
                        <p class="text-primary mb-2">Head of Operations</p>
                        <p class="text-muted">Former non-profit executive</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm hover-card">
                    <div class="card-body text-center p-4">
                        <div class="team-avatar mb-3">
                            <img src="https://ui-avatars.com/api/?name=TBNRobaro&background=4361ee&color=fff"
                                 alt="TBNRobaro" class="rounded-circle">
                        </div>
                        <h4>TBNRobaro</h4>
                        <p class="text-primary mb-2">Technical Lead</p>
                        <p class="text-muted">Smart contract developer</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Get in Touch</h2>
                <p class="text-muted mb-5">Have questions about our platform? We\'re here to help!</p>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="contact-item">
                            <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                            <h5>Email</h5>
                            <p class="text-muted mb-0">support@givewell.com</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="contact-item">
                            <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                            <h5>Phone</h5>
                            <p class="text-muted mb-0">+1 (555) 123-4567</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt fa-2x text-primary mb-3"></i>
                            <h5>Location</h5>
                            <p class="text-muted mb-0">San Francisco, CA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
';
?>
