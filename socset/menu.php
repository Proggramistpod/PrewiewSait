<?php
//session_start(); // обязательно в начале, если не вызывается раньше
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Меню</title>
    <link rel="stylesheet" href="/socset/css/menu.css">
</head>
<body>

<input class="menu-icon" type="checkbox" id="menu-icon" name="menu-icon"/>
<label for="menu-icon"></label>

<nav class="nav">
    <ul class="pt-5">
        <li><a href="/socset/">Главная</a></li>

        <?php if (isset($_SESSION['id'])): ?>
            <li><a href="/socset/profile/accaunt.php">Личный кабинет</a></li>
            <li><a href="/socset/chat/chat.php">Чат</a></li>
        <?php else: ?>
            <li><a href="/socset/auth.php">Личный кабинет</a></li>
            <li><a href="/socset/auth.php">Чат</a></li>
        <?php endif; ?>

        <li><a href="/socset/posts/posts.php">Новости</a></li>
        <li><a href="/socset/game/games.php">Игра</a></li>

        <?php if (isset($_SESSION['id'])): ?>
            <li><a href="/socset/shop/shop.php">Магазин</a></li>
            <li><a href="/socset/video/all_videos.php">Видео</a></li>
            <li><a href="/socset/music/music_list.php">Музыка</a></li>
            <li><a href="/socset/posts/createpost.php" class="create-post-btn">Создать пост</a></li>
        <?php else: ?>
            <li><a href="/socset/auth.php">Магазин</a></li>
            <li><a href="/socset/auth.php">Видео</a></li>
            <li><a href="/socset/auth.php">Музыка</a></li>
        <?php endif; ?>
    </ul>
</nav>

</body>
</html>