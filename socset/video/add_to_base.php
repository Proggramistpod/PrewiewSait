<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../auth.php");
    exit;
}

include_once '../application/db.php';

$video_name = $_POST['video_name'];
$video_description = $_POST['video_description'];
$video_source = $_POST['video_source'];
$video_link = ($_POST['video_link']) ? trim($_POST['video_link']) : null;
$video_path = null;

$user_id = $_SESSION['id'];
$username = $_SESSION['login'];

if ($video_source == 'file') {
    // Обработка загрузки файла
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($_FILES["video_file"]["name"]);
    $uploadOk = 1;
    $fileExtension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Проверка типа файла
    if (!in_array($fileExtension, array("mp4", "webm"))) {
        echo "Допустимы только файлы MP4, WebM";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Файл не загружен.";
    } else {
        if (move_uploaded_file($_FILES["video_file"]["tmp_name"], $target_file)) {
            $video_path = $target_file;
        } else {
            echo "Ошибка при загрузке файла.";
            exit;
        }
    }
} elseif ($video_source == 'link') {
    if (!empty($video_link)) {
        // YouTube
        if (strpos($video_link, 'youtube.com') !== false || strpos($video_link, 'youtu.be') !== false) {
            $video_id = '';
            if (strpos($video_link, 'youtube.com') !== false) {
                parse_str(parse_url($video_link, PHP_URL_QUERY), $params);
                $video_id = $params['v'] ?? '';
            } elseif (strpos($video_link, 'youtu.be') !== false) {
                $path = parse_url($video_link, PHP_URL_PATH);
                $video_id = trim($path, '/');
            }
            if ($video_id) {
                $video_path = "https://www.youtube.com/embed/" . $video_id;
            }
        }
        // Rutube
        elseif (strpos($video_link, 'rutube.ru') !== false) {
            $path = parse_url($video_link, PHP_URL_PATH);
            $video_path = "https://rutube.ru/play/embed/" . basename($path);
        } else {
            echo "Неподдерживаемая ссылка. Используйте YouTube или Rutube.";
            exit;
        }
    } else {
        echo "Ссылка на видео не указана.";
        exit;
    }
}

if ($video_path !== null) {
    $sql = "INSERT INTO videos (user_id, username, video_name, video_description, video_source, video_path) 
            VALUES ('$user_id', '$username', '$video_name', '$video_description', '$video_source', '$video_path')";
    if ($conn->query($sql) === TRUE) {
        header("Location: all_videos.php");
        exit;
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>