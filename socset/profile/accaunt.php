<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../auth.php");
    exit;
}

include_once "../application/db.php";

// Проверяем, что подключение живое
if ($conn->connect_error) {
    die("Ошибка подключения к БД: " . $conn->connect_error);
}

$user_id = $_SESSION['id'];
$query = "
    SELECT 
        us_name, 
        email, 
        age, 
        created AS created,
        info,                
        'profile-picture'        
    FROM users 
    WHERE id = ?
";

$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Ошибка prepare: " . $conn->error);
}

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    die("Ошибка execute: " . $stmt->error);
}

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

// Значения по умолчанию, если данных нет
$fullname = $user['us_name']   ?? ($_SESSION['login'] ?? '—');
$age      = $user['age']       ?? ($_SESSION['age']  ?? '—');
$email    = $user['email']     ?? ($_SESSION['email'] ?? '—');
$created  = $user['created']   ?? '—';
$about    = $user['info']      ?? '—';
$avatar   = !empty($user['profile-picture']) 
    ? htmlspecialchars($user['profile-picture']) 
    : 'avatars/placeholder.jpg';

$adminId = 6;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="../css/profile-styles.css">
    <link rel="stylesheet" href="../css/menu.css">
</head>
<body>

    <header>
        <h1>Профиль пользователя</h1>
        <form action="/socset/profile/logout.php" method="post">
            <button type="submit" name="logout_button">Выйти</button>
        </form>
    </header>

    <?php include("../menu.php"); ?>

    <section id="profile-info">
        <h2>Информация о пользователе</h2>

        <div id="profile-picture-container">
            <img id="profile-picture" src="<?= $avatar ?>" alt="Аватар пользователя">
        </div>

        <p>ФИО:     <span class="info"><?= htmlspecialchars($fullname) ?></span></p>
        <p>Возраст: <span class="info"><?= htmlspecialchars($age) ?></span></p>
        <p>Дата создания аккаунта: <span class="info"><?= htmlspecialchars($created) ?></span></p>
        <p>Email:   <span class="info"><?= htmlspecialchars($email) ?></span></p>
        <p>О себе:  <span class="info"><?= nl2br(htmlspecialchars($about)) ?></span></p>

        <h3>Панель управления</h3>

        <button onclick="location.href='editor_profile.php'" id="edit-profile">
            Редактировать профиль
        </button>

        <button onclick="location.href='/socset/chat/chat.php?user_id=<?= $adminId ?>'" id="admin-chat">
            Написать администратору
        </button>

        <button id="update-picture">Обновить картинку</button>
        <button id="delete-picture">Удалить картинку</button>
        <button id="story_order"
            onclick="document.location='http://localhost/socset/shop/history_order.php'"
            >История заказов</button>
            <input type="file" id="file-input" accept="image/*" style="display: none">
            </section>
            <section id="user-posts">
        <!-- Скрытый input для загрузки -->
        <input type="file" id="file-input" accept="image/*" style="display:none">
    </section>

    <section id="user-posts">
        <h2>Мои посты</h2>

        <?php
    // подключаем БД (если ещё не подключена выше)
    include_once "../application/db.php";

    $user_id = (int)$_SESSION['id'];

    $sql = "SELECT id, title, LEFT(content, 150) AS short_content, created_at 
            FROM posts 
            WHERE user_id = $user_id 
            ORDER BY created_at DESC 
            LIMIT 15";

    $result = $conn->query($sql);

    if (!$result) {
        echo '<p style="color:red;">Ошибка запроса: ' . $conn->error . '</p>';
    } elseif ($result->num_rows > 0) {
        echo '<ul id="posts-list" style="list-style:none; padding:0;">';
        while ($row = $result->fetch_assoc()) {
            $short = htmlspecialchars($row['short_content']);
            if (strlen($row['short_content']) < strlen($row['content'] ?? '')) {
                $short .= '...';
            }
            echo '<li style="margin:12px 0; padding:12px; background:#2a2f3a; border-radius:6px;">';
            echo '<a href="../posts/posts.php?id=' . (int)$row['id'] . '" style="color:#ffc000; font-size:1.2em; text-decoration:none;">';
            echo htmlspecialchars($row['title']);
            echo '</a>';
            echo '<p style="margin:8px 0; color:#ccc;">' . nl2br($short) . '</p>';
            echo '<small style="color:#888;">' . date('d.m.Y H:i', strtotime($row['created_at'])) . '</small>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p style="color:#aaa; font-style:italic; margin-top:16px;">У вас пока нет созданных постов.</p>';
    }

    // можно закрыть соединение, если больше запросов нет
    // $conn->close();
    ?>
</section>

    <!-- Скрипты -->
    <script src="socset/js/profile.js" defer></script>
    <script src="socset/js/picture.js" defer></script>

</body>
</html>