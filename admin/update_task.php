<?php
// Подключение к базе данных
require_once('../func/db_connection.php');

if (isset($_POST['taskId'], $_POST['description'], $_POST['location'], $_POST['date'], $_POST['status'])) {
    $taskId = $_POST['taskId'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    // Начинаем транзакцию
    $connect->begin_transaction();

    try {
        // Обновление данных в таблице tasks
        $queryTasks = $connect->prepare("UPDATE tasks SET Description=?, Status=? WHERE TaskID=?");
        $queryTasks->bind_param("ssi", $description, $status, $taskId);
        $queryTasks->execute();
        $queryTasks->close();

        // Обновление данных в таблице taskinfo
        $queryTaskInfo = $connect->prepare("UPDATE taskinfo SET Location=?, Date=? WHERE TaskID=?");
        $queryTaskInfo->bind_param("ssi", $location, $date, $taskId);
        $queryTaskInfo->execute();
        $queryTaskInfo->close();

        // Фиксируем транзакцию
        $connect->commit();
        echo "Данные задачи успешно обновлены";
    } catch (Exception $e) {
        // Откатываем транзакцию в случае ошибки
        $connect->rollback();
        echo "Ошибка при обновлении данных: " . $e->getMessage();
    }

    // Закрытие соединения с базой данных
    $connect->close();
} else {
    echo "Ошибка: Не все данные получены";
}

?>
