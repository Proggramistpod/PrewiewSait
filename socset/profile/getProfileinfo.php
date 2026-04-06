<?php
session_start();
include_once "../application/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['id'])) {
        echo "error|Пользователь не авторизован|";
        exit;
    }

    $userId = $_SESSION['id'];

    // Подготавливаем запрос
    $query = "SELECT email, created, info FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "error|Ошибка подготовки запроса|";
        exit;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($email, $created, $info);

    if ($stmt->fetch()) {
        // Заменяем NULL на пустые строки
        $email = $email ?? '';
        $created = $created ?? '';
        $info = $info ?? '';

        // Формируем строку с разделителем '|'
        echo $email . '|' . $created . '|' . $info;
    } else {
        echo "error|Пользователь не найден|";
    }

    $stmt->close();
} else {
    echo "error|Недопустимый метод запроса|";
}
?>