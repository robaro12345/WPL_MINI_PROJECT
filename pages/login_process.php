<?php
session_start();
$conn = new mysqli("localhost", "root", "", "givewell221");
if ($conn->connect_error) {
    error_log("Database Connection Error: " . $conn->connect_error);
    die('<div class="alert alert-danger">Database connection failed.</div>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_GET['type'] ?? 'login';

    if ($type === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Fetch user data
        $query = "SELECT U.*, U.Password, E.Email FROM USERS U 
          INNER JOIN EMAIL E ON U.UserID = E.UserID 
          WHERE E.Email = ?";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['Password'])) {
                    $_SESSION['user'] = [
                        'UserID' => $user['UserID'],
                        'Fname' => $user['Fname'],
                        'Lname' => $user['Lname'],
                        'Email' => $user['Email'],
                        'Role' => $user['Role'],
                        'Wallet_Address' => $user['Wallet_Address']
                    ];
                    echo '<div class="alert alert-success">Login successful! Redirecting...</div>';
                    echo '<script>setTimeout(function(){ window.location = "dashboard.php"; }, 2000);</script>';
                } else {
                    echo '<div class="alert alert-danger">Invalid password. Please try again.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">Email not found. Please register first.</div>';
            }
            $stmt->close();
        }
    } else { // Registration
        $fname = $_POST['fname'] ?? '';
        $mname = $_POST['mname'] ?? null;  // Allow NULL instead of an empty string
        $lname = $_POST['lname'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
        $role = $_POST['role'] ?? 'user';
        $wallet = $_POST['wallet'] ?? '';

        $conn->begin_transaction();
        try {
            // Check if email or wallet exists
            $check_query = "SELECT U.UserID FROM USERS U 
                            LEFT JOIN EMAIL E ON U.UserID = E.UserID 
                            WHERE U.Wallet_Address = ? OR E.Email = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("ss", $wallet, $email);
            $stmt->execute();
            $check_result = $stmt->get_result();

            if ($check_result->num_rows > 0) {
                throw new Exception("User with this email or wallet already exists");
            }

            // Insert into USERS table
            $user_query = "INSERT INTO USERS (Fname, Mname, Lname, Wallet_Address, Creation_Date, Role, Password) 
                           VALUES (?, ?, ?, ?, CURDATE(), ?, ?)";
            $stmt = $conn->prepare($user_query);
            $stmt->bind_param("ssssss", $fname, $mname, $lname, $wallet, $role, $password);
            $stmt->execute();
            $userID = $conn->insert_id;

            // Insert into EMAIL table
            $email_query = "INSERT INTO EMAIL (UserID, Email, Primary_Email) VALUES (?, ?, TRUE)";
            $stmt = $conn->prepare($email_query);
            $stmt->bind_param("is", $userID, $email);
            $stmt->execute();

            $conn->commit(); // Commit transaction

            $_SESSION['user'] = [
                'UserID' => $userID,
                'Fname' => $fname,
                'Lname' => $lname,
                'Email' => $email,
                'Role' => $role,
                'Wallet_Address' => $wallet
            ];

            echo '<div class="alert alert-success">Registration successful! Redirecting...</div>';
            echo '<script>setTimeout(function(){ window.location = "dashboard.php"; }, 2000);</script>';
        } catch (Exception $e) {
            $conn->rollback(); // Rollback transaction
            error_log("Registration Error: " . $e->getMessage());
            echo '<div class="alert alert-danger">Registration failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>
