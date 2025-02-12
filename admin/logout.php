<?php
// Проверка, запущена ли уже сессия
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Настройки подключения к базе данных
$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "voli";
$port = 3306;




// Создание подключения
$connect = new mysqli($servername, $db_username, $db_password, $db_name,$port);

// Проверка подключения
if ($connect->connect_error) {
    die("Ошибка подключения: " . $connect->connect_error);
}

// Установка кодировки
$connect->set_charset("utf8mb4");
?>
