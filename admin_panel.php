<?php
if (!isset($_SESSION['user']) || (($_SESSION['user']['Role'] ?? '') != 'Admin')) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger text-center">Access denied.</div>
          </div>';
} else {
    echo '<div class="container mt-5">';
    echo '<h1 class="text-center mb-4">Admin Panel</h1>';
    
    $result = $conn->query("SELECT * FROM CAMPAIGN");
    
    if ($result && $result->num_rows > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-hover table-bordered shadow-sm">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';
        
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>' . $row['CID'] . '</td>
                    <td>' . htmlspecialchars($row['Name']) . '</td>
                    <td class="' . ($row['Approval_Status'] ? 'text-success' : 'text-warning') . '">' . 
                        ($row['Approval_Status'] ? 'Approved' : 'Pending') . 
                    '</td>';
                    
            if (!$row['Approval_Status']) {
                echo '<td>
                        <a href="?page=approve_campaign&campaign=' . $row['CID'] . '" 
                           class="btn btn-success btn-sm">
                           <i class="fas fa-check"></i> Approve
                        </a>
                      </td>';
            } else {
                echo '<td class="text-muted">--</td>';
            }
            echo '</tr>';
        }
        
        echo '</tbody></table></div>';
    } else {
        echo '<div class="alert alert-info text-center">No campaigns found.</div>';
    }
    
    echo '</div>';
}
?>
