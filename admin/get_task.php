<?php
// Подключение к базе данных
require_once('db_connection.php');

if (isset($_GET['projectId'])) {
    $projectId = $_GET['projectId'];
    $query = "SELECT TaskID, Description FROM tasks WHERE ProjectID = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    echo json_encode($tasks);
} else {
    echo json_encode([]);
}
?>
