<?php
include 'func\db_connection.php';
include 'func\function.php';
include 'admin\add_user.php';
include 'admin\delete_user.php';
include 'admin\update_user.php';
include 'admin\selectUsr.php';
include 'admin\get_task.php';
include 'admin\deleteusertask.php';
include 'admin\export.php';
include 'admin\logout.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$loginquery = "SELECT u.UserID, u.Name, u.Surname, uc.Login, uc.Password
FROM users u
LEFT JOIN usercredentials uc ON u.UserID = uc.UserID
ORDER BY u.UserID";

$projectsQuery = "SELECT * FROM projects";

$tasksQuery = "SELECT tasks.TaskID, tasks.Description, tasks.Status, taskinfo.Location, taskinfo.Date, projects.ProjectName
    FROM tasks
    JOIN taskinfo ON tasks.TaskID = taskinfo.TaskID
    JOIN projects ON tasks.ProjectID = projects.ProjectID;";

$usersQuery = "
SELECT
    u.UserID,  
    u.Name,
    u.Surname,
    u.UserSkills,
    u.Email,
    u.Phone,
    t.Description AS 'Task',
    p.ProjectName
FROM
    user_tasks ut
JOIN users u ON ut.UserID = u.UserID
JOIN tasks t ON ut.TaskID = t.TaskID
JOIN taskinfo ti ON t.TaskID = ti.TaskID
JOIN projects p ON t.ProjectID = p.ProjectID;";

$usersExtendedQuery = "
SELECT
    u.UserID,
    u.Name,
    u.Surname,
    u.Email,
    u.Phone,
    u.DateOfBirth,
    u.Gender,
    u.Role,
    u.UserSkills,
    u.Address
FROM
    users u";


 $currentUsername = mysqli_real_escape_string($connect, $_SESSION["username"]);
$adminProfileQuery = "
    SELECT u.Name, u.Surname, u.Email, u.Phone, u.DateOfBirth, u.Gender, u.Address
    FROM users u
    JOIN usercredentials uc ON u.UserID = uc.UserID
    WHERE uc.Login = '$currentUsername' AND u.Role = 'Администратор'
    LIMIT 1
";
$adminProfileResult = mysqli_query($connect, $adminProfileQuery);

if ($adminProfileResult && mysqli_num_rows($adminProfileResult) === 1) {
    $adminProfile = mysqli_fetch_assoc($adminProfileResult);
} else {
    header("Location: index.php");
    exit();
}




$usersExtended = fetchAll($usersExtendedQuery);
$projects = fetchAll($projectsQuery);
$tasks = fetchAll($tasksQuery);
$users = fetchAll($usersQuery);
$login = fetchAll($loginquery);

// Группировка пользователей по проектам
$groupedUsers = [];
foreach ($users as $user) {
    $projectName = $user['ProjectName'];
    if (!isset($groupedUsers[$projectName])) {
        $groupedUsers[$projectName] = [];
    }
    $groupedUsers[$projectName][] = $user;
}

$groupedData = [];
foreach ($users as $user) {
    $projectName = $user['ProjectName'];
    $userKey = $user['Name'] . ' ' . $user['Surname'] . ' ' . $user['Email']; // Уникальный ключ пользователя

    if (!isset($groupedData[$projectName])) {
        $groupedData[$projectName] = [];
    }

    if (!isset($groupedData[$projectName][$userKey])) {
        $groupedData[$projectName][$userKey] = [
            'Name' => $user['Name'],
            'Surname' => $user['Surname'],
            'UserSkills' => $user['UserSkills'],
            'Email' => $user['Email'],
            'Phone' => $user['Phone'],
            'Tasks' => []
        ];
    }

    $groupedData[$projectName][$userKey]['Tasks'][] = $user['Task'];
}

// Получение заявок на участие
$pendingRequestsQuery = "
    SELECT r.RequestID, u.Name, u.Surname, p.ProjectName, t.Description AS TaskDescription, r.Status, r.UpdatedAt
    FROM users_pending_approval r
    JOIN users u ON r.UserID = u.UserID
    JOIN projects p ON r.ProjectID = p.ProjectID
    JOIN tasks t ON r.TaskID = t.TaskID
    WHERE r.Status = 'В процессе'
    ORDER BY r.RequestID DESC
";
$pendingRequests = fetchAll($pendingRequestsQuery);

