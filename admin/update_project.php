<?php
// Подключение к базе данных
require_once('../func/db_connection.php');

// Получаем данные из POST запроса
$projectId = $_POST['projectId'];
$projectName = $_POST['projectName'];
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];
$status = $_POST['status'];

// Подготовка SQL запроса
$query = "UPDATE projects SET ProjectName='$projectName', StartDate='$startDate', EndDate='$endDate', Status='$status' WHERE ProjectID='$projectId'";

// Выполнение запроса
if (mysqli_query($connect, $query)) {
    echo "Данные успешно обновлены";
} else {
    echo "Ошибка при обновлении данных: " . mysqli_error($connect);
}

// Закрытие соединения с базой данных
mysqli_close($connect);


?>
