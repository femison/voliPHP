// window.onload = function() {
//     var activeTabId = localStorage.getItem("activeTabId");
//     if (activeTabId) {
//         var tabButton = document.getElementById(activeTabId);
//         if (tabButton) {
//             tabButton.click();
//         }
        
//     } else {
//         document.getElementById("projectsTabButton").click(); 
//     }
// };


window.onload = function() {
    var activeTabId = localStorage.getItem("activeTabId");
    if (activeTabId && document.getElementById(activeTabId)) {
        document.getElementById(activeTabId).click();
    } else {
        
        document.getElementById("projectsTabButton").click();
        localStorage.setItem("activeTabId", "projectsTabButton"); 
    }
};

document.addEventListener("DOMContentLoaded", function() {
    var activeTabId = localStorage.getItem('activeTabId');
    if (activeTabId) {
        var tabButton = document.getElementById(activeTabId);
        if (tabButton) {
            tabButton.click();
        } else {
            console.error('No tab button with ID:', activeTabId);
        }
    }
});






function openTab(event, tabName) {
    var tabcontent = document.getElementsByClassName("tabcontent");
    var tablinks = document.getElementsByClassName("tablinks");

    // Скрываем все вкладки
    for (var i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Убираем класс 'active' у всех кнопок
    for (var i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }

    var activeTab = document.getElementById(tabName);
    if (activeTab) {
        activeTab.style.display = "block"; // Показываем нужную вкладку
    } else {
        console.error(`Вкладка с именем "${tabName}" не найдена.`);
        return;
    }

    if (event && event.currentTarget) {
        event.currentTarget.classList.add("active"); // Делаем кнопку активной
        localStorage.setItem('activeTabId', event.currentTarget.id); // Сохраняем активную вкладку
    }
}


function editProject(button, projectId, projectName, startDate, endDate, status) {
    // Получаем элементы формы для редактирования
    var editProjectForm = document.getElementById("editProjectForm");
    var editProjectName = document.getElementById("editProjectName");
    var editStartDate = document.getElementById("editStartDate");
    var editEndDate = document.getElementById("editEndDate");
    var editStatus = document.getElementById("editStatus");
    var editProjectId = document.getElementById("editProjectId");

    if (editProjectName && editStartDate && editEndDate && editStatus && editProjectId) {
        // Заполняем форму данными о проекте
        editProjectName.value = projectName;
        editStartDate.value = startDate;
        editEndDate.value = endDate;
        editStatus.value = status;
        editProjectId.value = projectId;

        // Получаем позицию кнопки и прокрутку страницы
        var rect = button.getBoundingClientRect();
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Позиционируем форму редактирования под кнопкой и смещаем влево
        editProjectForm.style.position = "absolute";
        editProjectForm.style.top = (rect.bottom + scrollTop) + 'px'; // Позиция под кнопкой
        
        // Смещаем форму влево. Например, на 200 пикселей левее, чем начало кнопки
        var leftShift = 200; // Можно регулировать этот параметр
        editProjectForm.style.left = (rect.left - leftShift) + 'px'; // Смещение влево

        editProjectForm.style.display = 'block';
    } else {
        console.error("Не удалось найти элементы формы редактирования проекта.");
    }
}



function reloadPage() {
    localStorage.setItem('openTab', 'userCredentialsTab');
    location.reload();
}

function cancelEdit() {
        // Скрываем форму для редактирования
        document.getElementById("editProjectForm").style.display = "none";
        return false; // Отменяем отправку формы
    }

    function saveChanges() {
        // Собираем данные формы
        var formData = new FormData(document.getElementById("projectForm"));
    
        // Отправляем запрос на сервер с помощью Fetch API
        fetch('admin/update_project.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // Обработка ответа от сервера
            localStorage.setItem('activeTab', 'tasksTabButton'); // Устанавливаем вкладку, которую нужно открыть после перезагрузки
            window.location.reload(); // Перезагрузка страницы
        })
        .catch(error => console.error('Ошибка:', error));
    
        return false; // Предотвратить стандартную отправку формы
    }
    

        
    function deleteProject(projectId) {
        if (confirm("Вы уверены, что хотите удалить проект с ID " + projectId + "?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "admin/delete_project.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    location.reload(); // Перезагружаем страницу после успешного удаления
                }
            };
            xhr.send("deleteProjectId=" + projectId); // Отправляем ID проекта для удаления на сервер
        }
    }
    



function cancelEditTask() {
    // Скрываем форму для редактирования
    document.getElementById("editTaskForm").style.display = "none";
    return false; // Отменяем отправку формы
}


 // Переменные для хранения оригинальных значений перед редактированием
var originalTaskDescription, originalProjectID, originalLocation, originalDate, originalStatus;

