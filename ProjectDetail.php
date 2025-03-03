<?php
session_start();
require 'admin/db_connection.php'; // Подключение к базе данных
require 'func/function.php'; // Подключение функции formatDate

if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    header("Location: index.php"); // Перенаправление, если project_id не указан
    exit();
}

$project_id = intval($_GET['project_id']);

// Запрос информации о проекте
$project_sql = "SELECT * FROM projects WHERE ProjectID = $project_id";
$project_result = mysqli_query($connect, $project_sql);

if (mysqli_num_rows($project_result) == 0) {
    header("Location: index.php"); // Перенаправление, если проект не найден
    exit();
}

$project = mysqli_fetch_assoc($project_result);

// Запрос задач проекта
$tasks_sql = "SELECT * FROM tasks WHERE ProjectID = $project_id";
$tasks_result = mysqli_query($connect, $tasks_sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['ProjectName']); ?> - Подробности</title>
    <link rel="stylesheet" href="volistyle.css">
</head>
<body>
<div class="wrapper">
    <header class="header">
        <div class="header-content">
            <p>Добро пожаловать, <?php echo isset($_SESSION["username"]) ? htmlspecialchars($_SESSION["username"]) : "Гость"; ?>!</p>
        </div>
        <nav class="tab-link">
            <a href="index.php" class="tablinks">Назад к списку проектов</a>
        </nav>
    </header>

    <main>
        <section class="project-details">
            <h1><?php echo htmlspecialchars($project['ProjectName']); ?></h1>
            <div class="project-info">
                <p><strong>Начало:</strong> <?php echo formatDate(htmlspecialchars($project['StartDate'])); ?></p>
                <p><strong>Завершение:</strong> <?php echo formatDate(htmlspecialchars($project['EndDate'])); ?></p>
                <p><strong>Статус:</strong> <?php echo htmlspecialchars($project['Status']); ?></p>
            </div>

            <h2>Задачи проекта</h2>
            <div class="tasks-container">
                <?php
                if (mysqli_num_rows($tasks_result) > 0) {
                    while ($task = mysqli_fetch_assoc($tasks_result)) {
                        echo "<div class='task'>";
                        echo "<p><strong>Описание:</strong> " . htmlspecialchars($task['Description']) . "</p>";
                        echo "<p><strong>Статус:</strong> " . htmlspecialchars($task['Status']) . "</p>";
                        if ($project['Status'] == 'Планируется' || $project['Status'] == 'Активен') {
                            echo "<form method='post' action='submit_application.php'>";
                            echo "<input type='hidden' name='project_id' value='" . $project_id . "'>";
                            echo "<input type='hidden' name='task_id' value='" . intval($task['TaskID']) . "'>";
                            echo "<button type='submit'>Подать заявку</button>";
                            echo "</form>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<p>Для данного проекта задачи пока не добавлены.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <p>© 2025 Волонтерские проекты. Все права защищены.</p>
            <p>Свяжитесь с нами: info@volunteerprojects.ru</p>
        </div>
    </footer>
</div>
</body>
</html>

<?php
mysqli_close($connect); // Закрытие соединения с базой данных
?>