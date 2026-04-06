<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Крестики-нолики</title>
    <link rel="stylesheet" href="x-o.css">
    <link rel="stylesheet" href="../../css/menu.css">
    <link rel="stylesheet" href="../../css/loader.css">
    <script src="../../js/loader.js"></script>
</head>
<body>
    <?php include("../../menu.php"); ?>
    <?php include("../../application/loader.php"); ?>
    <div class="container">
        <header><h1>Крестики-нолики</h1></header>
        <div id="gameboard">
            <div class="cell" id="cell0" onclick="cellClicked(0)"></div>
            <div class="cell" id="cell1" onclick="cellClicked(1)"></div>
            <div class="cell" id="cell2" onclick="cellClicked(2)"></div>
            <div class="cell" id="cell3" onclick="cellClicked(3)"></div>
            <div class="cell" id="cell4" onclick="cellClicked(4)"></div>
            <div class="cell" id="cell5" onclick="cellClicked(5)"></div>
            <div class="cell" id="cell6" onclick="cellClicked(6)"></div>
            <div class="cell" id="cell7" onclick="cellClicked(7)"></div>
            <div class="cell" id="cell8" onclick="cellClicked(8)"></div>
        </div>
        <p id="message"></p>
    </div>
    <script src="x-o.js"></script>
</body>
</html>