<?php


require_once('../func/db_connection.php');

// Обработчик удаления проекта
if (isset($_POST['deleteProjectId'])) {
    $projectId = $_POST['deleteProjectId'];
    // Подготавливаем запрос на удаление проекта
    $deleteProjectQuery = $connect->prepare("DELETE FROM projects WHERE ProjectID = ?");
    // Привязываем параметр (ID проекта)
    $deleteProjectQuery->bind_param("i", $projectId);
    // Выполняем запрос
    if ($deleteProjectQuery->execute()) {
        // echo "Проект успешно удален.";
    } else {
        echo "Ошибка при удалении проекта: " . $connect->error;
    }
    exit; // Завершаем скрипт после удаления проекта
}

// Закрытие соединения с базой данных
mysqli_close($connect);

?>