// Получение отклоненных заявок
$rejectedRequestsQuery = "
    SELECT r.RequestID, u.Name, u.Surname, p.ProjectName, t.Description AS TaskDescription, r.Status, r.UpdatedAt
    FROM users_pending_approval r
    JOIN users u ON r.UserID = u.UserID
    JOIN projects p ON r.ProjectID = p.ProjectID
    JOIN tasks t ON r.TaskID = t.TaskID
    WHERE r.Status = 'Отклонена'
    ORDER BY r.UpdatedAt DESC
";
$rejectedRequests = fetchAll($rejectedRequestsQuery);

// Получение принятых заявок
$approvedRequestsQuery = "
    SELECT r.RequestID, u.Name, u.Surname, p.ProjectName, t.Description AS TaskDescription, r.Status, r.UpdatedAt
    FROM users_pending_approval r
    JOIN users u ON r.UserID = u.UserID
    JOIN projects p ON r.ProjectID = p.ProjectID
    JOIN tasks t ON r.TaskID = t.TaskID
    WHERE r.Status = 'Одобрена'
    ORDER BY r.UpdatedAt DESC
";
$approvedRequests = fetchAll($approvedRequestsQuery);

// Закрытие соединения (после всех операций)
mysqli_close($connect);
?>

// Closing the database connection
mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Справочники</title>
    <link rel="stylesheet" href="adminstyle.css">
    <script src="admin/script.js"></script>
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>



</head>
<body>




<div class="tab">
    <button class="tablinks" onclick="openTab(event, 'projects')" id="projectsTabButton">Проекты</button>
    <button class="tablinks" onclick="openTab(event, 'tasks')" id="tasksTabButton">Задачи</button>
    <button class="tablinks" onclick="openTab(event, 'usersTab')" id="usersTabButton">Участия</button>
    <button class="tablinks" onclick="openTab(event, 'userCredentialsTab')" id="userCredentialsButton">Пользователи</button>
    <button class="tablinks" onclick="openTab(event, 'userlogTab')" id="userlogButton">Учетные записи</button>
    <button class="tablinks" onclick="openTab(event, 'requestsTab')" id="requestsTabButton">Заявки</button>
    <button class="tablinks" onclick="openTab(event, 'profile')" id="profileTabButton">Профиль</button>
    <form method="post" action="index.php">
        <button type="submit" class="logout-button">Выход</button>
        
    </form>
        
</div>

<style>#profile label {
    display: inline-block;
    width: 150px;
    font-weight: bold;
    z-index: -10;
}

#profile input[type="text"],
#profile input[type="email"],
#profile input[type="date"],
#profile select {
    width: 300px;
    padding: 8px;
    margin-bottom: 10px;
    z-index: -10;
}

#profile button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    z-index: -10;
}

#profile button:hover {
    background-color: #0056b3;
    z-index: -10;
}
</style>
<div id="profile"  class="tabcontent">
    
    <h2>Информация о профиле</h2>
    <form id="profileForm" method="post" action="admin/update_profile.php">
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($adminProfile['Name']); ?>" required><br><br>

        <label for="surname">Фамилия:</label>
        <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($adminProfile['Surname']); ?>" required><br><br>

        <label for="email">Электронная почта:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($adminProfile['Email']); ?>" required><br><br>

        <label for="phone">Телефон:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($adminProfile['Phone']); ?>"><br><br>


        <button type="submit">Сохранить изменения</button>
    </form>
</div>

<div id="userlogTab" class="tabcontent">
    <h2>Учетные записи</h2>
    <table>
        <thead>
            <tr>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Логин</th>
                <th>Пароль</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($login as $userCredentials): ?>
    <tr>
        <td><?= htmlspecialchars($userCredentials['Name']) ?></td>
        <td><?= htmlspecialchars($userCredentials['Surname']) ?></td>
        <td>
            <?= $userCredentials['Login'] ? htmlspecialchars($userCredentials['Login']) : '<span style="color: red;">Без логина</span>' ?>
        </td>
        <td>
            <?= $userCredentials['Password'] ? htmlspecialchars($userCredentials['Password']) : '<span style="color: red;">Без пароля</span>' ?>
        </td>
        <td>
        <button class="action-button" onclick="openEditCredentials(this, <?= $userCredentials['UserID']; ?>, '<?= htmlspecialchars($userCredentials['Login'] ?? '', ENT_QUOTES); ?>', '<?= htmlspecialchars($userCredentials['Password'] ?? '', ENT_QUOTES); ?>');">
    <img src="ico/ed.png" alt="Edit" style="width: 3vh; height: 3vh;">
