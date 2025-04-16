<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_GET['type'] ?? 'login';
    
    if ($type === 'login') {
        $email = sanitize($_POST['email']);
        $password = sanitize($_POST['password']);
        
        $query = "SELECT U.*, E.Email 
                  FROM USERS U 
                  INNER JOIN EMAIL E ON U.UserID = E.UserID 
                  WHERE E.Email = ? AND E.Primary_Email = TRUE";

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
                        'Role' => $user['Role'], // Ensure this is set correctly
                        'Wallet_Address' => $user['Wallet_Address']
                    ];
                    
                    echo '<div class="alert alert-success text-center p-3">
                            <strong>Success!</strong> Login successful. Redirecting...
                          </div>';
                    echo '<script>setTimeout(function(){ window.location = "?page=dashboard"; }, 2000);</script>';
                } else {
                    echo '<div class="alert alert-danger text-center p-3">
                            <strong>Error!</strong> Incorrect password. Please try again.
                          </div>';
                }
            } else {
                echo '<div class="alert alert-danger text-center p-3">
                        <strong>Error!</strong> Email not found. Please register first.
                      </div>';
            }
            $stmt->close();
        }
    } else { // Registration Process
        $fname = sanitize($_POST['fname']);
        $mname = sanitize($_POST['mname']) ?: null;
        $lname = sanitize($_POST['lname']);
        $email = sanitize($_POST['email']);
        $password = password_hash(sanitize($_POST['password']), PASSWORD_DEFAULT);
        $role = sanitize($_POST['role']);
        $wallet = sanitize($_POST['wallet']);

        $conn->begin_transaction();
        
        try {
            $check_query = "SELECT U.UserID FROM USERS U 
                            LEFT JOIN EMAIL E ON U.UserID = E.UserID 
                            WHERE U.Wallet_Address = ? OR E.Email = ?";
                            
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("ss", $wallet, $email);
            $stmt->execute();
            $check_result = $stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                throw new Exception("User with this email or wallet already exists.");
            }
            
            $user_query = "INSERT INTO USERS (Fname, Mname, Lname, Wallet_Address, Creation_Date, Role, Password) 
                           VALUES (?, ?, ?, ?, CURDATE(), ?, ?)";
                           
            $stmt = $conn->prepare($user_query);
            $stmt->bind_param("ssssss", $fname, $mname, $lname, $wallet, $role, $password);
            $stmt->execute();
            $userID = $conn->insert_id;
            
            $email_query = "INSERT INTO EMAIL (UserID, Email, Primary_Email) VALUES (?, ?, TRUE)";
            $stmt = $conn->prepare($email_query);
            $stmt->bind_param("is", $userID, $email);
            $stmt->execute();
            
            $conn->commit();
            
            $_SESSION['user'] = [
                'UserID' => $userID,
                'Fname' => $fname,
                'Lname' => $lname,
                'Email' => $email,
                'Role' => $role,
                'Wallet_Address' => $wallet
            ];
            
            echo '<div class="alert alert-success text-center p-3">
                    <strong>Success!</strong> Registration complete. Redirecting...
                  </div>';
            echo '<script>setTimeout(function(){ window.location = "?page=dashboard"; }, 2000);</script>';
            
        } catch (Exception $e) {
            $conn->rollback();
            echo '<div class="alert alert-danger text-center p-3">
                    <strong>Registration Failed:</strong> ' . $e->getMessage() . '
                  </div>';
        }
    }
} else {
    echo '<div class="alert alert-danger text-center p-3">
            <strong>Error!</strong> Invalid request.
          </div>';
}
?>
