<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История заказов</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="history_order.css">
</head>
<body>
<?php include("../menu.php"); ?>

<header>
    <h1>История заказов</h1>
</header>

<table class='table_shop'>
<?php
include '../application/db.php';

if (!isset($_SESSION["id"])) {
    echo "<tr><td colspan='4'>Пожалуйста, авторизуйтесь для просмотра истории заказов.</td></tr>";
} else {
    $userId = $_SESSION["id"];

    $userQuery = "SELECT email FROM users WHERE id = ?";
    $stmt = $conn->prepare($userQuery);
    if (!$stmt) {
        die("Ошибка подготовки запроса для users: " . $conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    if (!$email) {
        echo "<tr><td colspan='4'>Пользователь не найден.</td></tr>";
    } else {
        // ВНИМАНИЕ: поле даты в БД — data_order (см. скриншот)
        $ordersQuery = "SELECT num_order, data_order, paid FROM `order` WHERE users = ? GROUP BY num_order, data_order, paid";
        $stmt = $conn->prepare($ordersQuery);
        if (!$stmt) {
            echo "<tr><td colspan='4'>Ошибка подготовки запроса для orders: " . $conn->error . "<br>Запрос: " . htmlspecialchars($ordersQuery) . "</td></tr>";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img style='width:150px;' src='http://localhost/socset/shop/img/cart.png' alt=''></td>";
                    echo "<td><a href='http://localhost/socset/shop/content_order.php?OrderNum=" . $row['num_order'] . "'>Заказ №" . $row['num_order'] . " от " . $row['data_order'] . "</a></td>";
                    if ($row['paid'] == 0) {
                        echo "<td>Не оплачен</td>";
                        echo "<td><img style='width:100px;' src='http://localhost/socset/shop/img/krest.png' alt=''></td>";
                    } else {
                        echo "<td>Оплачен</td>";
                        echo "<td><img style='width:115px;' src='http://localhost/socset/shop/img/galochka.png' alt=''></td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>У вас пока нет заказов.</td></tr>";
            }
            $stmt->close();
        }
    }
}
mysqli_close($conn);
?>
</table>

<script src="../js/jquery-3.7.1.min.js"></script>
</body>
</html>