// Функция для заполнения формы редактирования задачи
function fillEditTaskForm(taskId, description, projectId, location, date, status) {
    // Заполняем поля формы значениями из переданной задачи
    document.getElementById("editTaskId").value = taskId;
    document.getElementById("editTaskDescription").value = description;
    document.getElementById("editTaskProjectID").value = projectId;
    document.getElementById("editTaskLocation").value = location;
    document.getElementById("editTaskDate").value = date;
    document.getElementById("editTaskStatus").value = status;

    // Сохраняем оригинальные значения
    originalTaskDescription = description;
    originalProjectID = projectId;
    originalLocation = location;
    originalDate = date;
    originalStatus = status;
}









function fillTaskFields(taskId, description, projectId, location, date, status) {
    document.getElementById("editTaskId").value = taskId;
    document.getElementById("editTaskDescription").value = description;
    document.getElementById("editTaskLocation").value = location;
    document.getElementById("editTaskDate").value = date;

    // Выбираем соответствующий проект в выпадающем списке
    var editTaskProject = document.getElementById("editTaskProjectID");
    for (var i = 0; i < editTaskProject.options.length; i++) {
        if (editTaskProject.options[i].value == projectId) {
            editTaskProject.selectedIndex = i;
            break;
        }
    }

    // Выбираем соответствующий статус в выпадающем списке
    var editTaskStatus = document.getElementById("editTaskStatus");
    for (var j = 0; j < editTaskStatus.options.length; j++) {
        if (editTaskStatus.options[j].value == status) {
            editTaskStatus.selectedIndex = j;
            break;
        }
    }
}


function deleteUser(userId) {
    if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
        const formData = new FormData();
        formData.append('deleteUserId', userId);

        fetch('admin/delete_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // alert(data); // Сообщение об успешном удалении или ошибке
            window.location.reload(); // Перезагрузка страницы для обновления списка пользователей
        })
        .catch(error => console.error('Ошибка:', error));
    }
}


function editUser(button, userId) {
    var editUserForm = document.getElementById("editUserForm");
    if (!editUserForm) {
        console.error("Форма редактирования не найдена.");
        return;
    }

    console.log("UserID Passed to Function:", userId);

    var userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!userRow) {
        console.error("User row not found.");
        return;
    }

    // Извлечение элементов формы и установка значений
    var editUserId = document.getElementById("editUserId");
    var editUserName = document.getElementById("editUserName");
    var editUserSurname = document.getElementById("editUserSurname");
    var editUserEmail = document.getElementById("editUserEmail");
    var editUserPhone = document.getElementById("editUserPhone");
    var editUserDOB = document.getElementById("editUserDOB");
    var editUserAddress = document.getElementById("editUserAddress");
    var editUserGender = document.getElementById("editUserGender");
    var editUserRole = document.getElementById("editUserRole");
    

    // Установка значений из атрибутов данных строки пользователя
    editUserId.value = userId || ''; // Если userId не определен, установить пустую строку
    editUserName.value = userRow.getAttribute('data-name') || '';
    editUserSurname.value = userRow.getAttribute('data-surname') || '';
    editUserEmail.value = userRow.getAttribute('data-email') || '';
    editUserPhone.value = userRow.getAttribute('data-phone') || '';
    editUserDOB.value = userRow.getAttribute('data-dob') || '';
    editUserAddress.value = userRow.getAttribute('data-address') || '';
    editUserGender.value = userRow.getAttribute('data-gender') || '';
    editUserRole.value = userRow.getAttribute('data-role') || '';
    

    // Позиционирование формы
    var rect = button.getBoundingClientRect();
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    var leftShift = 200;
    editUserForm.style.position = "absolute";
    editUserForm.style.top = (rect.bottom + scrollTop) + 'px';
    editUserForm.style.left = Math.max(rect.left - leftShift, 0) + 'px';

    editUserForm.style.display = 'block';
}





function CancelUserChanges(){
    var editUserForm = document.getElementById("editUserForm");

// Проверяем, существует ли форма на странице
if (!editUserForm) {
    console.error("Форма редактирования не найдена.");
    return;
}

// Скрываем форму редактирования
editUserForm.style.display = 'none';
}





function saveUserChanges() {//hihi
    const userId = document.getElementById('editUserId').value;
    const userName = document.getElementById('editUserName').value;
    const userSurname = document.getElementById('editUserSurname').value;
    const userEmail = document.getElementById('editUserEmail').value;
    const userPhone = document.getElementById('editUserPhone').value;
    const userDOB = document.getElementById('editUserDOB').value;
    const userAddress = document.getElementById('editUserAddress').value;
    const userGender = document.getElementById('editUserGender').value;
    const userRole = document.getElementById('editUserRole').value;
    

    let formData = new FormData();
    formData.append('userId', userId);
    formData.append('name', userName);
    formData.append('surname', userSurname);
    formData.append('email', userEmail);
    formData.append('phone', userPhone);
    formData.append('dob', userDOB);
    formData.append('address', userAddress);
    formData.append('gender', userGender);
    formData.append('role', userRole);
    

    fetch('admin/update_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            console.error('Ошибка при обновлении данных: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
    });
}


