<?php
if (isset($_SESSION['user'])) {
    header("Location: ?page=dashboard");
    exit;
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Welcome to Give Well</h2>

                    <div class="d-flex justify-content-center mb-4">
                        <div class="tab-container">
                            <span class="tab active" id="tab-login" onclick="showTab('login')">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </span>
                            <span class="tab" id="tab-register" onclick="showTab('register')">
                                <i class="fas fa-user-plus mr-2"></i>Register
                            </span>
                        </div>
                    </div>

                    <div id="login-form">
                        <form action="?page=login_process&type=login" method="POST" class="needs-validation" novalidate>
                            <div class="form-group">
                                <label for="login_email"><i class="fas fa-envelope mr-2"></i>Email</label>
                                <input type="email" name="email" id="login_email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="login_password"><i class="fas fa-lock mr-2"></i>Password</label>
                                <input type="password" name="password" id="login_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </button>
                        </form>
                    </div>

                    <div id="register-form" style="display:none;">
                        <form action="?page=login_process&type=register" method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reg_fname"><i class="fas fa-user mr-2"></i>First Name</label>
                                        <input type="text" name="fname" id="reg_fname" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reg_lname"><i class="fas fa-user mr-2"></i>Last Name</label>
                                        <input type="text" name="lname" id="reg_lname" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="reg_mname"><i class="fas fa-user mr-2"></i>Middle Name</label>
                                <input type="text" name="mname" id="reg_mname" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="reg_email"><i class="fas fa-envelope mr-2"></i>Email</label>
                                <input type="email" name="email" id="reg_email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="reg_password"><i class="fas fa-lock mr-2"></i>Password</label>
                                <input type="password" name="password" id="reg_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="reg_role"><i class="fas fa-user-tag mr-2"></i>Role</label>
                                <select name="role" id="reg_role" class="form-control" required>
                                    <option value="Donor">Donor</option>
                                    <option value="Campaigner">Campaigner</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="reg_wallet"><i class="fas fa-wallet mr-2"></i>Wallet Address</label>
                                <div class="input-group">
                                    <input type="text" name="wallet" id="reg_wallet" class="form-control" required>
                                    <div class="input-group-append">
                                        <button type="button" id="connectWalletRegister" class="btn btn-secondary">
                                            <i class="fas fa-link mr-2"></i>Connect Wallet
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus mr-2"></i>Register
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
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

/* Tab styling */
.tab-container {
    display: flex;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #dee2e6;
}

.tab {
    padding: 10px 20px;
    cursor: pointer;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.tab.active {
    background-color: #4361ee;
    color: white;
}

/* Dark Mode Styles */
.dark-mode .tab-container {
    border-color: #404040;
}

.dark-mode .tab {
    background-color: #2d2d2d;
    color: #e0e0e0;
}

.dark-mode .tab.active {
    background-color: #3046eb;
    color: white;
}

.dark-mode .input-group-append .btn-secondary {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e0e0e0;
}

.dark-mode .input-group-append .btn-secondary:hover {
    background-color: #333;
    border-color: #4361ee;
}
</style>
<script>
function showTab(tab) {
    document.getElementById('login-form').style.display = tab === 'login' ? 'block' : 'none';
    document.getElementById('register-form').style.display = tab === 'register' ? 'block' : 'none';
    document.getElementById('tab-login').classList.toggle('active', tab === 'login');
    document.getElementById('tab-register').classList.toggle('active', tab === 'register');
}

async function connectWalletRegister() {
    if (typeof window.ethereum !== 'undefined') {
        window.web3 = new Web3(window.ethereum);
        try {
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            document.getElementById("reg_wallet").value = accounts[0];
            alert('✅ Wallet connected: ' + accounts[0]);
        } catch (error) {
            alert('❌ Wallet connection failed: ' + error.message);
        }
    } else {
        alert('⚠️ Please install MetaMask');
    }
}

document.getElementById("connectWalletRegister").addEventListener("click", connectWalletRegister);

// Add form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
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
</script>
