<?php
require_once '../admin/db_connection.php';

header('Content-Type: application/json');

$userId = $_GET['user_id'] ?? 0;

$query = "SELECT TaskID FROM users_pending_approval 
          WHERE UserID = ? AND Status != 'Отклонена'";
$stmt = $connect->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$requests = [];

while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);
?>