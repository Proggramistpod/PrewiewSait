<!-- Инициализация сессии -->
<?php
session_start();
?>
<!-- Шаблон для создания html разметки -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Заголовок страницы -->
<title>Содержание заказа</title>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Содержание заказа</title>
<link rel="stylesheet" href="../css/menu.css">
<link rel="stylesheet" href="../css/content_order.css">
</head>
<body>
<!-- Подключение меню в файл -->
<header><?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Содержание заказа</title>
    <link rel="stylesheet" href="../css/menu.css">
    <style>
        .table_shop, .table_shop1 {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table_shop td, .table_shop1 td, .table_shop1 th {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        .table_shop1 th {
            background-color: #f2f2f2;
        }
        tfoot td {
            font-weight: bold;
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
<?php include("../menu.php"); ?>
<header>
    <h1>Содержание заказа</h1>
</header>

<?php
// Подключаемся к базе данных
include '../application/db.php';

// Получаем номер заказа из GET
$num_id = $_GET["OrderNum"] ?? '';
if (empty($num_id)) {
    die("Номер заказа не указан");
}

// 1. Основная информация о заказе
$query1 = "SELECT num_order, data_order, paid FROM `order` WHERE num_order = ? GROUP BY num_order, data_order, paid";
$stmt = $conn->prepare($query1);
if (!$stmt) {
    die("Ошибка подготовки запроса: " . $conn->error);
}
$stmt->bind_param("s", $num_id);
$stmt->execute();
$result1 = $stmt->get_result();

if ($result1->num_rows == 0) {
    echo "<p>Заказ не найден.</p>";
    $stmt->close();
    mysqli_close($conn);
    exit;
}

$orderInfo = $result1->fetch_assoc();
$stmt->close();

// Таблица с общей информацией
echo '<table class="table_shop">';
echo '<tr>';
echo '<td><img style="width:150px;" src="http://localhost/socset/shop/img/cart.png" alt=""></td>';
echo '<td>Заказ №' . htmlspecialchars($orderInfo['num_order']) . ' от ' . htmlspecialchars($orderInfo['data_order']) . '</td>';
if ($orderInfo['paid'] == 0) {
    echo '<td>Не оплачен</td>';
    echo '<td><img style="width:100px;" src="http://localhost/socset/shop/img/krest.png" alt=""></td>';
} else {
    echo '<td>Оплачен</td>';
    echo '<td><img style="width:115px;" src="http://localhost/socset/shop/img/galochka.png" alt=""></td>';
}
echo '</tr>';
echo '</table>';

// 2. Детали заказа (товары) с картинками
$query2 = "SELECT o.product, o.price, o.count_product, o.summa, m.picture 
           FROM `order` o 
           INNER JOIN merchendise m ON o.product = m.name 
           WHERE o.num_order = ?";
$stmt = $conn->prepare($query2);
if (!$stmt) {
    die("Ошибка подготовки запроса деталей: " . $conn->error);
}
$stmt->bind_param("s", $num_id);
$stmt->execute();
$result2 = $stmt->get_result();

$sum = 0;
$output = '<div class="table_order"><table class="table_shop1">';
$output .= '<tr><th colspan="2">Товар</th><th>Цена</th><th>Количество</th><th>Сумма</th></tr>';

while ($row = $result2->fetch_assoc()) {
    $output .= '<tr>';
    $output .= '<td><img style="width:70px;" src="data:image/jpg;base64,' . $row['picture'] . '" /></td>';
    $output .= '<td>' . htmlspecialchars($row['product']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['price']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['count_product']) . '</td>';
    $output .= '<td>' . htmlspecialchars($row['summa']) . '</td>';
    $output .= '</tr>';
    $sum += $row['summa'];
}

$output .= '<tfoot><tr><td colspan="4">Итоговая сумма:</td><td>' . $sum . '</td></tr></tfoot>';
$output .= '</table></div>';
echo $output;

$stmt->close();
mysqli_close($conn);
?>

<script src="js/jquery-3.7.1.min.js"></script>
</body>
</html>
<h1>Содержание заказа</h1>
</header>
<!-- Подключение меню в файл -->
<?php
include("../menu.php");
// Подключаемся к базе даных
include '../application/db.php';
// Помещаем в переменную значение номера заказа из массива _GET
$num_id = $_GET["OrderNum"];
// Запрос на получение информации о содержимом заказа
$test_query = "SELECT num_order, date_order,paid FROM `order`WHERE num_order =
".$num_id." GROUP by num_order, date_order,paid";
// Выполнение запроса
$result = mysqli_query($conn, $test_query);
// Помещение результат выборки в массив
for($data = []; $row=mysqli_fetch_assoc($result); $data[] = $row);
// Переменная для создания строки кода формирования таблицы
$result = '<table class="table_shop">';
// Цикл на добавление данных в ячейки таблицы
foreach($data as $elem){
$result .='<tr>';
// Помещаем в ячейку картинку
$result .='<td><img style="width: 150px;"
src="http://localhost/Social/shop/img/cart.png" alt=""></td>';
// Формируем в ячейке надпись о просматриваемом заказе
$result .='<td>Заказ №'.$elem['num_order'].' от
'.$elem['date_order'].'</td>';
// Условие на вывод данных в зависимости от того опласен заказ или нет
if ($elem['paid'] == 0){
$result .='<td>Не оплачен</td>';
$result .='<td><img style="width: 100px;"
src="http://localhost/Social/shop/img/krest.png" alt=""></td>';
}
else{
$result .='<td>Оплачен</td>';
$result .='<td><img style="width: 115px;"
src="http://localhost/Social/shop/img/galochka.png" alt=""></td>';
}
$result .='</tr>';
}
$result .='</table>';
echo $result;
// Создание переменной, которая будет содержать итоговую сумму по заказу
$sum = 0;
// Формируем запрос к базе данных
$test_query = "SELECT order.product,
order.price,order.count_product,order.summa, merchendise.picture FROM `order` INNER
JOIN merchendise on order.product=merchendise.name WHERE num_order = ".$num_id."";
// Выполняем запрос
$result = mysqli_query($conn, $test_query);
// Помещаем выборку в массив
for($data = []; $row=mysqli_fetch_assoc($result); $data[] = $row);
// Формирование строки кода таблицы
$result = '<div class= "table_order"><table class="table_shop1"><tr><th
colspan="2">Товар</th><th>Цена</th><th>Количество</th><th>Сумма</th></tr>';
// Цикл на добавление данных в ячейки таблицы
foreach($data as $elem){
$result .='<tr>';
// Помещаем в ячейку картинку
$result .='<td><img style="width: 70px;"
src="data:image/jpg;base64,'.$elem['picture'].'"; /></td>';
// Помещаем в ячейку название товара
$result .='<td>'.$elem['product'].'</td>';
// Помещаем в ячейку цену
$result .='<td>'.$elem['price'].'</td>';
// Помещаем в ячейку количество
$result .='<td>'.$elem['count_product'].'</td>';
// Помещаем в ячейку сумму
$result .='<td>'.$elem['summa'].'</td>';
$result .='</tr>';
// Изменяем значение переменной
$sum += $elem['summa'];
}
// Формируем подвал таблицы
$result .='<tfoot><tr><td>Итоговая
сумма:</td><td></td><td></td><td></td><td>'.$sum.'</td></tr></tfoot></table></div>'
;
// Вывод информации на страницу
echo $result;
mysqli_close($conn);
?>
<!-- Подключение библиотеки JQuery -->
<script src="js/jquery-3.7.1.min.js"></script>
</body>
</html>