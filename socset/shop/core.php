<?php
session_start();
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'init':
        init();
        break;
    case 'inputData':
        PutData();
        break;
}

function init() {
    include '../application/db.php';
    $id_product = $_POST['id_product'] ?? '';
    // Разбиваем строку с id, разделёнными пробелами, удаляем пустые значения
    $ids = array_filter(explode(' ', trim($id_product)), 'is_numeric');
    if (empty($ids)) {
        echo json_encode([]);
        return;
    }
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT id, name, price FROM merchendise WHERE id IN ($placeholders)");
    if (!$stmt) {
        echo json_encode([]);
        return;
    }
    $types = str_repeat('i', count($ids));
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    $out1 = [];
    while ($row = $result->fetch_assoc()) {
        $out1[$row["id"]] = $row;
    }
    echo json_encode($out1);
    $stmt->close();
    mysqli_close($conn);
}

function PutData() {
    include '../application/db.php';
    $Data_Order = $_POST['Data_Order'] ?? [];
    if (empty($Data_Order)) {
        return;
    }

    // Извлекаем массивы данных (значения разделены пробелами)
    $arrProd = array_filter(explode(' ', trim($Data_Order["product"] ?? '')));
    $arrPrice = array_filter(explode(' ', trim($Data_Order["price"] ?? '')));
    $arrCount = array_filter(explode(' ', trim($Data_Order["count_product"] ?? '')));
    $arrSum = array_filter(explode(' ', trim($Data_Order["summa"] ?? '')));

    $num_order = (int)($Data_Order["num_order"] ?? 0);
    $date_order = $Data_Order["date_order"] ?? '';

    // Получаем email пользователя из сессии
    $userId = $_SESSION["id"] ?? 0;
    $email = '';
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    if (!$email || !$num_order || !$date_order) {
        return;
    }

    // Подготовленный запрос для вставки
    $sql = "INSERT INTO `order` (`num_order`, `data_order`, `users`, `product`, `price`, `count_product`, `summa`, `paid`) VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return;
    }

    for ($i = 0; $i < count($arrProd); $i++) {
        $product = $arrProd[$i];
        $price = (float)$arrPrice[$i];
        $count = (int)$arrCount[$i];
        $summa = (float)$arrSum[$i];
        $stmt->bind_param("isssdii", $num_order, $date_order, $email, $product, $price, $count, $summa);
        $stmt->execute();
    }
    $stmt->close();
    mysqli_close($conn);
}
?>