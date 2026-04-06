<?php
session_start();

// Проверка: только администратор (admin == 1) имеет доступ
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    // Если не админ, перенаправляем на главную страницу
    header("Location: /socset/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат администратора</title>
    <!-- Подключение стилей (абсолютные пути от корня сайта) -->
    <link rel="stylesheet" href="/socset/css/menu.css">
    <link rel="stylesheet" href="/socset/css/chat-styles.css">
</head>
<body>
    <!-- Подключение меню с использованием абсолютного пути на сервере -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/socset/menu.php'; ?>

    <div id="chat-container">
        <!-- Боковая панель со списком пользователей (заполняется через JS) -->
        <div id="user-list">
            <h3>Пользователи</h3>
            <ul id="all-users">
                <!-- Сюда будут добавлены пользователи динамически -->
            </ul>
        </div>

        <!-- Область отображения сообщений -->
        <div id="chat-messages">
            <h3>Выберите пользователя для начала общения</h3>
            <!-- Сообщения будут загружаться через JS -->
        </div>
    </div>

    <!-- Поле ввода сообщения и кнопка отправки (как в обычном чате) -->
    <div id="message-input">
        <input type="text" id="message" placeholder="Введите ваше сообщение...">
        <!-- Опционально можно добавить стикеры, если нужно -->
        <button id="send-button" onclick="sendMessage()">Отправить</button>
    </div>

    <!-- Передаём данные сессии в JavaScript -->
    <script>
        const currentUserId = <?php echo json_encode($_SESSION['id']); ?>;
        const isAdmin = true; // флаг администратора, может использоваться в скрипте
    </script>

    <!-- Подключение скрипта для чата администратора -->
    <script src="/socset/js/chat-admin.js"></script>
</body>
</html>