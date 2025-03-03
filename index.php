<?php
session_start();
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']); // Удаляем сообщение после отображения
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <link rel="stylesheet" href="styleindex.css">
    <script>
        function validateForm() {
            var username = document.getElementById('username').value.trim();
            var password = document.getElementById('password').value.trim();
            var errorMessage = '';

            if (username === '') {
                errorMessage += 'Имя пользователя не может быть пустым.<br>';
            }

            if (password === '') {
                errorMessage += 'Пароль не может быть пустым.<br>';
            }

            var errorContainer = document.getElementById('clientError');
            if (errorMessage !== '') {
                errorContainer.innerHTML = errorMessage;
                errorContainer.style.display = 'block';
                return false; // Предотвращает отправку формы
            } else {
                errorContainer.innerHTML = '';
                errorContainer.style.display = 'none';
                return true; // Разрешает отправку формы
            }
        }
    </script>
    <style>
        .error-message, .client-error {
            color: red;
            margin-bottom: 15px;
        }
        .client-error {
            display: none; /* Скрываем контейнер по умолчанию */
        }
        .bg-video {
            /* Фиксируем видеоролик, чтобы он занимал всю площадь страницы
            от верхнего левого угла */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            /* Определяем, что все другие элементы на странице будут поверх видео */
            z-index: -100;
        }
    </style>
</head>
<body>
   
        
    <section class = form>
        <div class="container">
            <h2>Авторизация</h2>
            <?php if(!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <!-- Контейнер для клиентских ошибок -->
            <div id="clientError" class="client-error"></div>
            <form id="loginForm" method="post" action="func/check.php" onsubmit="return validateForm();">
                <label for="username">Имя пользователя:</label><br>
                <input type="text" id="username" name="username"><br>
                <label for="password">Пароль:</label><br>
                <input type="password" id="password" name="password"><br><br>
                <input class='joinBut' type="submit" value="Войти">
            </form>
            <p>Нет аккаунта, <a href="registration/registerform.php">зарегистрируйся!</a></p>
        </div>
    </section>
</body>
</html>
