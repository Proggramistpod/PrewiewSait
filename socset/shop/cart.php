<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>
    <?php include("../menu.php"); ?>
    <header>
        <h1>Корзина</h1>
    </header>
    <div id="main-cart" class="main-cart"></div>
    <div class="zagalovok">
        <button class="post-bd" onclick="POSTData()">Оформить заказ</button>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/scriptcart.js"></script>
</body>
</html>