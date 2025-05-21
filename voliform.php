<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список проектов и задач</title>
    <link rel="stylesheet" href="volistyle.css"> 
    <link rel="stylesheet" href="MainPage.css"> 
    <script src="admin/script.js"></script>
    <script src="volunteer/src.js"></script>
</head>
<body>
<div class="wrapper">
    <header class="header">
        <?php
        require 'func/function.php';
        session_start();
        require 'admin/db_connection.php'; // Подключаем файл с функцией подключения к БД

        if (mysqli_connect_errno()) {
            die("Ошибка подключения к базе данных: " . mysqli_connect_error());
        }

        if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
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
            <button class="tablinks" onclick="openTab(event, 'MainPage')" id="mainpageTabButton">Главная страница</button>
            <button class="tablinks" onclick="openTab(event, 'projects')" id="projectsTabButton">Проекты</button>
            <button class="tablinks" onclick="openTab(event, 'applications')" id="applicationsTabButton">Заявки</button>
            <button class="tablinks" onclick="openTab(event, 'volunteer-book')" id="volunteerBookTabButton">Участия</button>
            <form method="post" action="index.php" style="display:inline;">
                <button type="submit" class="tablinks-exit">Выход</button>
            </form>
        </nav>
    </header>

    <main>
        <!-- Раздел MainPage -->

               
<section id="MainPage" class="tabcontent mainpage-section">
    <header class="mainpage-header" >
        <div class="header-text">
            <h1>Добро пожаловать в волонтёрский центр</h1>
            <div class="org-description">
            Наша организация объединяет волонтёров, желающих внести вклад в общество. Мы реализуем социальные, экологические, культурные и образовательные проекты, вовлекая всех желающих в добрые дела.
    </div>
           
        </div>

        <div class="photo-container">
            <img src="images/voli.png" alt="Фото организации">
        </div>
    </header>

    

    <div class="facts-section">
        <div class="fact-card">
            <h3>150+</h3>
            <p>Активных волонтёров</p>
        </div>
        <div class="fact-card">
            <h3>80+</h3>
            <p>Успешно завершённых проектов</p>
        </div>
        <div class="fact-card">
            <h3>15</h3>
            <p>Городов участвуют</p>
        </div>
        <div class="fact-card">
            <h3>10 лет</h3>
            <p>опыта работы</p>
        </div>
        <div class="fact-card">
            <h3>200+</h3>
            <p>Проведено мероприятий</p>
        </div>
    </div>

    <div class="welcome-note">
        <p>Присоединяйтесь к нам и станьте частью большого сообщества, меняющего мир к лучшему!</p>
    </div>

    <div class="additional-content">
    <div class="content-card">
        <h2>О нашей организации</h2>
        <p>Мы стремимся создавать позитивные изменения в жизни людей через развитие социальных проектов и поддержку связи между волонтёрами и сообществами. Наша миссия – вдохновлять людей на активное участие в жизни общества.</p>
    </div>

    <div class="content-card">
        <h2>Предстоящие события</h2>
        <p>Следите за нашими новыми мероприятиями, участвуйте в семинарах и мастер-классах, а также в обсуждениях, направленных на улучшение жизни в нашем городе.</p>
    </div>

    <div class="content-card">
        <h2>Последние новости</h2>
        <p>Будьте в курсе последних событий, читайте обновления и анонсы важных мероприятий, которые помогут вам оставаться активным участником нашей волонтёрской семьи.</p>
    </div>
</div>
</section>




<script>
          function openModalWithProject(projectId, projectName) {
        // Устанавливаем ID проекта и его название в модальном окне
        document.getElementById('modalProjectId').value = projectId;
        document.getElementById('modalProjectName').textContent = projectName;
        
        // Показываем модальное окно
        document.querySelector('.modal-requests').style.display = 'block';
        
        // Загружаем задачи для этого проекта
        loadTasksForProject(projectId);
    }
            closeModal = function() {
                document.querySelector('.modal-requests').style.display = 'none';
            }
