<?php


// Подключение к базе данных
require_once('../func/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $projectName = $_POST['projectName'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $status = $_POST['status'];

    // Подготовленный запрос на вставку данных в таблицу projects
    $stmt = mysqli_prepare($connect, "INSERT INTO projects (ProjectName, StartDate, EndDate, Status) VALUES (?, ?, ?, ?)");

    // Привязываем параметры к подготовленному выражению
    mysqli_stmt_bind_param($stmt, "ssss", $projectName, $startDate, $endDate, $status);

    // Выполняем запрос
    mysqli_stmt_execute($stmt);

    // Закрываем подготовленное выражение
    mysqli_stmt_close($stmt);

    // Перенаправляем обратно на главную страницу
    header("Location: ../adminform.php");
    exit();
}
?>
