<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список проектов и задач</title>
    <link rel="stylesheet" href="volistyle.css"> 
    <script src="admin/script.js"></script>
    
</head>
<body>
    <header class="header">
        <?php
        require 'func/function.php';
        session_start();
        require 'admin/db_connection.php'; // Подключаем файл с функцией подключения к БД

        if (mysqli_connect_errno()) {
            die("Ошибка подключения к базе данных: " . mysqli_connect_error());
        }

        if (isset($_SESSION["username"])) {
            $username = mysqli_real_escape_string($connect, $_SESSION["username"]);

            $user_id_query = "SELECT UserID FROM usercredentials WHERE Login='$username'";
            $user_id_result = mysqli_query($connect, $user_id_query);

            if (mysqli_num_rows($user_id_result) == 1) {
                $user_id_row = mysqli_fetch_assoc($user_id_result);
                $userID = $user_id_row['UserID'];

                $user_info_query = "SELECT Name, Surname FROM users WHERE UserID='$userID'";
                $user_info_result = mysqli_query($connect, $user_info_query);

                if (mysqli_num_rows($user_info_result) == 1) {
                    $user_info_row = mysqli_fetch_assoc($user_info_result);
                    $name = htmlspecialchars($user_info_row['Name']);
                    $surname = htmlspecialchars($user_info_row['Surname']);
                } else {
                    $name = "Неизвестно";
                    $surname = "";
                }
            } else {
                $name = "Неизвестно";
                $surname = "";
            }
        } else {
            $name = "Гость";
            $surname = "";
        }
        ?>
        <div class="header-content">
            <p>Добро пожаловать, <?php echo $name . ' ' . $surname; ?>!</p>
            
        </div>
        <!-- <nav class="tabs">
            <ul>
                <li><a href="#projects" class="tab-link">Проекты</a></li>
                <li><a href="#applications" class="tab-link">Мои заявки</a></li>
                <li><a href="#volunteer-book" class="tab-link">Волонтерская книжка</a></li>
            </ul>
        </nav> -->

        <nav class="tab-link">
            <button class="tablinks" onclick="openTab(event, 'projects')" id="projectsTabButton">Проекты</button>
            <button class="tablinks" onclick="openTab(event, 'applications')" id="applicationsTabButton">Задачи</button>
            <button class="tablinks" onclick="openTab(event, 'volunteer-book')" id="volunteerBookTabButton">Участия</button>
            <form method="post" action="index.php" style="display:inline;">
                <button type="submit" class="logout-button">Выход</button>
            </form>
        </nav>

    </header>



    <main>
    <div id="projects" class="tabcontent">
    <h1>Список проектов и задач</h1>

