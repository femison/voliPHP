<?php
function formatDate($date) {
    $timestamp = strtotime($date);
    
    // Получаем день, месяц и год из временной метки Unix
    $day = date('d', $timestamp);
    $month = date('m', $timestamp);
    $year = date('Y', $timestamp);

    // Массивы с названиями месяцев и их сокращениями
    $months = [
        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня',
        7 => 'июля', 8 => 'августа', 9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
    ];
    $monthsShort = [
        1 => 'янв', 2 => 'фев', 3 => 'мар', 4 => 'апр', 5 => 'май', 6 => 'июн',
        7 => 'июл', 8 => 'авг', 9 => 'сен', 10 => 'окт', 11 => 'ноя', 12 => 'дек'
    ];

    // Формируем строку с датой в нужном формате
    $formattedDate = $day . ' ' . $months[(int)$month] . ' ' . $year;

    return $formattedDate;
}
// Функция для выполнения запроса и получения результатов в виде массива
function fetchAll($query) {
    global $connect;
    $result = mysqli_query($connect, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}