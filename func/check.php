<?php
session_start(); // session_start() должен быть только здесь!

// Подключение к базе данных
include 'db_connection.php'; // Подключаем файл с функцией подключения к БД

// Проверка соединения
if (mysqli_connect_errno()) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

// Обработка данных из формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Запрос к базе данных для проверки пользователя
    $query = "SELECT * FROM usercredentials WHERE Login = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("s", $username); // Защита от SQL инъекций
    $stmt->execute();
    $result = $stmt->get_result();

    // Проверка наличия пользователя в базе данных
    if ($result->num_rows == 1) {
        // Получаем данные пользователя
        $user_row = $result->fetch_assoc();
        $storedPasswordHash = $user_row['Password']; // Хэш пароля из базы данных
        $userID = $user_row['UserID'];

        // Проверка пароля с хэшом
        if (password_verify($password, $storedPasswordHash)) {
            // Получение роли пользователя из таблицы users
            $role_query = "SELECT Role FROM users WHERE UserID = ?";
            $role_stmt = $connect->prepare($role_query);
            $role_stmt->bind_param("i", $userID);
            $role_stmt->execute();
            $role_result = $role_stmt->get_result();
            $role_row = $role_result->fetch_assoc();
            $role = $role_row['Role'];

            // Пользователь аутентифицирован
            $_SESSION["username"] = $username;
            $_SESSION["userID"] = $userID; // Сохранение ID пользователя в сессии

            // Перенаправление в зависимости от роли
            if ($role == "Администратор") {
                header("Location: ../adminform.php");
                exit;
            } elseif ($role == "Волонтер") {
                header("Location: ../voliform.php");
                exit;
            } else {
                echo "Неизвестная роль пользователя."; // Если роль неизвестна
            }
        } else {
            // Неверный пароль, установка сообщения в сессии
            $_SESSION['error_message'] = "Неправильное имя пользователя или пароль.";
            header("Location: ../index.php"); // Перенаправление на страницу входа
            exit;
        }
    } else {
        // Неверное имя пользователя
        $_SESSION['error_message'] = "Неправильное имя пользователя или пароль.";
        header("Location: ../index.php"); // Перенаправление на страницу входа
        exit;
    }
}

// Закрытие соединения с базой данных
mysqli_close($connect);
?>
