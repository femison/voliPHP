<?php
session_start();
require '../func/db_connection.php';
require '../func/function.php'; // Убедитесь, что этот файл содержит необходимые функции

// Проверка авторизации и роли
if (!isset($_SESSION["username"])) {
    header("Location: ../login.php");
    exit();
}

$currentUsername = mysqli_real_escape_string($connect, $_SESSION["username"]);
$adminProfileQuery = "
    SELECT u.UserID, u.Name, u.Surname, u.Email, u.Phone
    FROM users u
    JOIN usercredentials uc ON u.UserID = uc.UserID
    WHERE uc.Login = '$currentUsername' AND u.Role = 'Администратор'
    LIMIT 1
";
$adminProfileResult = mysqli_query($connect, $adminProfileQuery);

if ($adminProfileResult && mysqli_num_rows($adminProfileResult) === 1) {
    $adminProfile = mysqli_fetch_assoc($adminProfileResult);
} else {
    die("Информация о профиле администратора не найдена.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение и экранирование данных из формы
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $surname = mysqli_real_escape_string($connect, $_POST['surname']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $phone = mysqli_real_escape_string($connect, $_POST['phone']);
    // Добавьте другие поля по необходимости

    // Обновление данных в базе
    $updateQuery = "
        UPDATE users
        SET Name = '$name',
            Surname = '$surname',
            Email = '$email',
            Phone = '$phone'
            -- Добавьте другие поля по необходимости
        WHERE UserID = '{$adminProfile['UserID']}'
    ";

    if (mysqli_query($connect, $updateQuery)) {
        // Успешное обновление, перенаправление назад с сообщением
        echo "<script>
                window.location.href = '../adminform.php';
               
              </script>";
    } else {
        // Ошибка при обновлении, перенаправление назад с сообщением об ошибке
        $error = htmlspecialchars(mysqli_error($connect), ENT_QUOTES, 'UTF-8');
        echo "<script>
                alert('Ошибка при обновлении профиля: $error');
                window.history.back();
              </script>";
    }
} else {
    // Неверный метод запроса
    echo "<script>
            alert('Неверный метод запроса.');
            window.history.back();
          </script>";
}

mysqli_close($connect);
?>