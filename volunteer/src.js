window.onload = function() {
    var activeTabId = localStorage.getItem("activeTabId");
    if (activeTabId) {
        var tabButton = document.getElementById(activeTabId);
        if (tabButton) {
            tabButton.click();
        }
        
    } else {
        document.getElementById("mainpageTabButton").click(); // Если нет сохраненной вкладки, открываем по умолчанию
        tabButton.click();  
    }
};




