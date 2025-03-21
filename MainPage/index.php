<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voli";

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL запрос для подсчета пользователей с ролью "Волонтер"
$sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'Волонтер'";
$result = $conn->query($sql);

// Получение и вывод результата
$totalVolunteers = 0;
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $totalVolunteers = $row["total"];
    }
} else {
    $totalVolunteers = 0;
}


$sqlproj = "SELECT COUNT(*) AS total FROM projects WHERE Status = 'Завершен'";
$result = $conn->query($sqlproj);


$totalCompletedProjects = 0;
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $totalCompletedProjects = $row["total"];
    }
} else {
    $totalCompletedProjects = 0;
}




$sqlavailproj = "SELECT COUNT(*) AS total FROM projects WHERE Status = 'Планируется' OR Status = 'Активен'";
$res = $conn->query($sqlavailproj);

$totalavalproj = 0;
if ($res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
        $totalavalproj = $row["total"];
    }
} else {
    $totalavalproj = 0;
}


$colplaces = "SELECT count(Location) AS total FROM voli.taskinfo";
$resus = $conn->query($colplaces);

$totalplaces = 0;
if ($resus->num_rows > 0) {
    while($row = $resus->fetch_assoc()) {
        $totalplaces = $row["total"];
    }
} else {
    $totalplaces = 0;
}




$conn->close();
?>




<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="UTF-8">
        <title>Волонтерская программа</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Open+Sans:wght@300;400&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
        <title>Главная страница</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Open+Sans:wght@300;400&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<div class="content">

    <header class="bg-silver">
        <nav class="flex-justify-between">
            <div class='logo'>
                <a href="#">
                    <img class = "logo-img" src="ico/logo.png" alt="BrandLogo">
                </a>
            </div>

            <div class="navigation">
                <ul class="listOfItems">
                    <li>
                        <a class = "header-li" href="index.php">Главная</a>
                    </li>
                    <li>
                        <a href = "ivents.php" class = "header-li">Мероприятия</a>
                    </li>
                    <li>
                        <a href="../registration/registerform.php" class = "header-li">Стать волонтерам</a>
                    </li>
                    
                </ul>
            </div>

            <div class="buttons-container">
                <a class = "login-text" href="../index.php">
                    <button class="login-but" style="cursor: pointer;">
                        Войти
                        <div class="arrow-wrapper">
                            <div class="arrow"></div>
                        </div>
                    </button>
                </a>
            </div>
            
        </nav>
    </header>

<section class ="intro-secs">
    <img class="man-image" src="ico\clean-forest.jpg" alt="" style = "z-index:0">
        <div class="intro">
            
            <div class="Business_div_2">
           
                <h1 class="legalising">
                    Волонтерская работа и ее значимость <br>
                    <span class="text-brand-primary">в современном обществе</span>
                </h1>

                <p class="Lower_Text">Как стать частью волонтерской команды и менять мир к лучшему?</p>
                <a href="../registration/registerform.php">
                    <button class="RegisterBut">Регистрация</button>
                </a>
            </div>
            
        </div>
        
</section>

<section class="Clients">
    <h1 class="title">Наши партнеры и поддержка</h1>
    <p class="Lower_Text_clients">Мы работаем с организациями, которые помогают улучшать общество.</p>

    <div class="clientside">
        <div class="client-logo">
            
            <img class="square" src="ico/les_hoz.png" alt="Charity" loading="lazy">
        </div>
        <div class="client-logo">
            <img class="square" src="ico/min_prirodi.png" alt="Community" loading="lazy">
        </div>
        <div class="client-logo">
            <img class="square" src="ico/ohrana_prirodi.png" alt="NGO" loading="lazy">
        </div>
        <div class="client-logo">
            <img class="square" src="ico/tvoj_geroj.png" alt="NGO" loading="lazy">
        </div>
        <div class="client-logo">
            <img class="square" src="ico/edinru.png" alt="NGO" loading="lazy">
        </div>
        <div class="client-logo">
            <img class="square" src="ico/Dobrorf_Logo.png" alt="NGO" >
        </div>
    </div>
</section>


<section class="About_us">
    <div class="about-us">
        <h1 class="title">Станьте частью волонтерского движения</h1>
        <p class="Text_suitable">4 простых шага, чтобы начать помогать</p>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-user-check"></i></div>
                <h3>Регистрация</h3>
                <p>Заполните простую анкету за 5 минут</p>
            </div>
            <div class="step-card">
                <div class="step-icon"><i class="fa-duotone fa-solid fa-clipboard-list"></i></div>
                <h3>Выбор проекта</h3>
                <p>Подберите подходящее мероприятие</p>
            </div>
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-tasks"></i></div>
                <h3>Выбор Задачи</h3>
                <p>Подберите подходящую задачу</p>
            </div>
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-hands-helping"></i></div>
                <h3>Старт</h3>
                <p>Приступайте к волонтерской деятельности!</p>
            </div>
        </div>
    </div>
</section>


