<?php
session_start();
require_once '../application/db.php';

// Разрешаем только GET-запросы (как ожидается в chat-admin.js)
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Проверка авторизации
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Проверка прав администратора (опционально, можно добавить, если требуется)
// if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
//     http_response_code(403);
//     echo json_encode(['error' => 'Forbidden']);
//     exit;
// }

$currentUserId = $_SESSION['id'];
$otherUserId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($otherUserId <= 0) {
    // Если не указан получатель, возвращаем пустой массив
    echo json_encode([]);
    exit;
}

// Подготавливаем запрос для получения сообщений между текущим пользователем и выбранным
$stmt = $conn->prepare("
    SELECT 
        m.sender_id,
        m.receiver_id,
        m.message_text,
        m.timestamp
    FROM messages m
    WHERE (m.sender_id = ? AND m.receiver_id = ?)
       OR (m.sender_id = ? AND m.receiver_id = ?)
    ORDER BY m.timestamp ASC
");
$stmt->bind_param("iiii", $currentUserId, $otherUserId, $otherUserId, $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'sender_id' => $row['sender_id'],
        'receiver_id' => $row['receiver_id'],
        'message' => htmlspecialchars($row['message_text']), // экранируем для безопасного вывода
        'timestamp' => $row['timestamp']
    ];
}
$stmt->close();

// Отправляем JSON-ответ
header('Content-Type: application/json');
echo json_encode($messages);
?>