<?php
require_once('db_connection.php');

// Обработчик удаления пользователя
if (isset($_POST['deleteUserId'])) {
    $userId = $_POST['deleteUserId'];

    try {
        // Подготавливаем запрос на удаление пользователя
        $deleteUserQuery = $connect->prepare("DELETE FROM users WHERE UserID = ?");
        $deleteUserQuery->bind_param("i", $userId);

        // Выполняем запрос
        if (!$deleteUserQuery->execute()) {
            throw new Exception("Ошибка при удалении пользователя: " . $connect->error);
        }

        echo "Пользователь успешно удален вместе со всеми связанными данными.";
    } catch (Exception $e) {
        // Вывод сообщения об ошибке, если запрос не удался
        echo $e->getMessage();
    }

    // Закрытие соединения с базой данных
    $deleteUserQuery->close();
    mysqli_close($connect);
}
?>