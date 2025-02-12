<?php
require_once('db_connection.php');

// Получение всех пользователей из базы данных
$query = "SELECT UserID, Name, Surname FROM users";
$result = mysqli_query($connect, $query);

// Проверка на наличие результатов
if (mysqli_num_rows($result) > 0) {
    // Создание начала тега select
    $selectHTML = '<select id="userID" name="userID" required>';

    // Добавление опций в выпадающий список
    while ($row = mysqli_fetch_assoc($result)) {
        $selectHTML .= '<option value="' . htmlspecialchars($row['UserID']) . '">' . htmlspecialchars($row['Name']) . ' ' . htmlspecialchars($row['Surname']) . '</option>';
    }

    // Закрытие тега select
    $selectHTML .= '</select>';
} else {
    $selectHTML = 'Нет доступных пользователей для выбора.';
}
?>