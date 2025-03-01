<?php
require 'db_connection.php'; // Подключение к базе данных
require 'add_volunt.php';

// Инициализация переменной для ошибки логина
$loginError = '';

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
        // Логин уже существует, устанавливаем ошибку
        $loginError = "Этот логин уже занят. Пожалуйста, выберите другой.";
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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация волонтера</title>
    <link rel="stylesheet" href="registerstyle.css">
    
    <!-- Подключаем jQuery и jQuery Mask Plugin -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
</head>
<body>
<section>
    <div class="register-input">
        <h1>Регистрация волонтера</h1>
        
        <form action="registerform.php" method="POST">
            <input type="text" name="name" placeholder="Введите ваше имя" required><br>
            <input type="text" name="surname" placeholder="Введите вашу фамилию" required><br>
            <input type="email" name="email" placeholder="Введите ваш email" required><br>
            <input type="tel" name="phone" id="phone" placeholder="Введите ваш номер телефона" required><br>
            <input type="text" name="address" placeholder="Введите ваш адрес" required><br>
            <input type="text" name="skills" placeholder="Пожелания в работе" required><br>
            
            <!-- Поле логина с выводом ошибки -->
            <input type="text" name="login" placeholder="Придумайте логин" required><br>
            <?php if ($loginError): ?>
                <div style="color: red;"><?php echo $loginError; ?></div>
            <?php endif; ?>

            <input type="password" name="password" id="password" placeholder="Придумайте пароль" required><br>
            <input type="checkbox" id="watchpas" name="watchpas" onclick="togglePasswordVisibility()">
            <label for="watchpas">Показать пароль</label><br>
            <input type="date" name="dob" required><br>
            <label for="gender_m">Мужской</label>
            <input type="radio" name="gender" value="м" id="gender_m" required>
            <label for="gender_f">Женский</label>
            <input type="radio" name="gender" value="ж" id="gender_f" required><br>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <button type="button" style="background-color:red;" onclick="window.location.href='../index.php';">Отмена</button>
    </div>                   
</section>

<script>
    function togglePasswordVisibility() {
        var passwordField = document.getElementById('password');
        var checkbox = document.getElementById('watchpas');
        if (checkbox.checked) {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }

    $(document).ready(function() {
        $('#phone').mask('+7(999) 999-99-99', {
            placeholder: '+7(___) ___-__-__'
        });
    });
</script>

</body>
</html>
