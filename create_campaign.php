<?php      
if (!isset($_SESSION['user'])) {
    echo '<div class="container mt-5 text-center">
            <div class="alert alert-warning">
                Please <a href="?page=login" class="alert-link">login</a> to create a campaign.
            </div>
          </div>';
} else {
    echo '<div class="container mt-5">
            <div class="card shadow-sm p-4">
                <h1 class="text-center text-primary">Create a Campaign</h1>
                <form action="?page=submit_campaign" method="POST">
                    <div class="form-group">
                        <label for="campName"><strong>Campaign Name</strong></label>
                        <input type="text" name="campName" id="campName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="campDesc"><strong>Description</strong></label>
                        <textarea name="campDesc" id="campDesc" rows="3" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="campGoal"><strong>Goal Amount ($)</strong></label>
                        <input type="number" step="0.01" name="campGoal" id="campGoal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="campStart"><strong>Start Date</strong></label>
                        <input type="date" name="campStart" id="campStart" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="campEnd"><strong>End Date</strong></label>
                        <input type="date" name="campEnd" id="campEnd" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-3">
                        <i class="fas fa-plus-circle"></i> Create Campaign
                    </button>
                </form>
            </div>
          </div>';
}
?>
