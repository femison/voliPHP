// Функция для переключения вкладок
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
    
    // Сохраняем активную вкладку в localStorage
    localStorage.setItem("activeTabId", evt.currentTarget.id);
}

// Автоматическое открытие вкладки "Главная страница" при загрузке
window.onload = function() {
    var activeTabId = localStorage.getItem("activeTabId");
    if (activeTabId && document.getElementById(activeTabId)) {
        document.getElementById(activeTabId).click();
    } else {
        // Если нет сохраненной вкладки, открываем "Главная страница" по умолчанию
        document.getElementById("mainpageTabButton").click();
        localStorage.setItem("activeTabId", "mainpageTabButton"); // Сохраняем состояние
    }
};