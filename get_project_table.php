<?php
// Подключаем файл с соединением с базой данных
require 'func/db_connection.php'; 

// Получаем ID выбранного проекта из параметра запроса
$projectID = isset($_GET['projectID']) ? intval($_GET['projectID']) : 0;

if ($projectID > 0) {
    // Запрос к базе данных для получения данных проекта
    $sql = "SELECT 
    CONCAT(u.Name, ' ', u.Surname) AS FullName,  -- Объединяем имя и фамилию
    t.Description AS TaskDescription,   
    p.ProjectName               -- Название проекта
FROM 
    voli.user_tasks ut
JOIN 
    voli.tasks t ON ut.TaskID = t.TaskID
JOIN 
    voli.projects p ON t.ProjectID = p.ProjectID
JOIN
    voli.users u ON ut.UserID = u.UserID   
WHERE 
    p.ProjectID = ?;         
";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Получение названия проекта
        $row = $result->fetch_assoc();
        $projectName = $row['ProjectName'];

        // Вывод названия проекта
        echo '<h2>Проект: ' . htmlspecialchars($projectName) . '</h2>';

        // Возвращаемся к результатам запроса
        $stmt->execute();
        $result = $stmt->get_result();

        // Формирование таблицы с данными
        echo '<table>';
        echo '<thead>';
        echo '<tr><th>Описание задачи</th><th>Полное имя</th></tr>';
        echo '</thead>';
        echo '<tbody>';

        // Вывод данных из результата запроса
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['TaskDescription']) . '</td>';
            echo '<td>' . htmlspecialchars($row['FullName']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo "Нет данных для этого проекта.";
    }

    $stmt->close();
} else {
    echo "Неверный ID проекта.";
}

// Закрытие соединения
$connect->close();
?>
