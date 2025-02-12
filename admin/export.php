<?php
require_once('db_connection.php'); // Подключение файла с настройками базы данных

// Формирование SQL-запроса
$query1 = "SELECT u.UserID, u.Name as 'Имя', u.Surname as 'Фамилия', COUNT(DISTINCT ut.ProjectID) AS 'Количество проектов'
FROM users u
JOIN user_tasks ut ON u.UserID = ut.UserID
GROUP BY u.UserID, u.Name, u.Surname
ORDER BY u.UserID;";

$result1 = $connect->query($query1);