</script>



        <!-- Раздел проектов -->
  <!-- Раздел проектов -->
  <section id="projects" class="tabcontent">
            <h1>Список проектов</h1>

            <div class="projects-section" id="active-projects">
                <h2>Активные проекты</h2>
                <div class="projects-container">
                    <?php
                    $projects_sql = "SELECT * FROM projects WHERE Status IN ('Планируется', 'Активен')";
                    $projects_result = mysqli_query($connect, $projects_sql);

                    if (mysqli_num_rows($projects_result) > 0) {
                        while ($project = mysqli_fetch_assoc($projects_result)) {
                            echo "<div class='project active'>";
                            echo "<h3>" . htmlspecialchars($project['ProjectName']) . "</h3>";
                            echo "<p>Начало: " . formatDate(htmlspecialchars($project['StartDate'])) . "</p>";
                            echo "<p>Завершение: " . formatDate(htmlspecialchars($project['EndDate'])) . "</p>";
                            echo "<p>Статус: " . htmlspecialchars($project['Status']) . "</p>";
                            echo "<button onclick=\"openModalWithProject(" . $project['ProjectID'] . ", '" . htmlspecialchars(addslashes($project['ProjectName'])) . "')\">Подать заявку</button>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Нет доступных активных проектов.</p>";
                    }
                    ?>
                </div>
            </div>




            <div class="projects-section" id="active-projects">
                <h2>Завершённые проекты</h2>
                <div class="projects-container">
                    <?php
                    $completed_projects_sql = "SELECT * FROM projects WHERE Status = 'Завершен'";
                    $completed_projects_result = mysqli_query($connect, $completed_projects_sql);

                    if (mysqli_num_rows($completed_projects_result) > 0) {
                        while ($project = mysqli_fetch_assoc($completed_projects_result)) {
                            echo "<div class='project-completed'>";
                            echo "<h3>" . htmlspecialchars($project['ProjectName']) . "</h3>";
                            echo "<p>Начало: " . formatDate(htmlspecialchars($project['StartDate'])) . "</p>";
                            echo "<p>Завершение: " . formatDate(htmlspecialchars($project['EndDate'])) . "</p>";
                            echo "<p>Статус: " . htmlspecialchars($project['Status']) . "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Нет завершённых проектов.</p>";
                    }
                    ?>
                </div>
            </div>

            

            <div class="projects-section">
                <h2>Отменённые проекты</h2>
                <div class="projects-container">
                    <?php
                    $cancelled_projects_sql = "SELECT * FROM projects WHERE Status = 'Отменен'";
                    $cancelled_projects_result = mysqli_query($connect, $cancelled_projects_sql);

                    if (mysqli_num_rows($cancelled_projects_result) > 0) {
                        while ($project = mysqli_fetch_assoc($cancelled_projects_result)) {
                            echo "<a" . intval($project['ProjectID']) . "' class='project-cancelled'>";
                            echo "<h3>" . htmlspecialchars($project['ProjectName']) . "</h3>";
                            echo "<p>Начало: " . formatDate(htmlspecialchars($project['StartDate'])) . "</p>";
                            echo "<p>Завершение: " . formatDate(htmlspecialchars($project['EndDate'])) . "</p>";
                            echo "<p>Статус: " . htmlspecialchars($project['Status']) . "</p>";
                            echo "</a>";
                        }
                    } else {
                        echo "<p>Нет отменённых проектов.</p>";
                    }
                    ?>
                </div>
            </div>

<!-- Добавьте этот код в раздел projects перед закрывающим тегом </section> -->
<div class="modal-requests" style ="display:none">
    <div class="modal-content enhanced-modal">
        <span class="close-button" onclick="closeModal()">&times;</span>
        <h2 class="modal-title">Заявка на участие в проекте</h2>
        <form id="requestForm" method="post" action="volunteer/sendRequest.php">
            <input type="hidden" name="project_id" id="modalProjectId">
            
            <div class="form-group">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Проект:</label>
                <p id="modalProjectName" style="font-weight: bold; padding: 8px; background-color: #f5f5f5; border-radius: 4px;"></p>
            </div>

            <div class="form-group">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Выберите задачи:</label>
                <div class="tasks-container">
                    <div id="tasksContainer">
                        <p>Загрузка задач...</p>
                    </div>
                </div>
            </div>

            <input type="hidden" name="task_id" id="selectedTaskId">

            <div class="form-group" style="text-align: center;">
                <button type="submit" class="submit-btn ">Отправить заявку</button>
            </div>
        </form>
    </div>
</div>

<script>


     function openModalWithProject(projectId, projectName) {
        document.getElementById('modalProjectId').value = projectId;
        document.getElementById('modalProjectName').textContent = projectName;
        document.querySelector('.modal-requests').style.display = 'block';
        loadTasksForProject(projectId);
    }

    function closeModal() {
        document.querySelector('.modal-requests').style.display = 'none';
    }

    function loadTasksForProject(projectId) {
    const tasksContainer = document.getElementById('tasksContainer');
    tasksContainer.innerHTML = '<p>Загрузка задач...</p>';
    
    // Получаем ID текущего пользователя из PHP
    const userId = <?php echo json_encode($userID ?? 0); ?>;
    
    // Загружаем задачи проекта и статусы заявок/назначений
    Promise.all([
        fetch(`volunteer/getTasks.php?project_id=${projectId}`).then(r => r.json()),
        fetch(`volunteer/getUserRequests.php?user_id=${userId}`).then(r => r.json()),
        fetch(`volunteer/getUserAssignments.php?user_id=${userId}`).then(r => r.json())
    ])
    .then(([tasks, userRequests, userAssignments]) => {
        if (tasks.length > 0) {
            let html = `
            <table class="task-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Описание задачи</th>
                        <th >Статус задачи</th>
                        <th>Ваш статус</th>
                    </tr>
                </thead>
                <tbody>`;
            
            tasks.forEach(task => {
                // Проверяем, есть ли заявка на эту задачу
                const hasRequest = userRequests.some(r => r.TaskID == task.TaskID);
                // Проверяем, назначена ли задача пользователю
                const isAssigned = userAssignments.some(a => a.TaskID == task.TaskID);
                
                // Определяем классы для строки
                let rowClass = '';
                let statusText = '';
                
                if (isAssigned) {
                    rowClass = 'assigned-task';
                    statusText = 'Вы выполняете';
                } else if (hasRequest) {
                    rowClass = 'requested-task';
                    statusText = 'Заявка подана';
                }
                
                html += `
                <tr class="${rowClass}">
                    <td class="task-checkbox-container">
                        ${!isAssigned && !hasRequest ? 
                          `<input type="checkbox" class="task-checkbox" value="${task.TaskID}">` : 
                          `<span class="status-icon">✓</span>`}
                    </td>
                    <td>${task.Description}</td>
                    <td><span class="task-status status-${task.Status.toLowerCase()}">${task.Status}</span></td>
                    <td><span class="user-status">${statusText}</span></td>
                </tr>`;
            });
            
            html += `</tbody></table>`;
            tasksContainer.innerHTML = html;
        } else {
            tasksContainer.innerHTML = '<p>Для этого проекта нет доступных задач</p>';
        }
    })
    .catch(error => {
        console.error('Ошибка загрузки задач:', error);
        tasksContainer.innerHTML = '<p>Ошибка загрузки данных</p>';
    });
}

// Добавляем обработчик кнопке отправки
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.querySelector('.submit-btn');
    if (submitBtn) {
        submitBtn.addEventListener('click', submitApplication);
    }
});

    function selectTask(rowElement, taskId) {
        // Убираем выделение со всех строк
        const allRows = document.querySelectorAll('.task-table tr');
        allRows.forEach(row => {
            row.classList.remove('selected-task');
            row.querySelector('.task-checkbox').checked = false;
        });
        
        // Добавляем выделение выбранной строке
        rowElement.classList.add('selected-task');
        rowElement.querySelector('.task-checkbox').checked = true;
        
        // Устанавливаем значение скрытого поля
        document.getElementById('selectedTaskId').value = taskId;
    }

    async function submitApplication(event) {
    event.preventDefault();
    
    const projectId = document.getElementById('modalProjectId').value;
    const checkboxes = document.querySelectorAll('.task-checkbox:checked');
    const userId = <?php echo json_encode($userID ?? 0); ?>;

    if (checkboxes.length === 0) {
        alert('Пожалуйста, выберите хотя бы одну задачу');
        return;
    }

    const submitBtn = document.querySelector('.submit-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Отправка...';

    let successCount = 0;
    
    for (const checkbox of checkboxes) {
        const taskId = checkbox.value;
        const taskRow = checkbox.closest('tr');
        
        try {
            const response = await fetch('volunteer/sendRequest.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: userId,
                    project_id: projectId,
                    task_id: taskId
                })
            });

            const result = await response.json();
            
            if (result.success) {
                successCount++;
                // Обновляем строку задачи
                taskRow.classList.remove('requested-task');
                taskRow.classList.add('requested-task');
               
            }
        } catch (error) {
            console.error('Ошибка при отправке:', error);
        }
    }

    if (successCount > 0) {
        alert(`Успешно отправлено ${successCount} заявок!`);
        // Обновляем список задач
        loadTasksForProject(projectId);
    } else {
        alert('Не удалось отправить заявки');
    }
    
    submitBtn.disabled = false;
    submitBtn.textContent = 'Отправить заявку';
}



