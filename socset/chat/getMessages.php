<?php
session_start();
require_once '../application/db.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$currentUserId = $_SESSION['id'];
$otherUserId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($otherUserId <= 0) {
    echo json_encode([]);
    exit;
}

// Получаем сообщения между текущим пользователем и выбранным
$sql = "SELECT sender_id, receiver_id, message, created_at 
        FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $currentUserId, $otherUserId, $otherUserId, $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'sender_id' => $row['sender_id'],
        'receiver_id' => $row['receiver_id'],
        'message' => htmlspecialchars($row['message']),
        'created_at' => $row['created_at']
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($messages);
?>