<?php
// Подключение к базе данных
require_once $_SERVER['DOCUMENT_ROOT'] . '/21is/func/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $userName = $_POST['userName'];
    $userSurname = $_POST['userSurname'];
    $userEmail = $_POST['userEmail'];
    $userPhone = $_POST['userPhone'];
    $userDOB = $_POST['userDOB'];
    $userGender = $_POST['userGender'];
    $userRole = $_POST['userRole'];
    $userAddress = $_POST['userAddress'];
    $userSkills = $_POST['userSkills'];

    // Подготовленный запрос на вставку данных в таблицу users
    $stmt = mysqli_prepare($connect, "INSERT INTO users (Name, Surname, Email, Phone, DateOfBirth, Gender, Address, Role, UserSkills) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Привязываем параметры к подготовленному выражению
    mysqli_stmt_bind_param($stmt, "sssssssss", $userName, $userSurname, $userEmail, $userPhone, $userDOB, $userGender, $userAddress, $userRole, $userSkills);

    // Выполняем запрос
    mysqli_stmt_execute($stmt);

    // Получаем ID последнего добавленного пользователя
    $lastUserID = mysqli_insert_id($connect);

    // Закрываем подготовленное выражение
    mysqli_stmt_close($stmt);

    
    header("Location: ../adminform.php");
    exit();
}
?>
