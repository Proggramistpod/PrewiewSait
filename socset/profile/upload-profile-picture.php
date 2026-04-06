<?php
session_start();
include_once '../application/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? '';

    if ($action == "delete") {
        // Удаление картинки профиля
        if (!isset($_SESSION['id'])) {
            echo "error|Пользователь не авторизован";
            exit;
        }
        $userId = $_SESSION['id'];

        // Удаляем все файлы пользователя в папке avatars (формат: {userId}.*)
        $files = glob("avatars/{$userId}.*");
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // Обновляем запись в БД: profile_picture = NULL
        updateProfilePicturePath($userId, null);

        // Возвращаем путь к заглушке для немедленного обновления на странице
        echo "success|avatars/placeholder.jpg";
        exit;

    } elseif ($action == "update") {
        // Обновление картинки профиля
        if (!isset($_SESSION['id'])) {
            echo "error|Пользователь не авторизован";
            exit;
        }
        $userId = $_SESSION['id'];

        // Проверяем, загружен ли файл без ошибок
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
            $targetDir = "avatars/";
            // Создаём папку, если её нет
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Получаем расширение загруженного файла
            $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $targetFile = $targetDir . $userId . '.' . $extension;

            // Удаляем старые аватары этого пользователя (чтобы не было дублей)
            $oldFiles = glob("avatars/{$userId}.*");
            foreach ($oldFiles as $oldFile) {
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            // Перемещаем загруженный файл
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                // Обновляем путь в БД
                updateProfilePicturePath($userId, $targetFile);
                echo "success|" . $targetFile;
                exit;
            } else {
                echo "error|Ошибка при сохранении файла.";
                exit;
            }
        } else {
            echo "error|Файл не был загружен или произошла ошибка.";
            exit;
        }

    } elseif ($action == "getProfilePicture") {
        // Получение текущего пути к картинке профиля
        if (!isset($_SESSION['id'])) {
            echo "null";
            exit;
        }
        $userId = $_SESSION['id'];
        $path = getProfilePicturePath($userId);
        echo $path !== null ? $path : "null";
        exit;

    } else {
        echo "error|Некорректное действие.";
        exit;
    }
} else {
    echo "error|Недопустимый метод запроса.";
    exit;
}

// Функция для обновления пути к изображению профиля в базе данных
function updateProfilePicturePath($userId, $filePath) {
    global $conn;
    if ($filePath === null) {
        $stmt = $conn->prepare("UPDATE users SET profile_picture = NULL WHERE id = ?");
        $stmt->bind_param("i", $userId);
    } else {
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $filePath, $userId);
    }
    $stmt->execute();
    $stmt->close();
}

// Функция для получения пути к изображению профиля из базы данных
function getProfilePicturePath($userId) {
    global $conn;
    $query = "SELECT profile_picture FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($profilePicture);
    $stmt->fetch();
    $stmt->close();
    return $profilePicture; // может быть null
}
?>