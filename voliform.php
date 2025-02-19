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
       

        <nav class="tab-link">
            <button class="tablinks" onclick="openTab(event, 'projects')" id="projectsTabButton">Проекты</button>
            <button class="tablinks" onclick="openTab(event, 'applications')" id="applicationsTabButton">Заявки</button>
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

<section id="applications" class="tabcontent"> 
    <h1>Мои заявки</h1>
    <table class="appTable">
        <thead>
            <tr>
                <th>ID Заявки</th>
                <th>Проект</th>
                <th>Задача</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Проверяем, если сессия с данным пользователем активна
            if (isset($userID)) {
                // Запрос для получения заявок пользователя с добавлением поля статус
                $applications_sql = "
                    SELECT 
                        r.RequestID, 
                        p.ProjectName, 
                        t.Description AS TaskDescription,
                        r.status AS RequestStatus
                    FROM users_pending_approval r
                    JOIN projects p ON r.ProjectID = p.ProjectID
                    JOIN tasks t ON r.TaskID = t.TaskID
                    WHERE r.UserID = " . intval($userID);
                
                $applications_result = mysqli_query($connect, $applications_sql);

                if (mysqli_num_rows($applications_result) > 0) {
                    // Выводим каждую заявку пользователя
                    while ($application = mysqli_fetch_assoc($applications_result)) {
                        // Определяем CSS-класс для статуса
                        
                        
                        echo "<td>" . htmlspecialchars($application['RequestID']) . "</td>";
                        echo "<td>" . htmlspecialchars($application['ProjectName']) . "</td>";
                        echo "<td>" . htmlspecialchars($application['TaskDescription']) . "</td>";
                        echo "<td>" . htmlspecialchars($application['RequestStatus']) . "</td>";
                        echo "<td>";
                        // Показываем кнопку "Отменить заявку" только если статус не "Одобрена"
                        if ($application['RequestStatus'] !== 'Одобрена') {
                            echo "
                                <form method='post' action='volunteer/cancelRequest.php'>
                                    <input type='hidden' name='request_id' value='" . intval($application['RequestID']) . "'>
                                    <button type='submit' class='CancelReqBut'>Отменить заявку</button>
                                </form>
                            ";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>У вас нет заявок.</td></tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Ошибка при загрузке заявок.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</section>




<section id="volunteer-book" class="tabcontent"> 
    <h1>Мои задачи</h1>
    <table class="appTable">
        <thead>
            <tr>
                <th>ID Задачи</th>
                <th>Проект</th>
                <th>Описание задачи</th>
                <th>Статус задачи</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Проверяем, если сессия с данным пользователем активна
            if (isset($userID)) {
                // Запрос для получения задач пользователя с добавлением полей проекта и описания задачи
                $tasks_sql = "
                    SELECT 
                        ut.UserID, 
                        t.TaskID, 
                        t.Description AS TaskDescription, 
                        t.Status AS TaskStatus, 
                        p.ProjectName
                    FROM voli.user_tasks ut
                    JOIN voli.tasks t ON ut.TaskID = t.TaskID
                    JOIN voli.projects p ON t.ProjectID = p.ProjectID
                    WHERE ut.UserID = " . intval($userID);
                
                $tasks_result = mysqli_query($connect, $tasks_sql);

                if (mysqli_num_rows($tasks_result) > 0) {
                    // Выводим каждую задачу пользователя
                    while ($task = mysqli_fetch_assoc($tasks_result)) {
                        // Определяем CSS-класс для статуса задачи
                       
                        echo "<td>" . htmlspecialchars($task['TaskID']) . "</td>";
                        echo "<td>" . htmlspecialchars($task['ProjectName']) . "</td>";
                        echo "<td>" . htmlspecialchars($task['TaskDescription']) . "</td>";
                        echo "<td>" . htmlspecialchars($task['TaskStatus']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>У вас нет задач.</td></tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Ошибка при загрузке задач.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</section>



</div>
</main>

</body>
</html>