<!-- Контейнер для активных проектов -->
<div class="projects-section">
    <h2>Активные проекты</h2>
    <div class="projects-container">
        <?php
        // Получаем все активные проекты
        $projects_sql = "SELECT * FROM projects WHERE Status IN ('Планируется', 'Активен')";
        $projects_result = mysqli_query($connect, $projects_sql);

        if (mysqli_num_rows($projects_result) > 0) {
            while ($project = mysqli_fetch_assoc($projects_result)) {
                echo "<div class='project active'>";
                echo "<h3>" . htmlspecialchars($project['ProjectName']) . "</h3>";
                echo "<p>Начало: " . formatDate(htmlspecialchars($project['StartDate'])) . "</p>";
                echo "<p>Завершение: " . formatDate(htmlspecialchars($project['EndDate'])) . "</p>";
                echo "<p>Статус: " . htmlspecialchars($project['Status']) . "</p>";

                $tasks_sql = "SELECT * FROM tasks WHERE ProjectID = " . intval($project['ProjectID']);
                $tasks_result = mysqli_query($connect, $tasks_sql);

                if (mysqli_num_rows($tasks_result) > 0) {
                    echo "<h4>Задачи:</h4>";
                    while ($task = mysqli_fetch_assoc($tasks_result)) {
                        echo "<div class='task'>";
                        echo "<p>Описание: " . htmlspecialchars($task['Description']) . "</p>";
                        echo "<p>Статус: " . htmlspecialchars($task['Status']) . "</p>";
                        echo "<form method='post' action='submit_application.php'>";
                        echo "<input type='hidden' name='project_id' value='" . intval($project['ProjectID']) . "'>";
                        echo "<input type='hidden' name='task_id' value='" . intval($task['TaskID']) . "'>";
                        echo "<button type='submit'>Подать заявку</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Для данного проекта задачи пока не добавлены.</p>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>Нет доступных активных проектов.</p>";
        }
        ?>
    </div>
</div>

<!-- Контейнер для завершённых проектов -->
<div class="projects-section" >
    <h2>Завершённые проекты</h2>
    <div class="projects-container">
        <?php
        // Получаем все завершённые проекты
        $completed_projects_sql = "SELECT * FROM projects WHERE Status = 'Завершен'";
        $completed_projects_result = mysqli_query($connect, $completed_projects_sql);

        if (mysqli_num_rows($completed_projects_result) > 0) {
            while ($project = mysqli_fetch_assoc($completed_projects_result)) {
                echo "<div class='project-completed'>";
                echo "<h3>" . htmlspecialchars($project['ProjectName']) . "</h3>";
                echo "<p>Начало: " . formatDate(htmlspecialchars($project['StartDate'])) . "</p>";
                echo "<p>Завершение: " . formatDate(htmlspecialchars($project['EndDate'])) . "</p>";
                echo "<p>Статус: " . htmlspecialchars($project['Status']) . "</p>";

                $tasks_sql = "SELECT * FROM tasks WHERE ProjectID = " . intval($project['ProjectID']);
                $tasks_result = mysqli_query($connect, $tasks_sql);

                if (mysqli_num_rows($tasks_result) > 0) {
                    echo "<h4>Задачи:</h4>";
                    while ($task = mysqli_fetch_assoc($tasks_result)) {
                        echo "<div class='task'>";
                        echo "<p>Описание: " . htmlspecialchars($task['Description']) . "</p>";
                        echo "<p>Статус: " . htmlspecialchars($task['Status']) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Задачи завершены.</p>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>Нет завершённых проектов.</p>";
        }
        ?>
    </div>
</div>

<!-- Контейнер для отменённых проектов -->
<div class="projects-section">
    <h2>Отменённые проекты</h2>
    <div class="projects-container">
        <?php
        // Получаем все отменённые проекты
        $cancelled_projects_sql = "SELECT * FROM projects WHERE Status = 'Отменен'";
        $cancelled_projects_result = mysqli_query($connect, $cancelled_projects_sql);

        if (mysqli_num_rows($cancelled_projects_result) > 0) {
            while ($project = mysqli_fetch_assoc($cancelled_projects_result)) {
                echo "<div class='project-cancelled'>";
                echo "<h3>" . htmlspecialchars($project['ProjectName']) . "</h3>";
                echo "<p>Начало: " . formatDate(htmlspecialchars($project['StartDate'])) . "</p>";
                echo "<p>Завершение: " . formatDate(htmlspecialchars($project['EndDate'])) . "</p>";
                echo "<p>Статус: " . htmlspecialchars($project['Status']) . "</p>";

                $tasks_sql = "SELECT * FROM tasks WHERE ProjectID = " . intval($project['ProjectID']);
                $tasks_result = mysqli_query($connect, $tasks_sql);

                if (mysqli_num_rows($tasks_result) > 0) {
                    echo "<h4>Задачи:</h4>";
                    while ($task = mysqli_fetch_assoc($tasks_result)) {
                        echo "<div class='task'>";
                        echo "<p>Описание: " . htmlspecialchars($task['Description']) . "</p>";
                        echo "<p>Статус: " . htmlspecialchars($task['Status']) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Задачи отменены.</p>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>Нет отменённых проектов.</p>";
        }
        ?>
    </div>
</div>
</main>

<!-- Секция Мои заявки -->
<section id="applications" class="tabcontent">
    <main>
    <h1>Мои заявки</h1>
    <p>Контент о заявках...</p>
    </main>
</section>

<!-- Секция Волонтерская книжка -->
<section id="volunteer-book" class="tabcontent">
    <main>
    <h1>Волонтерская книжка</h1>
    <p>Контент о волонтерской книжке...</p>
</section>
    </div>
    </main>

</body>
</html>
