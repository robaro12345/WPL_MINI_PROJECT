<?php

if (!isset($_SESSION['user'])) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                <h4>Access Denied</h4>
                <p>Please <a href="?page=login" class="alert-link">login</a> to edit your profile.</p>
            </div>
          </div>';
    exit;
}

$userID = $_SESSION['user']['UserID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $conn->real_escape_string($_POST['fname']);
    $mname = $conn->real_escape_string($_POST['mname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $email = $conn->real_escape_string($_POST['email']);

    $updateUser = "UPDATE USERS SET Fname = '$fname', Mname = '$mname', Lname = '$lname' WHERE UserID = '$userID'";
    $updateEmail = "UPDATE EMAIL SET Email = '$email' WHERE UserID = '$userID' AND Primary_Email = 1";

    if ($conn->query($updateUser) && $conn->query($updateEmail)) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>Profile updated successfully!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
        $_SESSION['user']['Fname'] = $fname;
        $_SESSION['user']['Mname'] = $mname;
        $_SESSION['user']['Lname'] = $lname;
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>Error updating profile: ' . $conn->error . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    }
}

$result = $conn->query("SELECT Fname, Mname, Lname, Email FROM USERS u 
                       JOIN EMAIL e ON u.UserID = e.UserID 
                       WHERE u.UserID = '$userID' AND e.Primary_Email = 1");
$user = $result->fetch_assoc();
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <div class="profile-avatar mr-4">
                            <div class="avatar-circle-lg">
                                <?php echo strtoupper(substr($user['Fname'], 0, 1) . substr($user['Lname'], 0, 1)); ?>
                            </div>
                        </div>
                        <div>
                            <h2 class="mb-1">Edit Profile</h2>
                            <p class="mb-0">Update your personal information</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="profile-section mb-4">
                            <h4 class="text-primary mb-3">
                                <i class="fas fa-user mr-2"></i>Personal Information
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fname">
                                            <i class="fas fa-user mr-1"></i>First Name
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="fname" 
                                               name="fname" 
                                               value="<?php echo htmlspecialchars($user['Fname']); ?>" 
                                               required>
                                        <div class="invalid-feedback">Please provide your first name.</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lname">
                                            <i class="fas fa-user mr-1"></i>Last Name
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="lname" 
                                               name="lname" 
                                               value="<?php echo htmlspecialchars($user['Lname']); ?>" 
                                               required>
                                        <div class="invalid-feedback">Please provide your last name.</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mname">
                                            <i class="fas fa-user mr-1"></i>Middle Name
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="mname" 
                                               name="mname" 
                                               value="<?php echo htmlspecialchars($user['Mname']); ?>">
                                        <small class="text-muted">Optional</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">
                                            <i class="fas fa-envelope mr-1"></i>Email Address
                                        </label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               value="<?php echo htmlspecialchars($user['Email']); ?>" 
                                               required>
                                        <div class="invalid-feedback">Please provide a valid email address.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
                            <a href="?page=profile" class="btn btn-outline-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle-lg {
    width: 80px;
    height: 80px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    color: white;
}
.profile-section {
    position: relative;
    padding-bottom: 1.5rem;
}
.profile-section:not(:last-child)::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: rgba(0,0,0,.1);
}
.form-control {
    border-radius: 8px;
    padding: 0.75rem 1rem;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}
.form-control:focus {
    border-color: #4361ee;
    box-shadow: 0 0 0 0.2rem rgba(67,97,238,.25);
}
.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.btn:hover {
    transform: translateY(-2px);
}
.alert {
    border-radius: 10px;
    border: none;
}
.alert-dismissible .close {
    padding: 0.75rem 1.25rem;
}
</style>

<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Fade out alerts after 5 seconds
window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 5000);
</script>