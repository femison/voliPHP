<?php
session_start();
require 'admin/db_connection.php';

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = mysqli_real_escape_string($connect, $_SESSION["username"]);
$user_id_query = "SELECT UserID FROM usercredentials WHERE Login='$username'";
$user_id_result = mysqli_query($connect, $user_id_query);

if (mysqli_num_rows($user_id_result) == 1) {
    $user_id_row = mysqli_fetch_assoc($user_id_result);
    $userID = $user_id_row['UserID'];
} else {
    die("Пользователь не найден.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $projectID = mysqli_real_escape_string($connect, $_POST['project_id']);
    $taskID = mysqli_real_escape_string($connect, $_POST['task_id']);

    // Проверка на существование такой же заявки
    $check_query = "SELECT * FROM users_pending_approval WHERE UserID='$userID' AND ProjectID='$projectID' AND TaskID='$taskID'";
    $check_result = mysqli_query($connect, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>
        alert('Вы уже подали заявку на эту задачу.');
        window.history.back();
      </script>";
    } else {
        $insert_query = "INSERT INTO users_pending_approval (UserID, ProjectID, TaskID) VALUES ('$userID', '$projectID', '$taskID')";
        if (mysqli_query($connect, $insert_query)) {
            
            echo "<script>
                    alert('Заявка успешно отправлена.');
                    window.location.href = 'voliform.php';
                  </script>";
        } else {
            $error = htmlspecialchars(mysqli_error($connect), ENT_QUOTES, 'UTF-8');
            echo "<script>
                    alert('Ошибка при отправке заявки: $error');
                    window.history.back();
                  </script>";
        }
    }
} else {
    echo "Неверный метод запроса.";
}

mysqli_close($connect);
?>
