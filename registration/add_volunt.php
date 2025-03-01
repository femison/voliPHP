<?php
// Подключение к базе данных
require_once $_SERVER['DOCUMENT_ROOT'] . '/21is/func/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $userName = $_POST['name'];
    $userSurname = $_POST['surname'];
    $userEmail = $_POST['email'];
    $userPhone = $_POST['phone'];
    $userDOB = $_POST['dob'];
    $userGender = $_POST['gender'];
    $userAddress = $_POST['address'];
    $userSkills = $_POST['skills'];
    $userLogin = $_POST['login'];
    $userPassword = $_POST['password']; // Пароль из формы

    // Фиксируем роль как 'Волонтер'
    $userRole = 'Волонтер';

    // Проверка уникальности логина
    $checkLoginQuery = "SELECT * FROM usercredentials WHERE Login = ?";
    $stmtCheckLogin = mysqli_prepare($connect, $checkLoginQuery);
    mysqli_stmt_bind_param($stmtCheckLogin, "s", $userLogin);
    mysqli_stmt_execute($stmtCheckLogin);
    mysqli_stmt_store_result($stmtCheckLogin);

    if (mysqli_stmt_num_rows($stmtCheckLogin) > 0) {
        
        
    } else {
        // Логин уникален, продолжаем регистрацию

        // Подготовленный запрос на вставку данных в таблицу users
        $stmt = mysqli_prepare($connect, "INSERT INTO users (Name, Surname, Email, Phone, DateOfBirth, Gender, Address, Role, UserSkills) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssssssss", $userName, $userSurname, $userEmail, $userPhone, $userDOB, $userGender, $userAddress, $userRole, $userSkills);
        mysqli_stmt_execute($stmt);

        // Получаем ID последнего добавленного пользователя
        $lastUserID = mysqli_insert_id($connect);
        mysqli_stmt_close($stmt);

        // Хэшируем пароль для безопасности
        $hashedPassword = password_hash($userPassword, PASSWORD_BCRYPT);

        // Вставка логина и пароля в таблицу usercredentials
        $stmt2 = mysqli_prepare($connect, "INSERT INTO usercredentials (UserID, Login, Password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt2, "iss", $lastUserID, $userLogin, $hashedPassword);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        // Перенаправляем на страницу
        header("Location: ../adminform.php");
        exit();
    }

    // Закрытие подготовленного выражения для проверки логина
    mysqli_stmt_close($stmtCheckLogin);
}
?>
