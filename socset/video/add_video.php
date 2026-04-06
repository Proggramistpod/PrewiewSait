<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить Видео</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/loader.css">
    <link rel="stylesheet" href="../css/add_video.css">
    <script src="../js/loader.js"></script>
</head>
<body>
<header>
    <h1>Добавить Видео</h1>
</header>

<?php
session_start();
include("../menu.php");
include("../application/loader.php");
?>

<div class="container">
    <form action="add_to_base.php" method="post" enctype="multipart/form-data">
        <label for="video_name">Название Видео:</label><br>
        <input type="text" id="video_name" name="video_name" required><br>

        <label for="video_description">Описание Видео:</label><br>
        <textarea id="video_description" name="video_description"></textarea><br>

        <label for="video_source">Источник Видео:</label><br>
        <select id="video_source" name="video_source" onchange="toggleInput()" required>
            <option value="" selected disabled>Выберите вариант</option>
            <option value="link">Ссылка (YouTube / Rutube)</option>
            <option value="file">Файл (MP4 / WebM)</option>
        </select><br>

        <div id="file_input" class="file-input-container">
            <label for="video_file">Загрузить файл Видео:</label><br>
            <input type="file" id="video_file" name="video_file" accept="video/mp4, video/webm">
        </div>

        <div id="link_input" class="file-input-container">
            <label for="video_link">Ссылка на Видео:</label><br>
            <input type="text" id="video_link" name="video_link" placeholder="https://youtu.be/... или https://rutube.ru/...">
        </div>

        <input type="submit" value="Добавить Видео">
    </form>
</div>

<script>
    function toggleInput() {
        var source = document.getElementById("video_source").value;
        var fileInput = document.getElementById("file_input");
        var linkInput = document.getElementById("link_input");

        if (source === "file") {
            fileInput.classList.remove("fade-out");
            fileInput.classList.add("fade-in");
            linkInput.classList.remove("fade-in");
            linkInput.classList.add("fade-out");
        } else if (source === "link") {
            fileInput.classList.remove("fade-in");
            fileInput.classList.add("fade-out");
            linkInput.classList.remove("fade-out");
            linkInput.classList.add("fade-in");
        } else {
            fileInput.classList.remove("fade-in");
            fileInput.classList.add("fade-out");
            linkInput.classList.remove("fade-in");
            linkInput.classList.add("fade-out");
        }
    }
    // Изначально скрываем оба блока
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("file_input").classList.add("fade-out");
        document.getElementById("link_input").classList.add("fade-out");
    });
</script>
</body>
</html>