</button>


            
        </td>
    </tr>
<?php endforeach; ?>

        </tbody>
    </table>
</div>

<div id="editCredentialsModal">
    <form action="admin\updateCR.php" method="post">
        <input type="hidden" id="editUserId" name="userId">
        <label for="editLogin">Логин:</label>
        <input type="text" id="editLogin" name="login">
        <label for="editPassword">Пароль:</label>
        <input type="text" id="editPassword" name="password">
        <button type="submit">Сохранить</button>
        <button type="button" onclick="closeEditModal()">Отменить</button>
    </form>
</div>









<script>
function openEditCredentials(button, userId, currentLogin, currentPassword) {
    var modal = document.getElementById('editCredentialsModal');
    var editUserId = document.getElementById('editUserId');
    var editLogin = document.getElementById('editLogin');
    

    // Установка значений формы
    editUserId.value = userId;
    editLogin.value = currentLogin;
   

    // Получение координат кнопки
    var rect = button.getBoundingClientRect();
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

    // Позиционирование модального окна
    modal.style.display = 'block';
    modal.style.position = 'absolute';
    modal.style.top = (rect.bottom + scrollTop) + 'px'; // ниже кнопки
    modal.style.left = (rect.left + scrollLeft) + 'px'; // выравнивание по левому краю кнопки

    // Предотвращение выхода модального окна за пределы экрана
    if (modal.offsetWidth + rect.left > window.innerWidth) {
        modal.style.left = (window.innerWidth - modal.offsetWidth - 20) + 'px'; // 20px - небольшой отступ от края
    }
}


function closeEditModal() {
    document.getElementById('editCredentialsModal').style.display = 'none';
}

</script>

<?php
$records_per_page = 10;
$total_records = count($projects); // Количество всех проектов
$total_pages = ceil($total_records / $records_per_page); // Количество страниц

// Получаем текущую страницу из запроса (по умолчанию 1)
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // Убедиться, что страница корректна

// Вычисляем с какого проекта начинать на текущей странице
$start_from = ($current_page - 1) * $records_per_page;

// Получаем нужные проекты для текущей страницы
$projects_on_page = array_slice($projects, $start_from, $records_per_page);
?>


    <!-- Projects tab -->
    <div id="projects" class="tabcontent">
        <div class="form-container">
            <h2>Добавить проект</h2>
            <form method="post" action="admin/add_project.php">
                <label for="projectName">Название проекта:</label>
                <input type="text" id="projectName" name="projectName" required>
                <label for="startDate">Дата начала:</label>
                <input type="date" id="startDate" name="startDate" required>
                <label for="endDate">Дата окончания:</label>
                <input type="date" id="endDate" name="endDate" required>
                <label for="status">Статус:</label>
                <select id="status" name="status" required>
                    <option value="Активен">Активен</option>
                    <option value="Завершен">Завершен</option>
                    <option value="Отменен">Отменен</option>
                    <option value="Планируется">Планируется</option>
                </select>
                <button class="addbt" type="submit" >Добавить</button>
            </form>
        </div>
        <!-- Projects directory -->
        <div class="directory" id="projectsTable">
    <h2>Справочник проектов</h2>
    <table>
        <thead>
            <tr>
                <th>ID Проекта</th>
                <th>Название Проекта</th>
                <th>Дата Начала</th>
                <th>Дата Окончания</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($projects_on_page)): ?>
                <?php foreach ($projects_on_page as $project): ?>
                    <tr>
                        <td><?php echo $project['ProjectID']; ?></td>
                        <td><?php echo $project['ProjectName']; ?></td>
                        <td><?php echo formatDate($project['StartDate']); ?></td>
                        <td><?php echo formatDate($project['EndDate']); ?></td>
                        <td><?php echo $project['Status']; ?></td>
                        <td>
                            <button class="action-button" onclick="editProject(this, <?php echo $project['ProjectID']; ?>, '<?php echo htmlspecialchars($project['ProjectName'], ENT_QUOTES); ?>', '<?php echo $project['StartDate']; ?>', '<?php echo $project['EndDate']; ?>', '<?php echo $project['Status']; ?>')">
                                <img src="ico/ed.png" alt="Edit"  style="width: 3vh; height: 3vh; margin: 0px; margin-left:20px">
                            </button>
                            <button class="action-button DLT" onclick="deleteProject(<?php echo $project['ProjectID']; ?>)">
                                <img src="ico/dl.png" alt="Delete" style="width: 3vh; height: 3vh; margin: 0px">
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Нет данных о проектах.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Пагинация -->
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=1">&laquo; Первая</a>
            <a href="?page=<?php echo $current_page - 1; ?>">&lt; Предыдущая</a>
        <?php endif; ?>

        <?php
        // Отображаем первые 5 страниц и последние 5 страниц
        $page_range = 2; // Количество страниц до и после текущей, которое будет показываться
        $start_page = max(1, $current_page - $page_range);
        $end_page = min($total_pages, $current_page + $page_range);

        // Для отображения первых страниц
        if ($start_page > 1) {
            echo '<a href="?page=1">1</a>';
            if ($start_page > 2) {
                echo '<span>...</span>';
            }
        }

        // Отображаем страницы в пределах диапазона
        for ($page = $start_page; $page <= $end_page; $page++) {
            echo '<a href="?page=' . $page . '" class="' . ($page == $current_page ? 'active' : '') . '">' . $page . '</a>';
        }

        // Для отображения последних страниц
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo '<span>...</span>';
            }
            echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
        }
        ?>

        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>">Следующая &gt;</a>
            <a href="?page=<?php echo $total_pages; ?>">Последняя &raquo;</a>
        <?php endif; ?>
    </div>
