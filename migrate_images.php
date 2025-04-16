<?php
// Include database connection
require_once 'config/database.php';

// Set error logging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', 'migration_errors.log');

// Enable output buffering
ob_start();

echo '<html>
<head>
    <title>Image Migration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .log { margin: 10px 0; padding: 10px; border-left: 4px solid #ccc; }
    </style>
</head>
<body>';

// Create RESOURCES table if it doesn't exist
if (!$conn->query("DESCRIBE RESOURCES")) {
    error_log("Creating RESOURCES table...");
    $createTable = "CREATE TABLE IF NOT EXISTS RESOURCES (
        ResourceID INT PRIMARY KEY AUTO_INCREMENT,
        CampID INT,
        TYPE VARCHAR(50) NOT NULL,
        URL VARCHAR(255) NOT NULL,
        FOREIGN KEY (CampID) REFERENCES CAMPAIGN(CID)
    )";
    if (!$conn->query($createTable)) {
        error_log("Error creating RESOURCES table: " . $conn->error);
        die("<p class='error'>Error creating RESOURCES table: " . $conn->error . "</p>");
    }
    echo "<p class='success'>RESOURCES table created successfully!</p>";
}

// Start transaction
$conn->begin_transaction();

try {
    // Get all campaigns with images
    $result = $conn->query("SELECT CID, Image FROM CAMPAIGN WHERE Image IS NOT NULL AND Image != ''");
    
    if (!$result) {
        throw new Exception("Error fetching campaigns: " . $conn->error);
    }

    echo "<p>Found " . $result->num_rows . " campaigns with images</p>";
    
    $migrated = 0;
    while ($row = $result->fetch_assoc()) {
        // Check if image already exists in RESOURCES
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM RESOURCES WHERE CampID = ? AND TYPE = 'image'");
        $checkStmt->bind_param("i", $row['CID']);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->fetch_assoc()['count'] > 0;
        $checkStmt->close();

        if (!$exists) {
            // Insert into RESOURCES table
            $stmt = $conn->prepare("INSERT INTO RESOURCES (CampID, TYPE, URL) VALUES (?, 'image', ?)");
            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }
            
            $stmt->bind_param("is", $row['CID'], $row['Image']);
            if (!$stmt->execute()) {
                throw new Exception("Error inserting resource: " . $stmt->error);
            }
            $stmt->close();
            $migrated++;
    }
    
    // Only drop the Image column if we successfully migrated all images
    if ($migrated == $result->num_rows) {
        error_log("Dropping Image column from CAMPAIGN table...");
        if (!$conn->query("ALTER TABLE CAMPAIGN DROP COLUMN Image")) {
            throw new Exception("Error dropping Image column: " . $conn->error);
        }
        error_log("Image column dropped successfully");
        echo "<p class='success'>Dropped Image column from CAMPAIGN table</p>";
    }
    
    $conn->commit();
    error_log("Migration completed successfully! Migrated $migrated images.");
    echo "<p class='success'>Migration completed successfully! Migrated $migrated images.</p>";
} catch (Exception $e) {
    error_log("Migration failed: " . $e->getMessage());
    $conn->rollback();
    echo "<p class='error'>Migration failed: " . $e->getMessage() . "</p>";
}

$conn->close();
?>
    </div>
</body>
</html>