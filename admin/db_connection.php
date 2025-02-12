<?php


$host = "localhost";
$username = "root";
$password = "";
$database = "voli";
$port = 3306;

$connect = mysqli_connect($host, $username, $password, $database, $port);

if (mysqli_connect_errno()) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}
?>