</div>
</div>



    
    
<script>
function processRequest(requestID, action) {
    let actionText = action === 'approve' ? 'одобрить' : 'отклонить';
    if (confirm('Вы уверены, что хотите ' + actionText + ' эту заявку?')) {
        window.location.href = 'admin/process_request.php?action=' + action + '&request_id=' + requestID;
    }
}

</script>
<!-- Заявки вкладка -->
<div id="requestsTab" class="tabcontent">
    <h2>Заявки на участия</h2>
    <table>
        <thead>
            <tr>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Проект</th>
                <th>Задача</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pendingRequests)): ?>
                <?php foreach ($pendingRequests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['Name']); ?></td>
                        <td><?php echo htmlspecialchars($request['Surname']); ?></td>
                        <td><?php echo htmlspecialchars($request['ProjectName']); ?></td>
                        <td><?php echo htmlspecialchars($request['TaskDescription']); ?></td>
                        <td>
                            <button class="addbt"  processRequest(<?php echo $request['RequestID']; ?>, 'approve')">Одобрить</button>
                            <button class="delbt"  onclick="processRequest(<?php echo $request['RequestID']; ?>, 'reject')">Отклонить</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Нет новых заявок.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>


 <h3>Принятые заявки</h3>
    <table>
        <thead>
            <tr>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Проект</th>
                <th>Задача</th>
                <th>Дата одобрения</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($approvedRequests)): ?>
                <?php foreach ($approvedRequests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['Name']); ?></td>
                        <td><?php echo htmlspecialchars($request['Surname']); ?></td>
                        <td><?php echo htmlspecialchars($request['ProjectName']); ?></td>
                        <td><?php echo htmlspecialchars($request['TaskDescription']); ?></td>
                        <td><?php echo htmlspecialchars(formatDate($request['UpdatedAt'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Нет принятых заявок.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Отклоненные заявки</h2>

    <table>
        <thead>
            <tr>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Проект</th>
                <th>Задача</th>
                <th>Дата отклонения</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rejectedRequests)): ?>
                <?php foreach ($rejectedRequests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['Name']); ?></td>
                        <td><?php echo htmlspecialchars($request['Surname']); ?></td>
                        <td><?php echo htmlspecialchars($request['ProjectName']); ?></td>
                        <td><?php echo htmlspecialchars($request['TaskDescription']); ?></td>
                        <td><?php echo htmlspecialchars(formatDate($request['UpdatedAt'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Нет отклоненных заявок.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
  
</div>


    <!-- Tasks tab -->
    <div id="tasks" class="tabcontent">
        <div class="form-container">
            <h2>Добавить задачу</h2>
            <form method="post" action="admin/add_task.php">
                <label for="taskDescription">Описание задачи:</label>
                <input type="text" id="taskDescription" name="taskDescription" required>
                <label for="projectID">Выберите проект:</label>
                <select id="projectID" name="projectID" required>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?php echo $project['ProjectID']; ?>"><?php echo $project['ProjectName']; ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="taskStatus">Статус задачи:</label>
                <select id="taskStatus" name="taskStatus" required>
                    <option value="Активен">Активен</option>
                    <option value="Завершен">Завершен</option>
                    <option value="Отменен">Отменен</option>
                    <option value="Планируется">Планируется</option>
                </select>
                <label for="taskLocation">Местоположение задачи:</label>
                <input type="text" id="taskLocation" name="taskLocation" required>
                <label for="taskDate">Дата задачи:</label>
                <input type="date" id="taskDate" name="taskDate" required>
                <button class="addbt" type="submit" id="addButton">Добавить</button>
            </form>
        </div>
   
        
        <?php
$records_per_page = 10; // Количество задач на одной странице
$total_records = count($tasks); // Количество всех задач
$total_pages = ceil($total_records / $records_per_page); // Количество страниц

// Получаем текущую страницу из запроса (по умолчанию 1)
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // Убедиться, что страница корректна

// Вычисляем с какого элемента начинать на текущей странице
$start_from = ($current_page - 1) * $records_per_page;

// Получаем нужные задачи для текущей страницы
$tasks_on_page = array_slice($tasks, $start_from, $records_per_page);
?>


      <!-- Tasks directory -->
<div class="directory" id="tasksTable">
    <h2>Справочник задач</h2>
    <table>
        <thead>
            <tr>
                <th>ID Задачи</th>
                <th>Описание</th>
                <th>Название Проекта</th>
                <th>Место</th>
                <th>Дата</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tasks_on_page)): ?>
                <?php foreach ($tasks_on_page as $task): ?>
                    <tr>
                        <td><?php echo $task['TaskID']; ?></td>
                        <td><?php echo $task['Description']; ?></td>
                        <td><?php echo $task['ProjectName']; ?></td>
                        <td><?php echo $task['Location']; ?></td>
                        <td><?php echo formatDate($task['Date']); ?></td>
                        <td><?php echo $task['Status']; ?></td>
                        <td>
                            <button class="action-button" onclick="editTask(this, <?php echo $task['TaskID']; ?>, '<?php echo htmlspecialchars($task['Description'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($task['ProjectName'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($task['Location'], ENT_QUOTES); ?>', '<?php echo $task['Date']; ?>', '<?php echo htmlspecialchars($task['Status'], ENT_QUOTES); ?>');">
                                <img src="ico/ed.png" alt="Edit" style="width: 3vh; height: 3vh; margin: 0px; margin-left:0px">
                            </button>
                            <button class="action-button DLT" onclick="deleteTask(<?php echo $task['TaskID']; ?>)">
                                <img src="ico/dl.png" alt="Delete" style="width: 4vh; height: 4vh; margin: 0px">
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Нет данных о задачах.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Пагинация -->
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=1">&laquo; Первая</a>
            <a href="?page=<?php echo $current_page - 1; ?>">&lt; Предыдущая</a>
        <?php endif; ?>

        <?php
        // Отображаем первые 5 страниц и последние 5 страниц
        $page_range = 2; // Количество страниц до и после текущей, которое будет показываться
        $start_page = max(1, $current_page - $page_range);
        $end_page = min($total_pages, $current_page + $page_range);

        // Для отображения первых страниц
        if ($start_page > 1) {
            echo '<a href="?page=1">1</a>';
            if ($start_page > 2) {
                echo '<span>...</span>';
            }
        }

        // Отображаем страницы в пределах диапазона
        for ($page = $start_page; $page <= $end_page; $page++) {
            echo '<a href="?page=' . $page . '" class="' . ($page == $current_page ? 'active' : '') . '">' . $page . '</a>';
        }

        // Для отображения последних страниц
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo '<span>...</span>';
            }
            echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
        }
        ?>

        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>">Следующая &gt;</a>
            <a href="?page=<?php echo $total_pages; ?>">Последняя &raquo;</a>
        <?php endif; ?>
    </div>
