<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список проектов и задач</title>
    <link rel="stylesheet" href="volistyle.css"> <!-- Подключаем файл стилей -->
</head>
<body>
    <header class="header">
        <?php
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
        <p>Добро пожаловать, <?php echo $name . ' ' . $surname; ?>!</p>
        <form method="post" action="index.php" style="display:inline;">
            <button type="submit" class="logout-button">Выход</button>
        </form>
    </header>

    <h1>Список проектов и задач</h1>

    <!-- Контейнер для активных проектов -->
    <div class="projects-active">
        <div class="projects-container">
            <?php
            // Функция для форматирования даты
            function formatDate($date) {
                $months = array(
                    '01' => 'января', '02' => 'февраля', '03' => 'марта',
                    '04' => 'апреля', '05' => 'мая', '06' => 'июня',
                    '07' => 'июля', '08' => 'августа', '09' => 'сентября',
                    '10' => 'октября', '11' => 'ноября', '12' => 'декабря'
                );
            
                $timestamp = strtotime($date);
                $day = date('j', $timestamp);
                $month = date('m', $timestamp);
                $year = date('Y', $timestamp);
            
                return $day . ' ' . $months[$month] . ' ' . $year;
            }

            // Получаем все активные проекты
            $projects_sql = "SELECT * FROM projects WHERE Status IN ('Планируется', 'Активен')";
            $projects_result = mysqli_query($connect, $projects_sql);

            if (mysqli_num_rows($projects_result) > 0) {
                while ($project = mysqli_fetch_assoc($projects_result)) {
                    echo "<div class='project'>";
                    echo "<h2>" . htmlspecialchars($project['ProjectName']) . "</h2>";
                    echo "<p>Начало: " . formatDate(htmlspecialchars($project['StartDate'])) . "</p>";
                    echo "<p>Завершение: " . formatDate(htmlspecialchars($project['EndDate'])) . "</p>";
                    echo "<p>Статус: " . htmlspecialchars($project['Status']) . "</p>";

                    $tasks_sql = "SELECT * FROM tasks WHERE ProjectID = " . intval($project['ProjectID']);
                    $tasks_result = mysqli_query($connect, $tasks_sql);

                    if (mysqli_num_rows($tasks_result) > 0) {
                        echo "<h3>Задачи:</h3>";
                        while ($task = mysqli_fetch_assoc($tasks_result)) {
                            echo "<div class='task'>";
                            echo "<p>Описание: " . htmlspecialchars($task['Description']) . "</p>";
                            echo "<p>Статус: " . htmlspecialchars($task['Status']) . "</p>";

                            $taskinfo_sql = "SELECT * FROM taskinfo WHERE TaskID = " . intval($task['TaskID']);
                            $taskinfo_result = mysqli_query($connect, $taskinfo_sql);
                            
                            if (mysqli_num_rows($taskinfo_result) > 0) {
                                while ($taskinfo = mysqli_fetch_assoc($taskinfo_result)) {
                                    echo "<p>Местоположение: " . htmlspecialchars($taskinfo['Location']) . "</p>";
                                    echo "<p>Дата: " . formatDate(htmlspecialchars($taskinfo['Date'])) . "</p>";
                                }
                            } else {
                                echo "<p>Нет дополнительной информации о задаче.</p>";
                            }
                            echo "<form method='post' action='submit_application.php'>";
                            echo "<input type='hidden' name='project_id' value='" . intval($project['ProjectID']) . "'>";
                            echo "<input type='hidden' name='task_id' value='" . intval($task['TaskID']) . "'>";
                            echo "<button type='submit'>Подать заявку</button>";
                            echo "</form>";
                            
                            echo "</div>"; // task
                        }
                    } else {
                        echo "<p class='no-tasks'>Для данного проекта задачи пока не добавлены.</p>";
                    }

                    echo "</div>"; // project
                }
            } else {
                echo "<p>Нет доступных проектов.</p>";
            }

            // Не закрываем соединение здесь, так как оно понадобится позже
            ?>
        </div> <!-- Закрываем projects-container -->
    </div> <!-- Закрываем projects-active -->

    <!-- Выпадающий список для завершенных проектов -->
    <div class="dropdown">
        <button onclick="toggleDropdown(this)">Завершенные проекты</button>
        <div class="dropdown-content">
            <div class="projects-completed">
                <div class="projects-container">    
                    <?php
                    // Получаем все завершенные проекты
                    $completed_projects_sql = "SELECT * FROM projects WHERE Status = 'Завершен'";
                    $completed_projects_result = mysqli_query($connect, $completed_projects_sql);

                    if (mysqli_num_rows($completed_projects_result) > 0) {
                        while ($project = mysqli_fetch_assoc($completed_projects_result)) {
                            echo "<div class='project'>";
                            echo "<h2>" . htmlspecialchars($project['ProjectName']) . "</h2>";
                            echo "<p>Начало: " . formatDate(htmlspecialchars($project['StartDate'])) . "</p>";
                            echo "<p>Завершение: " . formatDate(htmlspecialchars($project['EndDate'])) . "</p>";
                            echo "<p>Статус: " . htmlspecialchars($project['Status']) . "</p>";

                            $tasks_sql = "SELECT * FROM tasks WHERE ProjectID = " . intval($project['ProjectID']);
                            $tasks_result = mysqli_query($connect, $tasks_sql);

                            if (mysqli_num_rows(result: $tasks_result) > 0) {
                                echo "<h3>Задачи:</h3>";
                                while ($task = mysqli_fetch_assoc($tasks_result)) {
                                    echo "<div class='task'>";
                                    echo "<p>Описание: " . htmlspecialchars($task['Description']) . "</p>";
                                    echo "<p>Статус: " . htmlspecialchars($task['Status']) . "</p>";

                                    $taskinfo_sql = "SELECT * FROM taskinfo WHERE TaskID = " . intval($task['TaskID']);
                                    $taskinfo_result = mysqli_query($connect, $taskinfo_sql);

                                    if (mysqli_num_rows($taskinfo_result) > 0) {
                                        while ($taskinfo = mysqli_fetch_assoc($taskinfo_result)) {
                                            echo "<p>Местоположение: " . htmlspecialchars($taskinfo['Location']) . "</p>";
                                            echo "<p>Дата: " . formatDate(htmlspecialchars($taskinfo['Date'])) . "</p>";
                                        }
                                    } else {
                                        echo "<p>Нет дополнительной информации о задаче.</p>";
                                    }

                                    echo "</div>"; // task
                                }
                            } else {
                                echo "<p class='no-tasks'>Для данного проекта задачи пока не добавлены</p>";
                            }

                            echo "</div>"; // project
                        }
                    } else {
                        echo "<p>Нет завершенных проектов.</p>";
                    }
                    ?>
                </div> <!-- Закрываем projects-container -->
            </div> <!-- Закрываем projects-completed -->
        </div> <!-- Закрываем dropdown-content -->
    </div> <!-- Закрываем dropdown -->

    <!-- Выпадающий список для отмененных проектов -->
    <div class="dropdown">
        <button onclick="toggleDropdown(this)">Отмененные проекты</button>
        <div class="dropdown-content">
            <div class="projects-cancelled">
                <div class="projects-container">
                    <?php
                    // Получаем все отмененные проекты
                    $cancelled_projects_sql = "SELECT * FROM projects WHERE Status = 'Отменен'";
                    $cancelled_projects_result = mysqli_query($connect, $cancelled_projects_sql);

                    if (mysqli_num_rows($cancelled_projects_result) > 0) {
                        while ($project = mysqli_fetch_assoc($cancelled_projects_result)) {
                            echo "<div class='project'>";
                            echo "<h2>" . htmlspecialchars($project['ProjectName']) . "</h2>";
                            echo "<p>Начало: " . formatDate(htmlspecialchars($project['StartDate'])) . "</p>";
                            echo "<p>Завершение: " . formatDate(htmlspecialchars($project['EndDate'])) . "</p>";
                            echo "<p>Статус: " . htmlspecialchars($project['Status']) . "</p>";

                            $tasks_sql = "
    SELECT 
        tasks.TaskID, tasks.Description, tasks.Status, 
        taskinfo.Location, taskinfo.Date
    FROM tasks
    LEFT JOIN taskinfo ON tasks.TaskID = taskinfo.TaskID
    WHERE tasks.ProjectID = " . intval($project['ProjectID']);
                            $tasks_result = mysqli_query($connect, $tasks_sql);

                           if (mysqli_num_rows($tasks_result) > 0) {
    echo "<h3>Задачи:</h3>";
    echo "<div class='tasks-container'>"; // Контейнер для задач

    while ($task = mysqli_fetch_assoc($tasks_result)) {
        echo "<div class='task'>";
        echo "<p>Описание: " . htmlspecialchars($task['Description']) . "</p>";
        echo "<p>Статус: " . htmlspecialchars($task['Status']) . "</p>";

        if (!empty($task['Location']) && !empty($task['Date'])) {
            echo "<p>Местоположение: " . htmlspecialchars($task['Location']) . "</p>";
            echo "<p>Дата: " . formatDate(htmlspecialchars($task['Date'])) . "</p>";
        } else {
            echo "<p>Нет дополнительной информации о задаче.</p>";
        }

        echo "</div>"; // Закрываем задачу
    }

    echo "</div>"; // Закрываем контейнер для задач
} else {
    echo "<p>Нет задач для этого проекта.</p>";
}

                            echo "</div>"; // project
                        }
                    } else {
                        echo "<p>Нет отмененных проектов.</p>";
                    }

                    mysqli_close($connect); // Закрываем соединение после последнего запроса
                    ?>
                </div> <!-- Закрываем projects-container -->
            </div> <!-- Закрываем projects-cancelled -->
        </div> <!-- Закрываем dropdown-content -->
    </div> <!-- Закрываем dropdown -->

    <script>
        function toggleDropdown(button) {
            var dropdown = button.parentElement;
            dropdown.classList.toggle('show');
        }
    </script>
</body>
</html>
