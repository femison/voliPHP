<?php
// admin/process_request.php

require '../func/db_connection.php';

// Проверяем, переданы ли необходимые параметры
if (!isset($_GET['action']) || !isset($_GET['request_id'])) {
    http_response_code(400); // Bad Request
    echo "Неверные параметры.";
    exit();
}

$action = $_GET['action'];
$requestID = intval($_GET['request_id']);

// Проверка корректности значения action
if (!in_array($action, ['approve', 'reject'])) {
    http_response_code(400); // Bad Request
    echo "Неверное действие.";
    exit();
}

// Получаем информацию о заявке
$requestQuery = "
    SELECT *
    FROM users_pending_approval
    WHERE RequestID = '$requestID'
";
$requestResult = mysqli_query($connect, $requestQuery);

if (mysqli_num_rows($requestResult) != 1) {
    http_response_code(404); // Not Found
    echo "Заявка не найдена.";
    exit();
}

$request = mysqli_fetch_assoc($requestResult);

$UserID = $request['UserID'];
$ProjectID = $request['ProjectID'];
$TaskID = $request['TaskID'];
$CurrentStatus = $request['Status'];

if ($action == 'approve') {
    // Проверяем, не была ли заявка уже одобрена
    if ($CurrentStatus == 'Одобрена') {
        echo "Заявка уже одобрена.";
        exit();
    }

    // Добавляем участие в таблицу user_tasks
    $insertQuery = "
        INSERT INTO user_tasks (UserID, TaskID, ProjectID)
        VALUES ('$UserID', '$TaskID', '$ProjectID')
    ";
    if (!mysqli_query($connect, $insertQuery)) {
        http_response_code(500); // Internal Server Error
        echo "Ошибка при добавлении участия: " . mysqli_error($connect);
        exit();
    }

    // Обновляем статус заявки на "Одобрена"
    $updateStatusQuery = "
        UPDATE users_pending_approval
        SET Status = 'Одобрена'
        WHERE RequestID = '$requestID'
    ";
    if (!mysqli_query($connect, $updateStatusQuery)) {
        http_response_code(500); // Internal Server Error
        echo "Ошибка при обновлении статуса заявки: " . mysqli_error($connect);
        exit();
    }

    echo "Заявка успешно одобрена.";
} elseif ($action == 'reject') {
    // Проверяем, не была ли заявка уже отклонена
    if ($CurrentStatus == 'Отклонена') {
        echo "Заявка уже отклонена.";
        exit();
    }

    // Обновляем статус заявки на "Отклонена"
    $updateStatusQuery = "
        UPDATE users_pending_approval
        SET Status = 'Отклонена'
        WHERE RequestID = '$requestID'
    ";
    if (!mysqli_query($connect, $updateStatusQuery)) {
        http_response_code(500); // Internal Server Error
        echo "Ошибка при обновлении статуса заявки: " . mysqli_error($connect);
        exit();
    }

    echo "Заявка успешно отклонена.";
}

mysqli_close($connect);
?>
