<?php
if (isset($_SESSION['user'])) {
    header("Location: ?page=dashboard");
    exit;
}

echo <<<HTML
<h1>Login / Register</h1>
<div>
    <div class="tab-container">
        <span class="tab active" id="tab-login" onclick="showTab('login')">Login</span>
        <span class="tab" id="tab-register" onclick="showTab('register')">Register</span>
    </div>

    <div id="login-form">
        <form action="?page=login_process&type=login" method="POST">
            <div class="form-group">
                <label for="login_email">Email</label>
                <input type="email" name="email" id="login_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="login_password">Password</label>
                <input type="password" name="password" id="login_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <div id="register-form" style="display:none;">
        <form action="?page=login_process&type=register" method="POST">
            <div class="form-group">
                <label for="reg_fname">First Name</label>
                <input type="text" name="fname" id="reg_fname" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="reg_mname">Middle Name</label>
                <input type="text" name="mname" id="reg_mname" class="form-control">
            </div>
            <div class="form-group">
                <label for="reg_lname">Last Name</label>
                <input type="text" name="lname" id="reg_lname" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="reg_email">Email</label>
                <input type="email" name="email" id="reg_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="reg_password">Password</label>
                <input type="password" name="password" id="reg_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="reg_role">Role</label>
                <select name="role" id="reg_role" class="form-control" required>
                    <option value="Donor">Donor</option>
                    <option value="Campaigner">Campaigner</option>
                </select>
            </div>
            <div class="form-group">
                <label for="reg_wallet">Wallet Address</label>
                <input type="text" name="wallet" id="reg_wallet" class="form-control" required>
                <button type="button" id="connectWalletRegister" class="btn btn-secondary mt-2">Connect Wallet via MetaMask</button>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</div>

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
            alert('Wallet connected: ' + accounts[0]);
        } catch (error) {
            alert('Wallet connection failed: ' + error.message);
        }
    } else {
        alert('Please install MetaMask');
    }
}

document.getElementById("connectWalletRegister").addEventListener("click", connectWalletRegister);
</script>
HTML;
?>
