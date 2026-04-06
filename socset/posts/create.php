<?php
// create_post.php
session_start();

// Если не авторизован — перенаправляем на вход
if (!isset($_SESSION['id'])) {
    header("Location: ../auth.php");
    exit;
}

require_once '../application/db.php'; // подключаем соединение с БД

$errors   = [];
$success  = false;
$title    = '';
$content  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']   ?? '');
    $content = trim($_POST['content'] ?? '');

    // Валидация
    if (empty($title)) {
        $errors[] = "Заголовок обязателен";
    }
    if (empty($content)) {
        $errors[] = "Содержание поста обязательно";
    }

    $image_url = null;

    // Обработка загрузки изображения (опционально)
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('post_') . '.' . $ext;
        $target = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_url = 'uploads/posts/' . $filename;
        } else {
            $errors[] = "Не удалось загрузить изображение";
        }
    }

    // Если ошибок нет — сохраняем в базу
    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO posts (title, content, image_url, user_id, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->bind_param("sssi", $title, $content, $image_url, $_SESSION['id']);

        if ($stmt->execute()) {
            $success = true;
            $title = $content = ''; // очищаем форму
        } else {
            $errors[] = "Ошибка базы данных: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать пост</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/postcreate.css"> <!-- если есть -->
    <style>
        .error { color: #ff6b6b; margin: 8px 0; }
        .success { color: #51cf66; margin: 12px 0; font-weight: bold; }
        form { max-width: 700px; margin: 30px auto; padding: 20px; background: #1e2533; border-radius: 8px; }
        input[type="text"], textarea { width: 100%; padding: 10px; margin: 8px 0; background: #2d3748; border: 1px solid #4a5568; color: white; border-radius: 4px; }
        textarea { min-height: 160px; }
        button { padding: 12px 24px; background: #4c51bf; border: none; color: white; font-weight: bold; border-radius: 6px; cursor: pointer; }
        button:hover { background: #5a67d8; }
    </style>
</head>
<body>

<?php include("../menu.php"); ?>

<main>
    <h1 style="text-align:center; margin: 30px 0;">Создать новый пост</h1>

    <?php if ($success): ?>
        <div class="success">Пост успешно опубликован! 
            <a href="../posts/posts.php" style="color:#a5b4fc;">Посмотреть все посты</a>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div style="color:#ff6b6b; background:#742a2a; padding:12px; border-radius:6px; margin:20px auto; max-width:700px;">
            <?php foreach ($errors as $err): ?>
                <p>× <?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="title">Заголовок <span style="color:#f87171;">*</span></label><br>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>

        <label for="content">Содержание <span style="color:#f87171;">*</span></label><br>
        <textarea id="content" name="content" required><?= htmlspecialchars($content) ?></textarea>

        <label for="image">Изображение (необязательно):</label><br>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">

        <button type="submit" style="margin-top:20px;">Опубликовать пост</button>
    </form>
</main>
</body>
</html>