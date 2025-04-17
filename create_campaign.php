<?php
checkAccess(['Campaigner']);

if (!isset($_SESSION['user'])) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                <h4>Access Denied</h4>
                <p>Please log in to create a campaign.</p>
                <a href="?page=login" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
          </div>';
    exit;
}
?>

<!-- Create Campaign Form -->
<div class="create-campaign-header bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 font-weight-bold mb-3">Create Campaign</h1>
                <p class="lead">Start your fundraising journey and make a difference</p>
            </div>
        </div>
    </div>
</div>

<div class="container mt-n5 mb-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <form id="createCampaignForm" method="POST" action="?page=submit_campaign" enctype="multipart/form-data">
                        <!-- Campaign Details Section -->
                        <div class="form-section mb-5">
                            <h4 class="mb-4">
                                <i class="fas fa-info-circle text-primary mr-2"></i>
                                Campaign Details
                            </h4>

                            <div class="form-group">
                                <label>Campaign Name</label>
                                <input type="text" name="name" class="form-control" required
                                       minlength="5" maxlength="100"
                                       data-validation="Please enter a campaign name (5-100 characters)">
                                <small class="form-text text-muted">
                                    Choose a clear, attention-grabbing name for your campaign
                                </small>
                            </div>

                            <div class="form-group">
                                <label>Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="">Select a category</option>
                                    <option value="Education">Education</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Environment">Environment</option>
                                    <option value="Community">Community</option>
                                    <option value="Technology">Technology</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Campaign Description</label>
                                <textarea name="description" class="form-control" rows="6" required
                                          minlength="100" maxlength="5000"
                                          data-validation="Please enter a detailed description (100-5000 characters)"></textarea>
                                <small class="form-text text-muted">
                                    Describe your campaign's purpose, goals, and how the funds will be used
                                </small>
                                <div class="char-counter text-muted mt-2">
                                    <span id="charCount">0</span>/5000 characters
                                </div>
                            </div>
                        </div>

                        <!-- Funding Details Section -->
                        <div class="form-section mb-5">
                            <h4 class="mb-4">
                                <i class="fas fa-dollar-sign text-primary mr-2"></i>
                                Funding Details
                            </h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Funding Goal (USD)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" name="goal" class="form-control" required
                                                   min="100" max="1000000" step="0.01"
                                                   data-validation="Please enter a valid goal amount ($100-$1,000,000)">
                                        </div>
                                        <small class="form-text text-muted">
                                            Set a realistic funding goal between $100 and $1,000,000
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control" required
                                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                               max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>"
                                               data-validation="Please select a valid end date">
                                        <small class="form-text text-muted">
                                            Campaign can run between 1 day and 1 year
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wallet Details Section -->
                        <div class="form-section mb-5">
                            <h4 class="mb-4">
                                <i class="fas fa-wallet text-primary mr-2"></i>
                                Wallet Details
                            </h4>

                            <div class="form-group">
                                <label>Ethereum Wallet Address</label>
                                <input type="text" name="wallet_address" class="form-control" required
                                       pattern="^0x[a-fA-F0-9]{40}$"
                                       title="Please enter a valid Ethereum wallet address starting with '0x' followed by 40 hexadecimal characters"
                                       data-validation="Please enter a valid Ethereum wallet address">
                                <small class="form-text text-muted">
                                    Enter your Ethereum wallet address to receive donations (e.g., 0x742d35Cc6634C0532925a3b844Bc454e4438f44e)
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Make sure to enter the correct wallet address. This cannot be changed after campaign creation.
                            </div>
                        </div>

                        <!-- Campaign Image Section -->
                        <div class="form-section mb-5">
                            <h4 class="mb-4">
                                <i class="fas fa-image text-primary mr-2"></i>
                                Campaign Image
                            </h4>

                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="campaignImage"
                                           name="image" accept="image/*"
                                           data-validation="Please upload a campaign image">
                                    <label class="custom-file-label" for="campaignImage">
                                        Choose image...
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Upload a compelling image that represents your campaign (max 5MB)
                                </small>
                            </div>

                            <div id="imagePreview" class="mt-3 d-none">
                                <img src="" alt="Campaign Image Preview" class="img-fluid rounded">
                            </div>
                        </div>

                        <!-- Terms and Submission -->
                        <div class="form-section">
                            <div class="custom-control custom-checkbox mb-4">
                                <input type="checkbox" class="custom-control-input" id="terms" required>
                                <label class="custom-control-label" for="terms">
                                    I agree to the terms and conditions and confirm that all information provided is accurate
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Create Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.create-campaign-header {
    background: linear-gradient(135deg, #4361ee 0%, #3046eb 100%);
    position: relative;
}
.form-section {
    position: relative;
    padding-left: 20px;
}
.form-section::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #e9ecef;
}
.char-counter {
    text-align: right;
    font-size: 0.875rem;
}
.custom-file-label::after {
    content: "Browse";
}
#imagePreview img {
    max-height: 300px;
    object-fit: cover;
}

/* Dark Mode Styles */
.dark-mode .create-campaign-header {
    background: linear-gradient(135deg, #3046eb 0%, #1e2a8f 100%);
}

.dark-mode .form-section::before {
    background: #333;
}

.dark-mode .text-primary {
    color: #6d8eff !important;
}

.dark-mode .custom-file-label {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e0e0e0;
}

.dark-mode .custom-file-label::after {
    background-color: #333;
    border-color: #404040;
    color: #e0e0e0;
}

.dark-mode .input-group-text {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e0e0e0;
}

.dark-mode .alert-info {
    background-color: #1e2a38;
    color: #9fcdff;
    border-color: #0d2a45;
}

.dark-mode .custom-control-label {
    color: #e0e0e0;
}

.dark-mode .invalid-feedback {
    color: #ff6b6b;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    document.getElementById('campaignImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        const label = document.querySelector('.custom-file-label');

        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.classList.remove('d-none');
                preview.querySelector('img').src = e.target.result;
            };
            reader.readAsDataURL(file);
            label.textContent = file.name;
        } else {
            preview.classList.add('d-none');
            label.textContent = 'Choose image...';
        }
    });

    // Character counter
    const descriptionField = document.querySelector('textarea[name="description"]');
    const charCounter = document.getElementById('charCount');

    descriptionField.addEventListener('input', function() {
        charCounter.textContent = this.value.length;
    });

    // Form validation
    const form = document.getElementById('createCampaignForm');
    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Validate required fields
        form.querySelectorAll('[required]').forEach(field => {
            if (!field.value) {
                isValid = false;
                const message = field.getAttribute('data-validation') || 'This field is required';
                showError(field, message);
            } else {
                removeError(field);
            }
        });

        // Validate wallet address format
        const walletField = form.querySelector('[name="wallet_address"]');
        if (walletField.value && !walletField.value.match(/^0x[a-fA-F0-9]{40}$/)) {
            isValid = false;
            showError(walletField, 'Please enter a valid Ethereum wallet address');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function showError(field, message) {
        removeError(field);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = message;
        field.classList.add('is-invalid');
        field.parentNode.appendChild(errorDiv);
    }

    function removeError(field) {
        field.classList.remove('is-invalid');
        const error = field.parentNode.querySelector('.invalid-feedback');
        if (error) {
            error.remove();
        }
    }
});
</script>
