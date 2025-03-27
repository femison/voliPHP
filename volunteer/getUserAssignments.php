<?php
require_once '../admin/db_connection.php';

header('Content-Type: application/json');

$userId = $_GET['user_id'] ?? 0;

$query = "SELECT TaskID FROM user_tasks WHERE UserID = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$assignments = [];

while ($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}

echo json_encode($assignments);
?>