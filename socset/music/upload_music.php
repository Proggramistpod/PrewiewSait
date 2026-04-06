<?php
include_once '../application/db.php';

if(isset($_FILES['music_file'])) {
    $file_name = $_FILES['music_file']['name'];
    $file_tmp = $_FILES['music_file']['tmp_name'];
    $file_type = $_FILES['music_file']['type'];

    if($file_type == 'audio/mpeg' || $file_type == 'audio/mp3') {
        session_start();
        $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : '';
        $username = isset($_SESSION['login']) ? $_SESSION['login'] : '';

        $file_name_without_extension = pathinfo($file_name, PATHINFO_FILENAME);

        // Создаём папку uploads, если её нет
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        move_uploaded_file($file_tmp, "uploads/$file_name");

        $music_path = "uploads/$file_name";

        $sql = "INSERT INTO music (user_id, username, music_name, music_path) 
                VALUES ('$user_id', '$username', '$file_name_without_extension', '$music_path')";

        if ($conn->query($sql) === TRUE) {
            header("Location: music_list.php");
            exit();
        } else {
            echo "Ошибка при загрузке музыки: " . $conn->error;
        }
    } else {
        echo "Неподдерживаемый формат файла. Пожалуйста, загрузите файл в формате MP3.";
    }
} else {
    echo "Файл не был отправлен.";
}
$conn->close();
?>