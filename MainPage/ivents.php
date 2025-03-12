<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мероприятия</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="iventstyles.css">
</head>
<body>
    <div class="content">
    <header class="bg-silver">
        <nav class="flex-justify-between">
            <div class="logo">
                <a href="index.php">
                    <img src="ico/Logo.svg" alt="BrandLogo">
                </a>
            </div>

            <div class="navigation">
                <ul class="listOfItems">
                    <li><a class="header-li" href="index.php">Главная</a></li>
                    <li><a href="ivents.php" class="header-li">Мероприятия</a></li>
                    <li><a href="../registration/registerform.php" class="header-li">Стать волонтерам</a></li>
                </ul>
            </div>

            <div class="buttons-container">
                <a class="login-text" href="../index.php">
                    <button class="login-but">Войти
                        <div class="arrow-wrapper">
                            <div class="arrow"></div>
                        </div>
                    </button>
                </a>
            </div>
        </nav>
    </header>

    <section class="about-events">
        <div class = "info-projects">
            <h1>Наши проекты</h1>
            <p>Мы проводим различные мероприятия, направленные на помощь в разных сферах. Присоединяйтесь к нам и сделайте мир лучше!</p>
        </div>
    </section>

    <div class="card-container">
        <!-- Пример карточки мероприятия -->
        <div class="card">
            <div class="contentt">
                    <img class = "img-card" src="ico/photo/kids.jpg" alt="">
                    <a href="#">
                        <span class="title">Помощь детям в детдомах</span>
                    </a>
                    <p class="desc">Мы организуем поездки в детские дома, где проводим мастер-классы, игры и помогаем детям учиться новым навыкам.</p>
                
            </div>
        </div>

        <div class="card">
            
            <div class="contentt">
                <img class = "img-card" src="ico/photo/voli_inforest.jpg" alt="">
                <a href="#">
                    <span class="title">Экологические акции</span>
                </a>
                <p class="desc">Помогите нам очистить города и леса! Присоединяйтесь к нашим экологическим акциям по уборке территорий.</p>
               
            </div>
        </div>

        <!-- Пример карточки мероприятия -->
        <div class="card">
            
            <div class="contentt">
                <img class = "img-card" src="ico/photo/taking_money.jpg" alt="">
                <a href="#">
                    <span class="title"> Сбор средств для бездомных</span>
                </a>
                <p class="desc">Мы организуем сбор средств для помощи бездомным людям и улучшения условий их жизни.</p>
               
            </div>
        </div>

        <div class="card">
            
            <div class="contentt">
                <img class = "img-card" src="ico/photo/farm.jpg" alt="">
                <a href="#">
                    <span class="title">Помощь на ферме</span>
                </a>
                <p class="desc">Мы предлагаем помощь на ферме, где волонтеры работают side by side с фермерами, поддерживая их в ежедневных задачах по уходу за животными, сбору урожая и улучшению сельскохозяйственной инфраструктуры. </p>
                
            </div>
        </div>

        <div class="card">
            
            <div class="contentt">
                <img class = "img-card" src="ico/photo/freefood.jpg" alt="">
                <a href="#">
                    <span class="title">Раздача еды в кризисных районах</span>
                </a>
                <p class="desc">Мы организуем раздачу еды в кризисных районах, помогая людям, оказавшимся в сложных жизненных обстоятельствах. Наши волонтеры каждый день раздают бесплатные пакеты с продуктами, горячие блюда и напитки, чтобы поддержать нуждающихся.</p>
                
            </div>
        </div>
        
        <!-- Можно добавить ещё карточек по аналогии -->
    </div>
    <div class = "after-text">
        <p>Зарегистрируйся чтобы увидеть больше!</p>
        <a href="../registration/registerform.php" class="register-but">Стать волонтерам</a>

    </div>
</div>



<div style = "padding-bottom:100px">

</div>

<footer style="z-index:999;">
    <section class="footer_section">
        <div class="copyright-div">
            <p class="footer_text">Copyright © 2025 Volunteering Organization</p>
            <p class="footer_text">Все права защищены</p>
            <a class="dog-link" href="../docs/Договор.pdf">Договор</a>
        </div>

        <div class="email_div">
            <p class="footer_text">Оставайтесь в курсе событий</p>
            <label>
                <input class="email-input" placeholder="Ваш Email"></input>
            </label>
        </div>
    </section>
</footer>
</body>
</html>