</div>


        <!-- Edit task form -->
        <div id="editTaskForm" class="edit-form" style="display: none;">
            <h2>Редактировать задачу</h2>
            <form id="taskForm" onsubmit="return saveTaskChanges()">
                <label for="editTaskDescription">Описание задачи:</label>
                <input type="text" id="editTaskDescription" name="editTaskDescription" required>
                <label for="editTaskLocation">Местоположение задачи:</label>
                <input type="text" id="editTaskLocation" name="editTaskLocation" required>
                <label for="editTaskDate">Дата задачи:</label>
                <input type="date" id="editTaskDate" name="editTaskDate" required>
                <label for="editTaskStatus">Статус задачи:</label>
                <select id="editTaskStatus" name="editTaskStatus" required>
                    <option value="Активен">Активен</option>
                    <option value="Завершен">Завершен</option>
                    <option value="Отменен">Отменен</option>
                    <option value="Планируется">Планируется</option>
                </select>
                <!-- Hidden field to pass task ID -->
                <input type="hidden" id="editTaskId" name="editTaskId">
                <!-- Button to save changes -->
                <button class="savech" type="submit">Сохранить</button>
                <!-- Button to cancel editing -->
                <button class="canedit" type="button" onclick="cancelEditTask()">Отменить</button>
            </form>
        </div>
    </div>

         <!-- Форма для редактирования проекта -->
         <div id="editProjectForm" class="edit-form" style="display: none;">
    <h2>Редактировать проект</h2>
    <form id="projectForm" onsubmit="return saveChanges()">
        <label for="editProjectName">Название проекта:</label>
        <input type="text" id="editProjectName" name="projectName" required>  

        <label for="editStartDate">Дата начала:</label>
        <input type="date" id="editStartDate" name="startDate" required>  

        <label for="editEndDate">Дата окончания:</label>
        <input type="date" id="editEndDate" name="endDate" required>  

        <label for="editStatus">Статус:</label>
        <select id="editStatus" name="status" required>  
            <option value="Активен">Активен</option>
            <option value="Завершен">Завершен</option>
            <option value="Отменен">Отменен</option>
            <option value="Планируется">Планируется</option>
        </select>

        <input type="hidden" id="editProjectId" name="projectId">  

        <button class="savech" type="submit">Сохранить</button>
        <button class="canedit" type="button" onclick="cancelEdit()">Отменить</button>
    </form>
