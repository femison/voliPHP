<?php
require_once('../func/db_connection.php');

$userId = $_POST['userId'] ?? null;
$login = $_POST['login'] ?? ''; 
$password = $_POST['password'] ?? '';

if ($userId) {
    // Хэшируем пароль перед вставкой или обновлением
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Проверка существует ли уже запись для данного пользователя
    $checkQuery = "SELECT UserId FROM usercredentials WHERE UserId = ?";
    $checkStmt = $connect->prepare($checkQuery);
    if (!$checkStmt) {
        die('Ошибка подготовки запроса: ' . $connect->error);
    }
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        // Запись существует, выполнить обновление
        $updateQuery = "UPDATE usercredentials SET Login = ?, Password = ? WHERE UserId = ?";
        $updateStmt = $connect->prepare($updateQuery);
        if (!$updateStmt) {
            die('Ошибка подготовки запроса: ' . $connect->error);
        }
        $updateStmt->bind_param("ssi", $login, $hashedPassword, $userId);
        $updateStmt->execute();

        if ($updateStmt->affected_rows > 0) {
            // Успешное обновление
        } else {
            echo "Нет изменений для обновления.";
        }
        $updateStmt->close();
    } else {
        // Запись не существует, выполнить вставку
        $insertQuery = "INSERT INTO usercredentials (UserId, Login, Password) VALUES (?, ?, ?)";
        $insertStmt = $connect->prepare($insertQuery);
        if (!$insertStmt) {
            die('Ошибка подготовки запроса: ' . $connect->error);
        }
        $insertStmt->bind_param("iss", $userId, $login, $hashedPassword);
        $insertStmt->execute();

        if ($insertStmt->affected_rows > 0) {
            // Успешная вставка
        } else {
            echo "Ошибка добавления логина и пароля.";
        }
        $insertStmt->close();
    }
    $checkStmt->close();
} else {
    echo "Не указан ID пользователя.";
}
header("Location: ../adminform.php");
?>
