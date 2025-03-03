<?php
require_once('../func/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['userID'] ?? null;
    $projectId = $_POST['projectID'] ?? null;
    $taskId = $_POST['taskID'] ?? null;

    // Проверка наличия всех необходимых данных
    if (!$userId || !$projectId || !$taskId) {
        echo "Не все данные предоставлены.";
        echo "Пользователь - ", $userId;
        echo "Проект - ", $projectId;
        echo "Задача - ", $taskId;
        exit;
    }

    // Подготовка SQL запроса для вставки данных
    $query = "INSERT INTO user_tasks (UserID, TaskID, ProjectID) VALUES (?, ?, ?)";
    $stmt = $connect->prepare($query);
    if ($stmt) {
        $stmt->bind_param("iii", $userId, $taskId, $projectId);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo "Участие успешно добавлено.";
            header("Location: ./adminform.php");
        } else {
            echo "Не удалось добавить участие.";
        }
        $stmt->close();
    } else {
        echo "Ошибка подготовки запроса: " . $connect->error;
    }
}

$connect->close();
exit();

?>