</div>


<iframe id="printFrame" style="display: none;"></iframe>

<div id="usersTab" class="tabcontent">
    <div class="content-layout">
        <!-- Левая колонка для формы -->
        <div class="form-panel">
            <div class="form-container">
                <h2>Добавить участие</h2>
                <form method="post" action="admin/add_participation.php" id="addParticipationForm">
                    <label for="userID">Выберите пользователя:</label>
                    <!-- предполагается, что $selectHTML уже содержит необходимый HTML -->
                    <?= $selectHTML; ?>
                    <label for="projectID">Выберите проект:</label>
                    <select id="projectSelect" name="projectID" onchange="updateTasks(this.value);">
                        <option value="">Выберите проект</option>
                        <!-- предполагается, что $projects загружены и обрабатываются корректно -->
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= htmlspecialchars($project['ProjectID']); ?>">
                                <?= htmlspecialchars($project['ProjectName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="taskID">Выберите задачу:</label>
                    <select id="taskSelect" name="taskID">
                        <option value="">Сначала выберите проект</option>
                    </select>
                    <button class="addbt" type="submit">Добавить</button>
                </form>
                <label for="printPDF"style="font-size: 15px;">Печать таблицы (Пользователь - Проект)</label>
                <button name = "printPDF" class='generate' onclick='generatePDF()'>На печать </button>                              

            </div>
        </div>
        
        <!-- Вывод таблицы с данными -->
        <div id = "napech" class="table-panel" style="margin-top: 20px;">
            <table>
                <thead>
                    <tr>
                        
                        <th>Имя</th>
                        <th>Фамилия</th>
                        <th>Навыки</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Описание задачи</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groupedData as $projectName => $users): ?>
                        <tr>
                        <td colspan="7" style="color: black;text-align: center; font-weight: bold; background: #FFF2F2;   "><?= htmlspecialchars($projectName) ?></td>
                        </tr>
                        <?php foreach ($users as $user): ?>
                            <tr>    
                                
                                <td><?= htmlspecialchars($user['Name']) ?></td>
                                <td><?= htmlspecialchars($user['Surname']) ?></td>
                                <td><?= htmlspecialchars($user['UserSkills']) ?></td>
                                <td><?= htmlspecialchars($user['Email']) ?></td>
                                <td><?= htmlspecialchars($user['Phone']) ?></td>
                                <td>
                                    <ul>
                                        <?php foreach ($user['Tasks'] as $task): ?>
                                            <li><?= htmlspecialchars($task) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                            </tr>
                            
                        <?php endforeach; ?>
                        <tr>
                                <td style="border-color: white; background-color: white;"></td>
                                <td style="border-color: white; background-color: white;"></td>
                                <td style="border-color: white; background-color: white;"></td>
                                <td style="border-color: white; background-color: white;"></td>
                                <td style="border-color: white; background-color: white;"></td>
                                <td style="border-color: white; background-color: white;"></td>
                                
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<script>

const pdf = new window.jspdf.jsPDF({
    orientation: 'landscape',
    unit: 'pt',
    format: 'a4'
});

function generatePDF() {
    let frame = document.getElementById('printFrame');
    let frameDoc = frame.contentDocument || frame.contentWindow.document;

    // Определение стилей для печати
    const styles = `
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: white;
                margin: 0;
                padding: 0;
            }
            table, th, td {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                border: 1px solid #ddd;
                text-align: center;
                padding: 8px;
            }
            th {
                background-color: #f2f2f2;
            }
            .table-panel table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            .table-panel th, .table-panel td {
                border: 1px solid #ccc;
                padding: 10px 15px;
                text-align: left;
            }
            .table-panel th {
                background-color: #f2f2f2;
                color: #333;
            }
            .table-panel tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .table-panel ul {
                padding-left: 20px;
                margin: 0;
            }
            .table-panel li {
                list-style-type: disc;
            }
        </style>
    `;

    // Получение HTML содержимого, которое нужно напечатать
    const content = document.getElementById('napech').innerHTML;
    
    // Запись HTML и стилей в документ iframe
    frameDoc.open();
    frameDoc.write('<html><head><title>Печать таблицы</title>' + styles + '</head><body>');
    frameDoc.write(content);
    frameDoc.write('</body></html>');
    frameDoc.close();

    // Вызов окна печати после полной загрузки содержимого iframe
    frame.onload = function() {
        frame.contentWindow.print();
    }
}







document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-task');    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userTaskID = this.getAttribute('data-user-task-id');
            performAction('delete_task', userTaskID);
        });
    });

    const removeButtons = document.querySelectorAll('.remove-user');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userTaskID = this.getAttribute('data-user-task-id');
            performAction('remove_user_from_project', userTaskID);
        });
    });
});