<section class="activity-types">
    <h1 class="title">Наша миссия воплощается в действии</h1>
    <div class="activity-types-grid">
        <div class="type-item">
            <h3>Организация мероприятий</h3>
            <p>Мы организуем различные мероприятия для нуждающихся.</p>
            <p class="second_str">Присоединяйтесь к нашим акциям.</p>
        </div>
        <div class="type-item">
            <h3>Образовательные программы</h3>
            <p>Мы проводим обучающие сессии и тренинги.</p>
            <p class="second_str">Поделитесь своими знаниями и опытом.</p>
        </div>
        <div class="type-item">
            <h3>Социальные проекты</h3>
            <p>Проекты для помощи людям в сложной жизненной ситуации.</p>
            <p class="second_str">Ваш вклад важен для каждого.</p>
        </div>
    </div>
</section>

<section class="activity_section">
    <h1 class="title">Направления деятельности, по которым вы можете выполнять задачи</h1>
    <div class="activity-grid">
        <div class="activity-item">
            <p class="activity-text">Дети и молодежь</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Образование</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Здравоохранение</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Природа</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Животные</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Поиск пропавших</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Медиа</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Спорт и события</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Срочная помощь (ЧС)</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Права человека</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Помощь людям с ОВЗ</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Культура и искусство</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Ветераны</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Старшее поколение</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Интеллектуальная помощь</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Урбанистика</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Наука</p>
        </div>
        <div class="activity-item">
            <p class="activity-text">Другое</p>
        </div>
    </div>
</section>


<section class="Helping_section">
    <div class="Helping_div">
        <h1 class="title">О платформе</h1>
        <p class="Text_suitable">Мы организуем различные мероприятия для нуждающихся, создавая возможности для волонтеров менять мир к лучшему. Наша цель — объединить усилия людей, готовых помогать, и тех, кто нуждается в поддержке.</p>
        <h2 class="subtitle">Что мы делаем?</h2>
        <p class="extra-text">Мы поддерживаем проекты по всей стране, предлагаем обучение и ресурсы для волонтеров, а также создаем сообщество единомышленников, где каждый может внести свой вклад.</p>
    </div>
    <div class="helping-grid">
        <div class="help-card">
            <b class="int-user"><?php echo $totalVolunteers + 2939; ?></b>
            <p class="sec-text">Волонтеров</p>
            <p class="card-desc">Активных участников, готовых помогать.</p>
        </div>
        <div class="help-card">
            <b class="int-user"><?php echo $totalplaces + 154; ?></b>
            <p class="sec-text">Мест проведения</p>
            <p class="card-desc">Точек, где проходят наши акции.</p>
        </div>
        <div class="help-card">
            <b class="int-user"><?php echo $totalCompletedProjects + 518; ?></b>
            <p class="sec-text">Добрых дел</p>
            <p class="card-desc">Завершенных инициатив для общества.</p>
        </div>
        <div class="help-card">
            <b class="int-user"><?php echo $totalavalproj + 221; ?></b>
            <p class="sec-text">Доступных проектов</p>
            <p class="card-desc">Возможностей присоединиться прямо сейчас.</p>
        </div>
    </div>
</section>

<section class="Support_section">
    <div class="Business">
        <div class="Business_div_2">
            <h1 class="title">Волонтерская поддержка</h1>
            <p class="Text_suitable">
                Наша команда всегда готова поддержать волонтеров в их начинаниях. Мы предоставляем информационную, юридическую и практическую помощь, чтобы каждый участник мог сосредоточиться на главном — помощи другим. Наша цель — создать комфортные условия для вашей деятельности и обеспечить успех каждого проекта.
            </p>
            <h2 class="subtitle">Как мы помогаем?</h2>
            <p class="extra-text">
                От консультаций и обучения до прямой помощи на местах — мы рядом на каждом этапе вашего волонтерского пути.
            </p>
            <div class="support-grid">
                <div class="support-item">
                    <h2>Информационная поддержка</h2>
                    <p>Предоставление актуальной информации о волонтерских проектах и событиях.</p>
                    <p class="card-desc">Доступ к новостям, расписаниям и ресурсам.</p>
                </div>
                <div class="support-item">
                    <h2>Юридическая помощь</h2>
                    <p>Консультации по правовым вопросам, связанным с волонтерской деятельностью.</p>
                    <p class="card-desc">Разъяснение прав и обязанностей волонтеров.</p>
                </div>
                <div class="support-item">
                    <h2>Практическая помощь</h2>
                    <p>Помощь в организации мероприятий и в решении текущих вопросов на местах.</p>
                    <p class="card-desc">Поддержка логистики и координации.</p>
                </div>
            </div>
        </div>
    </div>
</section>



</div>

<footer>
    <section class="footer_section">

        

        <div class="copyright-div">
            <p class="footer_text">Copyright © 2025 Volunteering Organization</p>
            <p class="footer_text">Все права защищены</p>
            <a class="dog-link" href="../docs/Договор.pdf">Договор</a>
        </div>

        <div class="email_div">
            <p class="footer_text">Наши Социальные Сети</p>
            <label>
                <p>Вк</p>
                <p>Телеграм</p>
                
            </label>
        </div>
    </section>
</footer>



<script>
// Анимация появления секций
const sections = document.querySelectorAll('section');
        const checkVisibility = () => {
            sections.forEach(section => {
                const rect = section.getBoundingClientRect();
                if (rect.top < window.innerHeight * 0.8 && rect.bottom > 0) {
                    section.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', checkVisibility);
        window.addEventListener('resize', checkVisibility);
        checkVisibility();

        // Плавный скролл
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    
</script>
</body>
</html>
