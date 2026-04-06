<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главные Новости</title>
    <link rel="stylesheet" href="../css/menu.css"> <!-- Стили меню -->
    <link rel="stylesheet" href="../css/main-posts.css"> <!-- Стили для постов -->
</head>
<body>
    <?php include("../menu.php"); ?>

    <header>
        <h1>Посты</h1>
    </header>

    <div class="container">
        <?php
        if (isset($_SESSION['id'])) {
            echo '<a href="createpost.php" class="create-post-button">Создать пост</a>';
        }
        ?>

        <div class="posts">
            <?php include("showposts.php"); ?>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Получаем все посты по классу
            var posts = document.querySelectorAll('.post');
            posts.forEach(function(post) {
                // Добавляем обработчик события при наведении мыши
                post.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#505766'; // Светлее при наведении
                });
                // Добавляем обработчик события при уходе мыши
                post.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = ''; // Возвращаем исходный цвет
                });
            });
        });
    </script>
</body>
</html>