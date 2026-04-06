<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="shop.css">
</head>
<body>
    <?php include("../menu.php"); ?>

    <header>
        <h1>Магазин</h1>
    </header>

    <div class="assortment">
        <div class="zagolovok-shop">
            <a class="korzina" href="cart.php">Моя корзина</a>
        </div>
        <div class="products">
            <table class='table_shop'>
                <tr>
                    <th colspan="2">Товар</th>
                    <th>Цена</th>
                    <th>Добавить</th>
                </tr>
                <?php
                include '../application/db.php';
                $product_query = "SELECT id, name, price, picture FROM merchendise";
                $result = mysqli_query($conn, $product_query);
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td><img style='width:150px;' src='data:image/jpg;base64," . $row['picture'] . "' /></td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . $row['price'] . "</td>";
                        echo "<td><button class='add-to-cart' type='button' onclick='func(" . $row['id'] . ")'>Купить</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Товаров пока нет.</td></tr>";
                }
                mysqli_close($conn);
                ?>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/scriptjs.js"></script>
</body>
</html>