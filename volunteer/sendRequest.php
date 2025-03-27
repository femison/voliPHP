<?php
require_once '../admin/db_connection.php';

header('Content-Type: application/json');

// Получаем данные из запроса
$data = json_decode(file_get_contents('php://input'), true);

// Валидация данных
if (empty($data['user_id']) || empty($data['project_id']) || empty($data['task_id'])) {
    echo json_encode(['success' => false, 'message' => 'Не все обязательные поля заполнены']);
    exit;
}

try {
    // Проверяем существование проекта и задачи
    $checkProject = $connect->prepare("SELECT ProjectID FROM projects WHERE ProjectID = ?");
    $checkProject->bind_param("i", $data['project_id']);
    $checkProject->execute();
    
    if ($checkProject->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Проект не найден']);
        exit;
    }

    $checkTask = $connect->prepare("SELECT TaskID FROM tasks WHERE TaskID = ? AND ProjectID = ?");
    $checkTask->bind_param("ii", $data['task_id'], $data['project_id']);
    $checkTask->execute();
    
    if ($checkTask->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Задача не найдена в этом проекте']);
        exit;
    }

    // Проверяем, не подана ли уже такая заявка
    $checkQuery = "SELECT RequestID FROM users_pending_approval 
                  WHERE UserID = ? AND ProjectID = ? AND TaskID = ?";
    $stmt = $connect->prepare($checkQuery);
    $stmt->bind_param("iii", $data['user_id'], $data['project_id'], $data['task_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Вы уже подавали заявку на эту задачу']);
        exit;
    }

    // Проверяем, не выполняется ли уже эта задача пользователем
    $checkAssignment = $connect->prepare("SELECT UserTaskID FROM user_tasks WHERE UserID = ? AND TaskID = ?");
    $checkAssignment->bind_param("ii", $data['user_id'], $data['task_id']);
    $checkAssignment->execute();
    
    if ($checkAssignment->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Вы уже выполняете эту задачу']);
        exit;
    }

    // Создаем новую заявку
    $insertQuery = "INSERT INTO users_pending_approval 
                   (UserID, ProjectID, TaskID, Status, CreatedAt, UpdatedAt) 
                   VALUES (?, ?, ?, 'На рассмотрении', NOW(), NOW())";
    $stmt = $connect->prepare($insertQuery);
    $stmt->bind_param("iii", $data['user_id'], $data['project_id'], $data['task_id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Заявка успешно создана']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при создании заявки: ' . $stmt->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера: ' . $e->getMessage()]);
}
?>