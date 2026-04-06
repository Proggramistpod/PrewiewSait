<?php
session_start();
include_once '../application/db.php';

// Получаем ID поста из URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.0 404 Not Found');
    die('Пост не найден');
}

$post_id = (int)$_GET['id'];

// Запрашиваем пост вместе с информацией об авторе
$stmt = $conn->prepare("
    SELECT p.id, p.title, p.content, p.image_url, p.created_at,
           u.us_name, u.id as author_id
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('HTTP/1.0 404 Not Found');
    die('Пост не найден');
}

$post = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/main-posts.css"> <!-- или свой стиль для одного поста -->
    <style>
        .single-post {
            max-width: 800px;
            margin: 2rem auto;
            background: #1e2533;
            padding: 2rem;
            border-radius: 8px;
        }
        .single-post img {
            max-width: 100%;
            border-radius: 4px;
            margin: 1rem 0;
        }
        .post-meta {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .post-content {
            line-height: 1.6;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #a5b4fc;
        }
    </style>
</head>
<body>
    <?php include("../menu.php"); ?>

    <div class="single-post">
        <a href="posts.php" class="back-link">&larr; К списку постов</a>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-meta">
            Автор: <?= htmlspecialchars($post['us_name']) ?> |
            Дата: <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>
        </div>

        <?php if (!empty($post['image_url'])): ?>
            <img src="/socset/<?= htmlspecialchars($post['image_url']) ?>" alt="Изображение поста">
        <?php endif; ?>

        <div class="post-content">
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>
    </div>
</body>
</html>