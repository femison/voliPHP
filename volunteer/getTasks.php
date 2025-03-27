<?php
require '../admin/db_connection.php';

$projectId = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

if ($projectId > 0) {
    $query = "SELECT TaskID, Description, Status FROM tasks WHERE ProjectID = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($tasks);
} else {
    echo json_encode([]);
}
?>