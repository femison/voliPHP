<?php
// Сделать логику добавления пользователя и проверку на правильность заполенения поля 
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="registerstyle.css">
</head>
<body>

<div class="bg"></div> 
    <div class="container" style="padding-top:3%">
        <h1 >Регистрация</h1>
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

           
           
            <div class="full-width">
                <input type="text" name="city" placeholder="Населенный пункт" maxlength="30" value="<?= $old_input['city'] ?? '' ?>" class="<?= isset($errors['city']) ? 'error-input' : '' ?>">
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

            <div class="full-width-button-group">
                
            
            
                <button class = "btn" type="submit">

                Зарегистрироваться
                <div class="arrow-wrapper">
                    <div class="arrow"></div>

                </div>
                
                
                </button>
                

                
                
                <a class="btn" href="../MainPage/index.php">
                    
                    Вернуться на главную
                    <div class="arrow-wrapper">
                        <div class="arrow">

                        </div>
                    </div>
                    
                </a>


               

            
                
                
                
                

            </div>
        </form>
    </div>

    


    <script>
       function openModal() {
            document.getElementById('addressModal').removeAttribute('hidden');
        }

        
    
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
