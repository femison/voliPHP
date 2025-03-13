<?php 
include('db_connection.php');

$errors = [];
$old_input = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $role = 'Волонтер';

    // Сохраняем введённые данные для повторного отображения
    $old_input = [
        'name' => $name,
        'surname' => $surname,
        'email' => $email,
        'phone' => $phone,
        'city' => $city,
        'skills' => $skills,
        'login' => $login,
        'dob' => $dob,
        'gender' => $gender
    ];

    // Проверка обязательных полей
    if (empty($name)) {
        $errors['name'] = 'Поле "Имя" обязательно для заполнения*';
    }
    if (empty($surname)) {
        $errors['surname'] = 'Поле "Фамилия" обязательно для заполнения*';
    }
    if (empty($email)) {
        $errors['email'] = 'Поле "Email" обязательно для заполнения*';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный формат email';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Поле "Телефон" обязательно для заполнения*';
    }
    if (empty($city)) {
        $errors['city'] = 'Поле "Населенный пункт" обязательно для заполнения*';
    }
    if (empty($login)) {
        $errors['login'] = 'Поле "Логин" обязательно для заполнения*';
    }
    if (empty($password)) {
        $errors['password'] = 'Поле "Пароль" обязательно для заполнения*';
    }
    if (empty($dob)) {
        $errors['dob'] = 'Поле "Дата рождения" обязательно для заполнения*';
    }
    if (empty($gender)) {
        $errors['gender'] = 'Поле "Пол" обязательно для выбора*';
    }

    // Если ошибок нет, выполняем запрос к базе данных
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $connect->prepare("INSERT INTO users (Name, Surname, UserSkills, Email, Phone, DateOfBirth, Gender, Address, Role) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $name, $surname, $skills, $email, $phone, $dob, $gender, $city, $role);

        if ($stmt->execute()) {
            $userId = $connect->insert_id;
            $stmt2 = $connect->prepare("INSERT INTO usercredentials (UserID, Login, Password) VALUES (?, ?, ?)");
            $stmt2->bind_param("iss", $userId, $login, $hashed_password);

            if ($stmt2->execute()) {
                header("Location: ../index.php");
                exit;
            } else {
                echo "<script>alert('Ошибка при добавлении учетных данных: " . $stmt2->error . "');</script>";
            }
            $stmt2->close();
        } else {
            echo "<script>alert('Ошибка при добавлении пользователя: " . $stmt->error . "');</script>";
        }
        $stmt->close();
        mysqli_close($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="registerstyle.css">
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.6/dist/inputmask.min.js"></script>
    <script src="datepicker.js" defer></script>
</head>
<body>
<div class="bg"></div>
<div class="container">
    <div style="display: flex; justify-content:center; padding-top:3%;"><h1>Регистрация</h1></div>
    <form class="grid-form" method="POST" novalidate>
        <div>
            <input type="text" name="name" placeholder="Имя" required maxlength="20"
                   value="<?= htmlspecialchars($old_input['name'] ?? '') ?>" class="<?= isset($errors['name']) ? 'error-input' : '' ?>">
            <?php if(isset($errors['name'])): ?>
                <div class="error"><?= $errors['name'] ?></div>
            <?php endif; ?>
        </div>
        <div>
            <input type="text" name="surname" placeholder="Фамилия" required maxlength="20"
                   value="<?= htmlspecialchars($old_input['surname'] ?? '') ?>" class="<?= isset($errors['surname']) ? 'error-input' : '' ?>">
            <?php if(isset($errors['surname'])): ?>
                <div class="error"><?= $errors['surname'] ?></div>
            <?php endif; ?>
        </div>
        <div class="full-width">
            <input type="text" readonly id="dob" name="dob" class="datepicker-here" placeholder="Дата рождения" required
                   value="<?= htmlspecialchars($old_input['dob'] ?? '') ?>" class="<?= isset($errors['dob']) ? 'error-input' : '' ?>">
            <?php if(isset($errors['dob'])): ?>
                <div class="error"><?= $errors['dob'] ?></div>
            <?php endif; ?>
        </div>
        <div class="full-width">
            <input type="email" name="email" placeholder="Email" required maxlength="50"
                   value="<?= htmlspecialchars($old_input['email'] ?? '') ?>" class="<?= isset($errors['email']) ? 'error-input' : '' ?>">
            <?php if(isset($errors['email'])): ?>
                <div class="error"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>
        <div class="full-width">
            <input type="tel" name="phone" id="phone" placeholder="Телефон" required maxlength="18"
                   value="<?= htmlspecialchars($old_input['phone'] ?? '') ?>" class="<?= isset($errors['phone']) ? 'error-input' : '' ?>">
            <?php if(isset($errors['phone'])): ?>
                <div class="error"><?= $errors['phone'] ?></div>
            <?php endif; ?>
        </div>
        <div class="full-width">
            <input type="text" name="city" placeholder="Населенный пункт" maxlength="30"
                   value="<?= htmlspecialchars($old_input['city'] ?? '') ?>" class="<?= isset($errors['city']) ? 'error-input' : '' ?>">
            <?php if(isset($errors['city'])): ?>
                <div class="error"><?= $errors['city'] ?></div>
            <?php endif; ?>
        </div>
        <div class="full-width">
            <input type="text" name="skills" placeholder="Навыки и пожелания" maxlength="30"
                   value="<?= htmlspecialchars($old_input['skills'] ?? '') ?>" class="<?= isset($errors['skills']) ? 'error-input' : '' ?>">
            <?php if(isset($errors['skills'])): ?>
                <div class="error"><?= $errors['skills'] ?></div>
            <?php endif; ?>
        </div>
        <div>
            <input type="text" name="login" placeholder="Логин" maxlength="15" required
                   value="<?= htmlspecialchars($old_input['login'] ?? '') ?>" class="<?= isset($errors['login']) ? 'error-input' : '' ?>">
            <?php if(isset($errors['login'])): ?>
                <div class="error"><?= $errors['login'] ?></div>
            <?php endif; ?>
        </div>
        <div>
            <input type="password" name="password" id="password" placeholder="Пароль" maxlength="15" required
                   class="<?= isset($errors['password']) ? 'error-input' : '' ?>">
            <?php if(isset($errors['password'])): ?>
                <div class="error"><?= $errors['password'] ?></div>
            <?php endif; ?>
            <label style="display: block; padding:10px 0px; cursor:pointer">
                <input type="checkbox" onclick="togglePassword()"> Показать пароль
            </label>
        </div>
        <div class="full-width" style="display: flex; gap: 5px;">
            <label class="rad-but">
                <input type="radio" name="gender" value="м" required
                    <?= ($old_input['gender'] ?? '') === 'м' ? 'checked' : '' ?>> Мужской
            </label>
            <label class="rad-but">
                <input type="radio" name="gender" value="ж"
                    <?= ($old_input['gender'] ?? '') === 'ж' ? 'checked' : '' ?>> Женский
            </label>
            <?php if(isset($errors['gender'])): ?>
                <div class="error"><?= $errors['gender'] ?></div>
            <?php endif; ?>
        </div>
        <div class="full-width-button-group">
            <button class="btn" type="submit" style="cursor: pointer;">
                Зарегистрироваться
                <div class="arrow-wrapper"><div class="arrow"></div></div>
            </button>
            <a class="btn" href="../MainPage/index.php">
                Вернуться на главную
                <div class="arrow-wrapper"><div class="arrow"></div></div>
            </a>
        </div>
        <div style="display: flex; justify-content:center;align-items:center;">
            <p>Уже есть аккаунт? - <a href="../index.php">Войти</a></p>
        </div>
    </form>
</div>
<div class="transparent-wrapper">
    <div class="volunteer-info">
        <h2>Стань волонтёром!</h2>
        <p>Волонтёры — это сердце нашей организации. Присоединяйтесь к нам, чтобы помогать людям, участвовать в интересных проектах и менять мир к лучшему. Ваше время и навыки могут сделать чью-то жизнь ярче!</p>
        <p>Мы ценим каждого, кто готов внести свой вклад. Независимо от опыта, вы найдёте у нас место, где сможете реализовать себя и почувствовать свою значимость.</p>
    </div>
</div>

<script>
function togglePassword() {
    const password = document.getElementById('password');
    password.type = password.type === 'password' ? 'text' : 'password';
}

document.addEventListener('DOMContentLoaded', () => {
    new Inputmask('+7(999) 999-99-99').mask(document.getElementById('phone'));
});
</script>
</body>
</html>