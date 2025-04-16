<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie("username", "", time() - 3600, "/"); // Delete the cookie
    header("Location: index.php");
    exit;
}

// Check if user is already logged in
if (isset($_SESSION['username'])) {
    echo "Welcome, " . htmlspecialchars($_SESSION['username']) . "! <br>";
    echo "<a href='?logout=true'>Logout</a>";
    exit;
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];

    if (!empty($username)) {
        $_SESSION['username'] = $username;

        // Set cookie for 7 days
        setcookie("username", $username, time() + (7 * 24 * 60 * 60), "/");

        header("Location: index.php");
        exit;
    } else {
        echo "Username cannot be empty!";
    }
}

// Check if username is stored in cookie
$saved_username = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login with Session & Cookies</title>
</head>
<body>

<h2>Login</h2>
<form method="POST">
    <input type="text" name="username" placeholder="Enter username" value="<?php echo htmlspecialchars($saved_username); ?>" required>
    <button type="submit">Login</button>
</form>

</body>
</html>