function performAction(action, userTaskID) {
    fetch('delete_user_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=${action}&userTaskID=${userTaskID}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Operation successful');
            window.location.reload(); // Reload the page to update the table
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error performing the operation');
    });
}
</script>


<div id="userCredentialsTab" class="tabcontent">
    <div class="form-container">
        <h2>Добавить пользователя</h2>
        <form method="post" action="admin/add_user.php">
    <label for="userName">Имя:</label>
    <input type="text" id="userName" name="userName" required>

    <label for="userSurname">Фамилия:</label>
    <input type="text" id="userSurname" name="userSurname" required>

    <label for="userEmail">Электронная почта:</label>
    <input type="text" id="userEmail" name="userEmail" required>

    <label for="userPhone">Телефон:</label>
    <input type="text" id="userPhone" name="userPhone">

    <label for="userDOB">Дата рождения:</label>
    <input type="date" id="userDOB" name="userDOB">

    <label for="userGender">Пол:</label>
    <select id="userGender" name="userGender">
        <option value="м">Мужской</option>
        <option value="ж">Женский</option>
    </select>

    <label for="userRole">Роль:</label>
    <select id="userRole" name="userRole">
        <option value="Волонтер">Волонтер</option>
        <option value="Администратор">Администратор</option>
    </select>

    <label for="userAddress">Адрес:</label>
    <input type="text" id="userAddress" name="userAddress">

    <label for="userSkills">Умения:</label>
    <textarea id="userSkills" name="userSkills" rows="4" cols="50" style="resize: none;" maxlength="255"></textarea>

                                
    <button class="addbt" type="submit" id="addButton">Добавить</button>
    </form>
</div>

<?php
$records_per_page = 10; // Количество пользователей на одной странице
$total_records = count($usersExtended); // Количество всех пользователей
$total_pages = ceil($total_records / $records_per_page); // Количество страниц

// Получаем текущую страницу из запроса (по умолчанию 1)
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // Убедиться, что страница корректна

// Вычисляем с какого элемента начинать на текущей странице
$start_from = ($current_page - 1) * $records_per_page;

