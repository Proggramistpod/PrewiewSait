<?php
session_start();
require_once '../application/db.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$currentUserId = $_SESSION['id'];

// Выбираем всех пользователей, кроме текущего
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