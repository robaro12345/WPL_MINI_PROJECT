<?php      
      if (!isset($_SESSION['user'])) {
          echo '<p>Please <a href="?page=login">login</a> to create a campaign.</p>';
      } else {

          echo '<h1>Create a Campaign</h1>
          <form action="?page=submit_campaign" method="POST">
            <div class="form-group">
              <label for="campName">Campaign Name</label>
              <input type="text" name="campName" id="campName" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="campDesc">Description</label>
              <textarea name="campDesc" id="campDesc" rows="3" class="form-control" required></textarea>
            </div>
            <div class="form-group">
              <label for="campGoal">Goal Amount</label>
              <input type="number" step="0.01" name="campGoal" id="campGoal" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="campStart">Start Date</label>
              <input type="date" name="campStart" id="campStart" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="campEnd">End Date</label>
              <input type="date" name="campEnd" id="campEnd" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Campaign</button>
          </form>';
      }
 ?>