// Получаем нужных пользователей для текущей страницы
$users_on_page = array_slice($usersExtended, $start_from, $records_per_page);
?>

 
<div class="directory" id="usersTable">
    <h2>Справочник пользователей</h2>
    <table>
        <thead>
            <tr>
                <th>UserID</th>
                <th>Имя и фамилия</th>
                <th>Эл. Почта</th>
                <th>Телефон</th>
                <th>Дата рождения</th>
                <th>Пол</th>
                <th>Адрес</th>
                <th>Умения</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users_on_page as $user): ?>
                <tr data-user-id="<?= htmlspecialchars($user['UserID']); ?>"
                    data-name="<?= htmlspecialchars($user['Name']); ?>"
                    data-surname="<?= htmlspecialchars($user['Surname']); ?>"
                    data-email="<?= htmlspecialchars($user['Email']); ?>"
                    data-phone="<?= htmlspecialchars($user['Phone']); ?>"
                    data-dob="<?= htmlspecialchars($user['DateOfBirth']); ?>"  
                    data-address="<?= htmlspecialchars($user['Address']); ?>"
                    data-gender="<?= htmlspecialchars($user['Gender']); ?>"
                    data-role="<?= htmlspecialchars($user['Role']); ?>">
                    <td><?= htmlspecialchars($user['UserID']); ?></td>
                    <td><?= htmlspecialchars($user['Name']) . ' ' . htmlspecialchars($user['Surname']); ?></td>
                    <td><?= htmlspecialchars($user['Email']); ?></td>
                    <td><?= htmlspecialchars($user['Phone']); ?></td>
                    <td><?= htmlspecialchars(formatDate($user['DateOfBirth'])); ?></td>
                    <td><?= htmlspecialchars($user['Gender']); ?></td>
                    <td><?= htmlspecialchars($user['Address']); ?></td>
                    <td><?= htmlspecialchars($user['UserSkills']); ?></td>
                    <td><?= htmlspecialchars($user['Role']); ?></td>
                    <td>
                        <button class="action-button" onclick="editUser(this, '<?= htmlspecialchars($user['UserID']) ?>');">
                            <img src="ico/ed.png" alt="Edit" style="width: 4vh; height: 4vh;">
                        </button>
                        <button class="action-button DLT" onclick="deleteUser('<?= htmlspecialchars($user['UserID']) ?>')">
                            <img src="ico/dl.png" alt="Delete" style="width: 4vh; height: 4vh;">
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Пагинация -->
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=1">&laquo; Первая</a>
            <a href="?page=<?php echo $current_page - 1; ?>">&lt; Предыдущая</a>
        <?php endif; ?>

        <?php
        // Отображаем первые 5 страниц и последние 5 страниц
        $page_range = 2; // Количество страниц до и после текущей, которое будет показываться
        $start_page = max(1, $current_page - $page_range);
        $end_page = min($total_pages, $current_page + $page_range);

        // Для отображения первых страниц
        if ($start_page > 1) {
            echo '<a href="?page=1">1</a>';
            if ($start_page > 2) {
                echo '<span>...</span>';
            }
        }

        // Отображаем страницы в пределах диапазона
        for ($page = $start_page; $page <= $end_page; $page++) {
            echo '<a href="?page=' . $page . '" class="' . ($page == $current_page ? 'active' : '') . '">' . $page . '</a>';
        }

        // Для отображения последних страниц
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo '<span>...</span>';
            }
            echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
        }
        ?>

        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>">Следующая &gt;</a>
            <a href="?page=<?php echo $total_pages; ?>">Последняя &raquo;</a>
        <?php endif; ?>
    </div>
</div>



<form id="editUserForm" style="display:none;">
    <h2>Редактировать пользователя</h2>
    <input type="hidden" id="editUserId">
    <input type="text" id="editUserName" placeholder="Имя">
    <input type="text" id="editUserSurname" placeholder="Фамилия">
    <input type="email" id="editUserEmail" placeholder="Email">
    <input type="text" id="editUserPhone" placeholder="Телефон">
    <input type="date" id="editUserDOB" placeholder="Дата рождения"> <!-- Добавленное поле -->
    <input type="text" id="editUserAddress" placeholder="Адрес"> <!-- Добавленное поле -->
    <select id="editUserGender">
        <option value="м">Мужской</option>
        <option value="ж">Женский</option>
    </select>
    <select id="editUserRole">
        <option value="Волонтер">Волонтер</option>
        <option value="Администратор">Администратор</option>
    </select>
    <button type="button" onclick="saveUserChanges()">Сохранить</button>
    <button type="button" onclick="CancelUserChanges()">Отменить</button>
</form>








</body>
</html>
