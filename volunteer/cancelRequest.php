<?php
require_once('../func/db_connection.php');

if (isset($_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);
    
    // Запрос на удаление заявки по RequestID
    $delete_sql = "DELETE FROM users_pending_approval WHERE RequestID = $request_id";

    if (mysqli_query($connect, $delete_sql)) {
        echo "Заявка успешно удалена.";
        header("Location: ../voliform.php");
    } else {
        echo "Ошибка при удалении заявки: " . mysqli_error($connect);
    }
} else {
    echo "Не указан RequestID.";
}

mysqli_close($connect);
?>
