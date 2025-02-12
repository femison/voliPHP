<?php
// Подключение к базе данных
require_once('../func/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $taskDescription = $_POST['taskDescription'];
    $projectID = $_POST['projectID'];
    $taskStatus = $_POST['taskStatus'];
    $taskLocation = $_POST['taskLocation'];
    $taskDate = $_POST['taskDate'];

    // Подготовленный запрос на вставку данных в таблицу tasks
    $stmt = mysqli_prepare($connect, "INSERT INTO tasks (ProjectID, Description, Status) VALUES (?, ?, ?)");

    // Привязываем параметры к подготовленному выражению
    mysqli_stmt_bind_param($stmt, "iss", $projectID, $taskDescription, $taskStatus);

    // Выполняем запрос
    mysqli_stmt_execute($stmt);

    // Получаем ID последней вставленной задачи
    $lastTaskID = mysqli_insert_id($connect);

    // Закрываем подготовленное выражение
    mysqli_stmt_close($stmt);

    // Вставляем данные в таблицу taskinfo
    $stmt_taskinfo = mysqli_prepare($connect, "INSERT INTO taskinfo (TaskID, Location, Date) VALUES (?, ?, ?)");

    // Привязываем параметры к подготовленному выражению
    mysqli_stmt_bind_param($stmt_taskinfo, "iss", $lastTaskID, $taskLocation, $taskDate);

    // Выполняем запрос
    mysqli_stmt_execute($stmt_taskinfo);

    // Закрываем подготовленное выражение
    mysqli_stmt_close($stmt_taskinfo);

    // Перенаправляем пользователя на вкладку "Задачи"
    header("Location: ../adminform.php");
    exit();
}
?>
