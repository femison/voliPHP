<?php
require_once('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'] ?? '';
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $address = $_POST['address'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $role = $_POST['role'] ?? '';

    $sql = "UPDATE users SET Name=?, Surname=?, Email=?, Phone=?, DateOfBirth=?, Address=?, Gender=?, Role=? WHERE UserID=?";

   
    

    $stmt = $connect->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ssssssssi', $name, $surname, $email, $phone, $dob, $address, $gender, $role, $userId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Данные пользователя обновлены.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении данных: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка при подготовке запроса: ' . $connect->error]);
    }
    $connect->close();
}
?>