function saveTaskChanges() {
    // Получаем данные из формы
    var taskId = document.getElementById("editTaskId").value;
    var description = document.getElementById("editTaskDescription").value;
    var location = document.getElementById("editTaskLocation").value;
    var date = document.getElementById("editTaskDate").value;
    var status = document.getElementById("editTaskStatus").value;

    // Отправляем данные на сервер для обновления
    var formData = new FormData();
    formData.append("taskId", taskId);
    formData.append("description", description);
    formData.append("location", location);
    formData.append("date", date);
    formData.append("status", status);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "admin/update_task.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            // Обработка успешного ответа
            console.log(xhr.responseText);
            // Закрыть форму редактирования
            document.getElementById("editTaskForm").style.display = "none";
            // Сохраняем ID активной вкладки в localStorage перед перезагрузкой
            localStorage.setItem("activeTabId", "tasksTabButton");
            // Перезагружаем страницу
            window.location.reload();
            document.getElementById('tasksTabButton').click();
        }
    };
    xhr.send(formData);

    return false; // Отменяем отправку формы
}

function deleteTask(taskId) {
    if (confirm('Вы точно хотите удалить задачу с ID ' + taskId + '?')) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "admin/delete_task.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Сохраняем ID активной вкладки в localStorage перед перезагрузкой
                    localStorage.setItem("activeTabId", "tasksTabButton");
                    // Перезагружаем страницу
                    window.location.reload();
                    document.getElementById('tasksTabButton').click();
                } else {
                    // alert("Произошла ошибка при удалении задачи: " + xhr.statusText);
                }
             }
        };
        xhr.send("deleteTaskId=" + taskId);
    }
}



function updateTasks(projectId) {
    if (!projectId) {
        document.getElementById('taskSelect').innerHTML = '<option value="">Сначала выберите проект</option>';
        return;
    }

    fetch(`admin/get_task.php?projectId=${projectId}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(tasks => {
        const taskSelect = document.getElementById('taskSelect');
        taskSelect.innerHTML = '';
        tasks.forEach(task => {
            const option = new Option(task.Description, task.TaskID);
            taskSelect.add(option);
        });
    })
    .catch(error => {
        console.error('Error fetching tasks:', error);
        alert('Error fetching tasks: ' + error.message);
    });
}


document.addEventListener('DOMContentLoaded', function() {
    // Весь ваш код здесь
    document.getElementById('projectSelect').addEventListener('change', function() {
        const projectId = this.value;
        if (!projectId) {
            document.getElementById('taskSelect').innerHTML = '<option value="">Сначала выберите проект</option>';
            return;
        }

        fetch(`admin/get_task.php?projectId=${projectId}`)
        .then(response => response.json())
        .then(tasks => {
            const taskSelect = document.getElementById('taskSelect');
            taskSelect.innerHTML = '';
            tasks.forEach(task => {
                const option = new Option(task.Description, task.TaskID);
                taskSelect.add(option);
            });
        })
        .catch(error => console.error('Error fetching tasks:', error));
    });
});




function editTask(button, taskId, description, projectName, location, taskDate, status) {
    // Получаем элементы формы для редактирования
    var editTaskForm = document.getElementById("editTaskForm");
    var editDescription = document.getElementById("editTaskDescription");
    var editProjectName = document.getElementById("editProjectName");
    var editLocation = document.getElementById("editTaskLocation");
    var editDate = document.getElementById("editTaskDate");
    var editStatus = document.getElementById("editTaskStatus");
    var editTaskId = document.getElementById("editTaskId");

    if (editDescription && editProjectName && editLocation && editDate && editStatus && editTaskId) {
        // Заполняем форму данными о задаче
        editDescription.value = description;
        editProjectName.value = projectName;
        editLocation.value = location;
        editDate.value = taskDate;
        editStatus.value = status;
        editTaskId.value = taskId;

        // Позиционируем форму редактирования под кнопкой и смещаем влево
        var rect = button.getBoundingClientRect();
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
      

        
    var leftShift = 300;
    editTaskForm.style.position = "absolute";
    editTaskForm.style.top = (rect.bottom + scrollTop) + 'px';
    editTaskForm.style.left = Math.max(rect.left - leftShift, 0) + 'px';

    editUserForm.style.display = 'block';
        // Показываем форму
        editTaskForm.style.display = 'block';
    } else {
        console.error("Не удалось найти элементы формы редактирования задачи.");
    }
}



