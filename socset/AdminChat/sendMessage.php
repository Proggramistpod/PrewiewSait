<?php
session_start();
require_once '../application/db.php';

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Проверяем авторизацию
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit;
}

$senderId = $_SESSION['id'];
$receiverId = 0; // По умолчанию - всем (общий чат)
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

// Получаем данные из POST-запроса
$receiverId = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
$messageText = isset($_POST['message']) ? trim($_POST['message']) : '';

// Валидация сообщения
if (empty($messageText)) {
    echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty']);
    exit;
}

// Если это администратор и receiver_id = 0, значит отправка всем (общее сообщение)
// Если receiver_id > 0, значит личное сообщение конкретному пользователю
// Для обычного пользователя receiver_id всегда должен быть > 0

if (!$isAdmin && $receiverId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid receiver']);
    exit;
}

// Подготавливаем запрос для вставки сообщения
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("iis", $senderId, $receiverId, $messageText);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Message sent']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>