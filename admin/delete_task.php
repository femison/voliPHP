<?php
require_once('../func/db_connection.php');

// Обработчик удаления задачи
if (isset($_POST['deleteTaskId'])) {
    $taskId = $_POST['deleteTaskId'];

    // Начинаем транзакцию
    $connect->begin_transaction();

    try {
        // Подготавливаем запрос на удаление информации о задаче
        $deleteTaskInfoQuery = $connect->prepare("DELETE FROM taskinfo WHERE TaskID = ?");
        $deleteTaskInfoQuery->bind_param("i", $taskId);

        // Выполняем запрос
        if (!$deleteTaskInfoQuery->execute()) {
            throw new Exception("Ошибка при удалении информации о задаче: " . $connect->error);
        }

        // Подготавливаем запрос на удаление задачи
        $deleteTaskQuery = $connect->prepare("DELETE FROM tasks WHERE TaskID = ?");
        $deleteTaskQuery->bind_param("i", $taskId);

        // Выполняем запрос
        if (!$deleteTaskQuery->execute()) {
            throw new Exception("Ошибка при удалении задачи: " . $connect->error);
        }

        // Если оба запроса успешны, подтверждаем транзакцию
        $connect->commit();
        echo "Задача успешно удалена.";
    } catch (Exception $e) {
        // В случае ошибки откатываем транзакцию
        $connect->rollback();
        echo $e->getMessage(); // Вывод сообщения об ошибке
    }
}

// Закрытие соединения с базой данных
mysqli_close($connect);
?>
