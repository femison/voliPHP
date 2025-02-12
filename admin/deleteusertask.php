<?php
require_once('db_connection.php'); // Подключите ваш скрипт для соединения с базой данных

if (isset($_POST['action']) && isset($_POST['userTaskID'])) {
    $userTaskID = $_POST['userTaskID'];

    if ($_POST['action'] == 'delete_task') {
        $query = "DELETE FROM user_tasks WHERE UserTaskID = ?";
    } elseif ($_POST['action'] == 'remove_user_from_project') {
        $query = "DELETE FROM user_tasks WHERE UserTaskID = ?";
    }

    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $userTaskID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made.']);
    }

    $stmt->close();
    $connect->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
