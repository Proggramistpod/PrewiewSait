<?php
session_start();
include("../menu.php");
include("../application/loader.php");
include_once "../application/db.php";

// Проверка подключения
if (!$conn || $conn->connect_error) {
    die("Ошибка подключения к базе данных: " . ($conn ? $conn->connect_error : "conn не определен"));
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p style='color:red; text-align:center;'>Неверный идентификатор видео.</p>";
    exit;
}
$video_id = (int)$_GET['id'];

// Получаем данные видео
$stmt = $conn->prepare("SELECT * FROM videos WHERE id = ?");
if (!$stmt) {
    die("Ошибка подготовки запроса видео: " . $conn->error);
}
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "<p style='color:red; text-align:center;'>Видео не найдено.</p>";
    exit;
}
$video = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр видео</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/loader.css">
    <link rel="stylesheet" href="../css/video_styles.css">
    <script src="../js/loader.js"></script>
</head>
<body>
<header>
    <h1>Видеоплеер</h1>
</header>

<div class="video-container">
    <h2><?= htmlspecialchars($video['video_name']) ?></h2>
    <p><strong>Автор:</strong> <?= htmlspecialchars($video['username']) ?></p>
    <p><?= nl2br(htmlspecialchars($video['video_description'])) ?></p>

    <?php if ($video['video_source'] == 'file'): ?>
        <video width="100%" controls>
            <source src="<?= htmlspecialchars($video['video_path']) ?>" type="video/mp4">
            Ваш браузер не поддерживает видео.
        </video>
    <?php else: ?>
        <iframe width="100%" height="500" src="<?= htmlspecialchars($video['video_path']) ?>"
                frameborder="0" allowfullscreen></iframe>
    <?php endif; ?>
</div>

<!-- Блок комментариев -->
<div class="comments-section">
    <h3>Комментарии</h3>
    <?php
    // Вывод существующих комментариев
    $stmt_comments = $conn->prepare("SELECT * FROM comments_for_video WHERE video_id = ? ORDER BY `created_at` DESC");
    if (!$stmt_comments) {
        echo "<p style='color:red'>Ошибка подготовки запроса комментариев: " . $conn->error . "</p>";
    } else {
        $stmt_comments->bind_param("i", $video_id);
        $stmt_comments->execute();
        $res_comments = $stmt_comments->get_result();

        if ($res_comments && $res_comments->num_rows > 0) {
            while ($comment = $res_comments->fetch_assoc()) {
                echo "<div class='comment'>";
                echo "<strong>" . htmlspecialchars($comment['author_name']) . "</strong> <small>(" . htmlspecialchars($comment['created_at']) . ")</small><br>";
                echo "<p>" . nl2br(htmlspecialchars($comment['content'])) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>Комментариев пока нет. Будьте первым!</p>";
        }
        $stmt_comments->close();
    }

    // Форма добавления комментария (только для авторизованных)
    if (isset($_SESSION['id'])): ?>
        <form action="add_comment.php" method="post" class="comment-form">
            <input type="hidden" name="video_id" value="<?= $video_id ?>">
            <textarea name="comment_content" rows="3" placeholder="Напишите комментарий..." required></textarea><br>
            <input type="submit" value="Отправить">
        </form>
    <?php else: ?>
        <p><a href="../auth.php">Войдите</a>, чтобы оставить комментарий.</p>
    <?php endif; ?>
</div>

</body>
</html>