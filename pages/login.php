<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php include 'navbar.php'; ?>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-5 w-75"> <!-- Increased width -->
        <h1 class="text-center mb-4">Login / Register</h1>
        
        <div class="nav nav-tabs justify-content-center" role="tablist">
            <button class="nav-link active" id="tab-login" onclick="showTab('login')">Login</button>
            <button class="nav-link" id="tab-register" onclick="showTab('register')">Register</button>
        </div>

        <div id="login-form">
            <form action="login_process.php?type=login" method="POST">
                <div class="form-group mt-3">
                    <label for="login_email">Email</label>
                    <input type="email" name="email" id="login_email" class="form-control form-control-lg" required>
                </div>
                <div class="form-group mt-2">
                    <label for="login_password">Password</label>
                    <input type="password" name="password" id="login_password" class="form-control form-control-lg" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
            </form>
        </div>

        <div id="register-form" style="display:none;">
            <form action="login_process.php?type=register" method="POST">
                <div class="form-group mt-3">
                    <label for="reg_fname">First Name</label>
                    <input type="text" name="fname" id="reg_fname" class="form-control form-control-lg" required>
                </div>
                <div class="form-group mt-2">
                    <label for="reg_mname">Middle Name</label>
                    <input type="text" name="mname" id="reg_mname" class="form-control form-control-lg">
                </div>
                <div class="form-group mt-2">
                    <label for="reg_lname">Last Name</label>
                    <input type="text" name="lname" id="reg_lname" class="form-control form-control-lg" required>
                </div>
                <div class="form-group mt-2">
                    <label for="reg_email">Email</label>
                    <input type="email" name="email" id="reg_email" class="form-control form-control-lg" required>
                </div>
                <div class="form-group mt-2">
                    <label for="reg_password">Password</label>
                    <input type="password" name="password" id="reg_password" class="form-control form-control-lg" required>
                </div>
                <div class="form-group mt-2">
                    <label for="reg_role">Role</label>
                    <select name="role" id="reg_role" class="form-control form-control-lg" required>
                        <option value="Donor">Donor</option>
                        <option value="Campaigner">Campaigner</option>
                    </select>
                </div>
                <div class="form-group mt-2">
                    <label for="reg_wallet">Wallet Address</label>
                    <input type="text" name="wallet" id="reg_wallet" class="form-control form-control-lg" required readonly>
                    <button type="button" id="connectWalletRegister" class="btn btn-secondary mt-2 w-100">Connect MetaMask</button>
                </div>
                <button type="submit" class="btn btn-success w-100 mt-3">Register</button>
            </form>
        </div>
    </div>
</div>

<script>
    function showTab(tab){
        document.getElementById('login-form').style.display = (tab === 'login') ? 'block' : 'none';
        document.getElementById('register-form').style.display = (tab === 'register') ? 'block' : 'none';
        document.getElementById('tab-login').classList.toggle('active', tab === 'login');
        document.getElementById('tab-register').classList.toggle('active', tab === 'register');
    }

    async function connectWalletRegister(){
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

<style>
    .container {
        max-width: 800px; /* Increased width */
    
    }
    .card {
        border-radius: 12px;
    }
    .nav-tabs .nav-link {
        cursor: pointer;
        width: 50%;
        text-align: center;
        font-size: 1.2rem;
        padding: 12px;
    }
    .nav-tabs .nav-link.active {
        background-color: #007bff;
        color: white;
    }
    .form-control {
        border-radius: 8px;
        padding: 12px;
    }
    .btn-primary, .btn-success, .btn-secondary {
        border-radius: 8px;
        padding: 12px;
        font-size: 1.1rem;
    }
    .btn-primary:hover, .btn-success:hover, .btn-secondary:hover {
        opacity: 0.85;
    }
</style>

<?php include 'footer.php'; ?>
