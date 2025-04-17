<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
    $name = sanitize($_POST['name']);
    $desc = sanitize($_POST['description']);
    $goal = floatval($_POST['goal']);
    $start = date('Y-m-d');
    $end = sanitize($_POST['end_date']);
    $category = sanitize($_POST['category']);
    $wallet = sanitize($_POST['wallet_address']);
    $userID = $_SESSION['user']['UserID'];

    $conn->begin_transaction();
    try {
        // Insert campaign first
        $sql = "INSERT INTO CAMPAIGN (CRID_USER, CRID_ORG, Current_Amount, Name, Start_Date, End_Date, 
                Description, Goal, Category, Approval_Status) 
                VALUES (?, NULL, 0, ?, ?, ?, ?, ?, ?, 0)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("issssds", $userID, $name, $start, $end, $desc, $goal, $category);
            if ($stmt->execute()) {
                $campaignId = $conn->insert_id;
                
                // Handle image upload if provided
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $file = $_FILES['image'];
                    $fileName = uniqid() . '_' . basename($file['name']);
                    $targetPath = 'uploads/' . $fileName;
                    
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        // Insert into RESOURCES table
                        $resourceSql = "INSERT INTO RESOURCES (CampID, TYPE, URL) VALUES (?, 'image', ?)";
                        $resourceStmt = $conn->prepare($resourceSql);
                        $resourceStmt->bind_param("is", $campaignId, $fileName);
                        $resourceStmt->execute();
                        $resourceStmt->close();
                    }
                }
                
                $conn->commit();
                echo '<div class="alert alert-success text-center">🎉 Campaign created successfully! <a href="?page=dashboard" class="alert-link">View Campaigns</a></div>';
            } else {
                throw new Exception($stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo '<div class="alert alert-danger text-center">⚠️ Error creating campaign: ' . $e->getMessage() . '</div>';
    }
} else {
    echo '<div class="alert alert-danger text-center">⛔ Unauthorized access.</div>';
}
?>