// Обновляем обработчик формы
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('requestForm');
    if (form) {
        form.addEventListener('submit', submitApplication);
    }
});


</script>


</section>

        <!-- Раздел заявок -->
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
                    if (isset($userID)) {
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
                            while ($application = mysqli_fetch_assoc($applications_result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($application['RequestID']) . "</td>";
                                echo "<td>" . htmlspecialchars($application['ProjectName']) . "</td>";
                                echo "<td>" . htmlspecialchars($application['TaskDescription']) . "</td>";
                                echo "<td>" . htmlspecialchars($application['RequestStatus']) . "</td>";
                                echo "<td>";
                                if ($application['RequestStatus'] !== 'Одобрена') {
                                    echo "<form method='post' action='volunteer/cancelRequest.php'>
                                            <input type='hidden' name='request_id' value='" . intval($application['RequestID']) . "'>
                                            <button type='submit' class='CancelReqBut'>Отменить заявку</button>
                                          </form>";
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

        <!-- Раздел задач -->
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
                    if (isset($userID)) {
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
                            while ($task = mysqli_fetch_assoc($tasks_result)) {
                                echo "<tr>";
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

        


    </main>

    <footer class="footer">
        <div class="footer-content">
            <p>© 2025 Волонтерские проекты. Все права защищены.</p>
            <div class="footer-links">
                <a href="#">О нас</a>
                <a href="#">Контакты</a>
                <a href="#">Политика конфиденциальности</a>
                <a href="#">Условия использования</a>
            </div>
            <p>Свяжитесь с нами: info@volunteerprojects.ru</p>
        </div>
    </footer>
</div>
</body>
</html>
