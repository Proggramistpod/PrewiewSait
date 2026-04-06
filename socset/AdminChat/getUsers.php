<?php
session_start();
require_once '../application/db.php';

// Проверка авторизации
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Проверка прав администратора (если необходимо, можно убрать, если админ уже авторизован отдельно)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$currentUserId = $_SESSION['id'];

// Выбираем всех пользователей, кроме самого администратора (чтобы не отображать себя в списке)
$sql = "SELECT id, us_name AS username FROM users WHERE id != ? ORDER BY us_name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        'id' => $row['id'],
        'username' => htmlspecialchars($row['username'])
    ];
}
$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($users);
?>