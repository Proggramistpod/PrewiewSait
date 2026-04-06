<?php
include_once '../application/db.php';

// SQL запрос на выборку всех постов
$sql = "SELECT id, title, content, image_url, created_at FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<div class="post">';
        echo '<div class="post-img-container">';
        if (!empty($row["image_url"])) {
            echo '<img src="/socset/' . htmlspecialchars($row["image_url"]) . '" alt="Post Image">';
        }
        echo '</div>';
        echo '<div class="post-content">';
        echo '<h2><a href="post.php?id=' . $row["id"] . '" style="color: #FFC000; text-decoration: none;">' . htmlspecialchars($row["title"]) . '</a></h2>';
        // Обрезаем контент до 200 символов с учётом многобайтовых символов
        $short_content = mb_substr($row["content"], 0, 200, 'UTF-8');
        echo '<p>' . nl2br(htmlspecialchars($short_content)) . '...</p>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo "Постов пока нет.";
}
$conn->close();
?>