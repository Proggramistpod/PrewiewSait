<?php
require_once '../../application/db.php';

session_start();

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["score"]) || !isset($_SESSION["login"])) {
    echo "Ошибка: нет данных или пользователь не авторизован";
    exit;
}

$score = (int)$_POST["score"];
$username = $_SESSION["login"];

if ($score <= 0) {
    echo "Счёт слишком маленький";
    exit;
}

// Получаем текущий рекорд
$sql = "SELECT score FROM high_scores_breakout WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($high_score);
$stmt->fetch();

if ($stmt->num_rows == 0) {
    // Вставляем новую запись
    $sql = "INSERT INTO high_scores_breakout (username, score, date) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $score);
    
    if ($stmt->execute()) {
        echo "Новый рекорд сохранён";
    } else {
        echo "Ошибка INSERT: " . $stmt->error;
    }
} else {
    if ($score > $high_score) {
        $sql = "UPDATE high_scores_breakout SET score = ?, date = NOW() WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $score, $username);
        if ($stmt->execute()) {
            echo "Рекорд обновлён";
        } else {
            echo "Ошибка UPDATE: " . $stmt->error;
        }
    } else {
        echo "Счёт не рекорд";
    }
}

$stmt->close();
$conn->close();
?>