<?php
require 'db_connection.php'; // Подключение к базе данных

$errors = [];
$old_input = [];
$address_fields = ['region', 'city', 'street', 'house', 'apartment'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Сбор данных
    $fields = array_merge(
        [
            'name' => trim($_POST['name'] ?? ''),
            'surname' => trim($_POST['surname'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'dob' => trim($_POST['dob'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'skills' => trim($_POST['skills'] ?? ''),
            'login' => trim($_POST['login'] ?? ''),
            'password' => $_POST['password'] ?? ''
        ],
        array_combine(
            $address_fields,
            array_map(fn($f) => trim($_POST[$f] ?? ''), $address_fields)
        )
    );

    // Санитизация
    foreach ($fields as $key => $value) {
        $old_input[$key] = htmlspecialchars($value);
        $fields[$key] = mysqli_real_escape_string($connect, $value);
    }

    // Валидация
    $validations = [
        'name' => [
            'pattern' => '/^[А-Яа-я\s\-]{2,20}$/u',
            'message' => 'Имя: 2-20 символов (только русские буквы, пробелы, дефисы)'
        ],
        'surname' => [
            'pattern' => '/^[А-Яа-я\s\-]{2,20}$/u',
            'message' => 'Фамилия: 2-20 символов (только русские буквы, пробелы, дефисы)'
        ],
        'email' => [
            'filter' => FILTER_VALIDATE_EMAIL,
            'message' => 'Некорректный email'
        ],
        'phone' => [
            'pattern' => '/^\+7\(\d{3}\) \d{3}-\d{2}-\d{2}$/',
            'message' => 'Формат: +7(XXX) XXX-XX-XX'
        ],
        'region' => [
            'pattern' => '/^[А-Яа-я\s\-]{2,50}$/u',
            'message' => 'Область: 2-50 символов (только русские буквы, дефисы)'
        ],
        'city' => [
            'pattern' => '/^[А-Яа-я\s\-]{2,50}$/u',
            'message' => 'Город: 2-50 символов (только русские буквы, дефисы)'
        ],
        'street' => [
            'pattern' => '/^[А-Яа-я0-9\s\-\/]{2,50}$/u',
            'message' => 'Улица: 2-50 символов (только русские буквы, цифры, пробелы, дефисы)'
        ],
        'house' => [
            'pattern' => '/^[А-Яа-яA-Za-z0-9\/\-]{1,10}$/u',
            'message' => 'Дом: 1-10 символов (буквы, цифры, дефисы)'
        ],
        'apartment' => [
            'pattern' => '/^[0-9\-]{0,10}$/',
            'message' => 'Квартира: до 10 символов (цифры, дефисы)',
            'optional' => true
        ],
        'password' => [
            'pattern' => '/(?=.*\d)(?=.*[A-Z]).{6,50}/',
            'message' => 'Пароль: минимум 6 символов, 1 цифра и заглавная буква'
        ]
    ];

    foreach ($validations as $field => $rule) {
        if (empty($fields[$field])) {
            if (!isset($rule['optional'])) $errors[$field] = $rule['message'];
            continue;
        }

        if (isset($rule['filter'])) {
            if (!filter_var($fields[$field], $rule['filter'])) {
                $errors[$field] = $rule['message'];
            }
        } elseif (!preg_match($rule['pattern'], $fields[$field])) {
            $errors[$field] = $rule['message'];
        }
    }

    // Проверка даты рождения
    $min_dob = date('Y-m-d', strtotime('-16 years'));
    if ($fields['dob'] > $min_dob) {
        $errors['dob'] = 'Возраст должен быть не менее 16 лет';
    }

    // Проверка уникальности логина
    $stmt = mysqli_prepare($connect, "SELECT UserID FROM usercredentials WHERE Login = ?");
    mysqli_stmt_bind_param($stmt, "s", $fields['login']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $errors['login'] = 'Этот логин уже занят';
    }
    mysqli_stmt_close($stmt);

    // Если нет ошибок - регистрация
    if (empty($errors)) {
        // Формирование адреса
        $address = sprintf("Обл. %s, г. %s, ул. %s, д. %s",
            $fields['region'], $fields['city'],
            $fields['street'], $fields['house']
        );
        if (!empty($fields['apartment'])) {
            $address .= ", кв. " . $fields['apartment'];
        }

        // Сохранение пользователя
        $stmt = mysqli_prepare($connect,
            "INSERT INTO users (Name, Surname, Email, Phone, DateOfBirth, Gender, Address, Role, UserSkills)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'Волонтер', ?)");
        mysqli_stmt_bind_param($stmt, "ssssssss",
            $fields['name'], $fields['surname'], $fields['email'],
            $fields['phone'], $fields['dob'], $fields['gender'],
            $address, $fields['skills']);

        if (!mysqli_stmt_execute($stmt)) {
            echo "Ошибка при сохранении пользователя: " . mysqli_error($connect);
        }
        $user_id = mysqli_insert_id($connect);
        mysqli_stmt_close($stmt);

        // Сохранение учетных данных
        $hashed_password = password_hash($fields['password'], PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($connect,
            "INSERT INTO usercredentials (UserID, Login, Password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $fields['login'], $hashed_password);

        if (!mysqli_stmt_execute($stmt)) {
            echo "Ошибка при сохранении учетных данных: " . mysqli_error($connect);
        }
        mysqli_stmt_close($stmt);

        header("Location: ../index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация волонтера</title>
    <link rel="stylesheet" href="registerstyle.css">
</head>
<body>
    <div class="container">
        <h1>Регистрация волонтера</h1>
        <form class="grid-form" method="POST" novalidate>
            <!-- Основные поля -->
            <div>
                <input type="text" name="name" placeholder="Имя" required maxlength="20"
                       value="<?= $old_input['name'] ?? '' ?>" class="<?= isset($errors['name']) ? 'error-input' : '' ?>">
                <?php if(isset($errors['name'])): ?>
                    <div class="error"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>

            <div>
                <input type="text" name="surname" placeholder="Фамилия" required maxlength="20"
                       value="<?= $old_input['surname'] ?? '' ?>" class="<?= isset($errors['surname']) ? 'error-input' : '' ?>">
                <?php if(isset($errors['surname'])): ?>
                    <div class="error"><?= $errors['surname'] ?></div>
                <?php endif; ?>
            </div>

            <div class="full-width">
                <input type="email" name="email" placeholder="Email" required maxlength="50"
                       value="<?= $old_input['email'] ?? '' ?>" class="<?= isset($errors['email']) ? 'error-input' : '' ?>">
                <?php if(isset($errors['email'])): ?>
                    <div class="error"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="full-width">
                <input type="tel" name="phone" id="phone" placeholder="Телефон" required maxlength="18"
                       value="<?= $old_input['phone'] ?? '' ?>" class="<?= isset($errors['phone']) ? 'error-input' : '' ?>">
                <?php if(isset($errors['phone'])): ?>
                    <div class="error"><?= $errors['phone'] ?></div>
                <?php endif; ?>
            </div>

           
            <!-- Адрес -->
                <div class="full-width">
                    <div class="address-preview">
                        <button type="button" onclick="openModal()">Указать адрес</button>
                            <div class="address-preview-text">
                                <?php if(isset($old_input['region'])): ?>
                                    <?= htmlspecialchars(
                                        "Обл. {$old_input['region']}, г. {$old_input['city']}, " . 
                                        "ул. {$old_input['street']}, д. {$old_input['house']}" . 
                                        (!empty($old_input['apartment']) ? ", кв. {$old_input['apartment']}" : '')
                                    ) ?>
                                <?php else: ?>
                                    Нажмите для ввода адреса
                                <?php endif; ?>
                            </div>
                    </div>
                    <?php if(isset($errors['address'])): ?>
                        <div class="error"><?= $errors['address'] ?></div>
                    <?php endif; ?>
                </div>

            <!-- Модальное окно адреса -->
            <div id="addressModal" class="modal" hidden>
                <div class="modal-content">
                    <h3>Введите адрес</h3>
                    <div style="display: grid; gap: 15px;">
                        <div>
                            <input type="text" name="region" placeholder="Область" required maxlength="50"
                                   value="<?= $old_input['region'] ?? '' ?>" class="<?= isset($errors['region']) ? 'error-input' : '' ?>">
                            <?php if(isset($errors['region'])): ?>
                                <div class="error"><?= $errors['region'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <input type="text" name="city" placeholder="Город" required maxlength="50"
                                   value="<?= $old_input['city'] ?? '' ?>" class="<?= isset($errors['city']) ? 'error-input' : '' ?>">
                            <?php if(isset($errors['city'])): ?>
                                <div class="error"><?= $errors['city'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <input type="text" name="street" placeholder="Улица" required maxlength="50"
                                   value="<?= $old_input['street'] ?? '' ?>" class="<?= isset($errors['street']) ? 'error-input' : '' ?>">
                            <?php if(isset($errors['street'])): ?>
                                <div class="error"><?= $errors['street'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <input type="text" name="house" placeholder="Дом" required maxlength="10"
                                       value="<?= $old_input['house'] ?? '' ?>" class="<?= isset($errors['house']) ? 'error-input' : '' ?>">
                                <?php if(isset($errors['house'])): ?>
                                    <div class="error"><?= $errors['house'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <input type="text" name="apartment" placeholder="Квартира" maxlength="10"
                                       value="<?= $old_input['apartment'] ?? '' ?>" class="<?= isset($errors['apartment']) ? 'error-input' : '' ?>">
                                <?php if(isset($errors['apartment'])): ?>
                                    <div class="error"><?= $errors['apartment'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="full-width">
                <input type="text" name="skills" placeholder="Навыки и пожелания" maxlength="30" value="<?= $old_input['skills'] ?? '' ?>" class="<?= isset($errors['skills']) ? 'error-input' : '' ?>">
            </div>

            <div>
                <input type="text" name="login" placeholder="Логин" maxlength="15" required
                       value="<?= $old_input['login'] ?? '' ?>" class="<?= isset($errors['login']) ? 'error-input' : '' ?>">
                <?php if(isset($errors['login'])): ?>
                    <div class="error"><?= $errors['login'] ?></div>
                <?php endif; ?>
            </div>

            <div>
                <input type="password" name="password" id="password" placeholder="Пароль" maxlength="15" required class="<?= isset($errors['password']) ? 'error-input' : '' ?>">
                <?php if(isset($errors['password'])): ?>
                    <div class="error"><?= $errors['password'] ?></div>
                <?php endif; ?>
                <label style="display: block; margin-top: 10px;">
                    <input type="checkbox" onclick="togglePassword()"> Показать пароль
                </label>
            </div>

            <div class="full-width">
                <input type="date" name="dob" required 
                       max="<?= date('Y-m-d', strtotime('-16 years')) ?>"
                       value="<?= $old_input['dob'] ?? '' ?>" class="<?= isset($errors['dob']) ? 'error-input' : '' ?>">
                <?php if(isset($errors['dob'])): ?>
                    <div class="error"><?= $errors['dob'] ?></div>
                <?php endif; ?>
            </div>

            <div class="full-width" style="display: flex; gap: 20px;">
                <label>
                    <input type="radio" name="gender" value="м" required
                        <?= ($old_input['gender'] ?? '') === 'м' ? 'checked' : '' ?>> Мужской
                </label>
                <label>
                    <input type="radio" name="gender" value="ж"
                        <?= ($old_input['gender'] ?? '') === 'ж' ? 'checked' : '' ?>> Женский
                </label>
            </div>

            <div class="full-width button-group">
                <button type="submit">Зарегистрироваться</button>
                <button type="button" onclick="window.history.back()">Назад</button>
                
            </div>
        </form>
    </div>

    <script>
       function openModal() {
            document.getElementById('addressModal').removeAttribute('hidden');
        }

        function saveAddress() {
    const fields = ['region', 'city', 'street', 'house', 'apartment'];
    let valid = true;
    
    // Сброс предыдущих ошибок
    fields.forEach(field => {
        const errorDiv = document.querySelector(`.error[data-field="${field}"]`);
        if (errorDiv) errorDiv.remove();
    });

    // Валидация
    fields.forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input.required && !input.value.trim()) {
            valid = false;
            input.classList.add('error-input');
            const error = document.createElement('div');
            error.className = 'error';
            error.textContent = 'Обязательное поле';
            error.dataset.field = field;
            input.parentNode.insertBefore(error, input.nextSibling);
        } else {
            input.classList.remove('error-input');
        }
    });

    if (valid) {
        // Обновление предпросмотра
        const region = document.querySelector('[name="region"]').value;
        const city = document.querySelector('[name="city"]').value;
        const street = document.querySelector('[name="street"]').value;
        const house = document.querySelector('[name="house"]').value;
        const apartment = document.querySelector('[name="apartment"]').value;

        let address = `Обл. ${region}, г. ${city}, ул. ${street}, д. ${house}`;
        if (apartment) address += `, кв. ${apartment}`;
        
        document.getElementById('addressPreview').textContent = address;
        document.getElementById('addressModal').setAttribute('hidden', true);
    }
}

        // Показать/скрыть пароль
        function togglePassword() {
            const password = document.getElementById('password');
            password.type = password.type === 'password' ? 'text' : 'password';
        }

        // Маска для телефона
        document.addEventListener('DOMContentLoaded', () => {
            new Inputmask('+7(999) 999-99-99').mask(document.getElementById('phone'));
        });

        // Закрытие модального окна
        window.onclick = function(e) {
                if (e.target.classList.contains('modal')) {
                    document.getElementById('addressModal').setAttribute('hidden', true);
                }
        }   
    </script>
</body>
</html>

<style>
    /* Добавляем стиль для подсветки полей с ошибками */
    .error-input {
        border-color: black;
    }
</style>
