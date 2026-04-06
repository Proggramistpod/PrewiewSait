<?php
session_start();
include_once '../application/db.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../auth.php");
    exit;
}

// Обработка формы (весь код из create.php сюда)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['id'];

    if (empty($title) || empty($content)) {
        $error = "Заголовок и содержание не могут быть пустыми.";
    } else {
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/posts/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $upload_file = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                $image_url = 'uploads/posts/' . $filename;
            } else {
                $error = "Ошибка при загрузке изображения.";
            }
        }

        if (!isset($error)) {
            $stmt = $conn->prepare("INSERT INTO posts (title, content, image_url, user_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $title, $content, $image_url, $user_id);
            
            if ($stmt->execute()) {
                header("Location: /socset/posts/posts.php?success=1");
                exit;
            } else {
                $error = "Ошибка сохранения: " . $stmt->error;
            }
            $stmt->close();
        }
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
    <link rel="stylesheet" href="../css/postcreate.css">
</head>
<body>
    <?php include("../menu.php"); ?>

    <header>
        <h1>Создать новый пост</h1>
    </header>

    <main>
        <?php if (isset($error)): ?>
            <div style="color:red; margin:20px; text-align:center;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <label for="title">Заголовок:</label><br>
            <input type="text" id="title" name="title" required><br><br>

            <label for="content">Содержание:</label><br>
            <textarea id="content" name="content" rows="6" required></textarea><br><br>

            <label for="image">Изображение:</label><br>
            <input type="file" id="image" name="image" accept="image/*"><br><br>

            <button type="submit">Создать пост</button>
        </form>
    </main>
</body>
</html>