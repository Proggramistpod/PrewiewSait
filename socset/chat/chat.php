<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link rel="stylesheet" href="/socset/css/menu.css">
    <link rel="stylesheet" href="/socset/css/chat-styles.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/socset/menu.php'; ?>

    <div id="chat-container">
        <div id="user-list">
            <h3>Пользователи</h3>
            <ul id="all-users"></ul>
        </div>
        <div id="chat-messages">
            <h3>Выберите пользователя из списка для начала общения</h3>
        </div>
    </div>

    <div id="message-input">
        <input type="text" id="message" placeholder="Введите ваше сообщение...">
        <div id="sticker-trigger" onclick="toggleStickerList()">😊</div>
        <div id="sticker-list" class="hidden">
            <div class="sticker" onclick="insertSticker('😊')">😊</div>
            <div class="sticker" onclick="insertSticker('😂')">😂</div>
            <div class="sticker" onclick="insertSticker('❤️')">❤️</div>
            <div class="sticker" onclick="insertSticker('👍')">👍</div>
            <div class="sticker" onclick="insertSticker('🎉')">🎉</div>
        </div>
        <button id="send-button" onclick="sendMessage()">Отправить</button>
    </div>

    <script>
        <?php if (isset($_SESSION['id'])): ?>
            const currentUserId = <?php echo $_SESSION['id']; ?>;
        <?php else: ?>
            const currentUserId = null;
            window.location.href = "/socset/auth.php";
        <?php endif; ?>
    </script>
    <script src="/socset/js/chat.js"></script>

    <!-- Добавленный скрипт для автоматического выбора пользователя по параметру user_id -->
    <script>
        (function() {
            // Функция для получения параметра из URL
            function getUrlParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            const userIdParam = getUrlParameter('user_id');
            if (userIdParam) {
                // Ждём загрузки списка пользователей (возможно, асинхронно)
                const checkExist = setInterval(function() {
                    const userItems = document.querySelectorAll('#all-users li');
                    if (userItems.length > 0) {
                        clearInterval(checkExist);
                        // Ищем элемент с соответствующим data-id
                        let targetItem = null;
                        userItems.forEach(item => {
                            // Предполагаем, что у <li> есть data-id
                            if (item.dataset.id == userIdParam) {
                                targetItem = item;
                            }
                        });
                        if (targetItem) {
                            targetItem.click(); // эмулируем клик
                        } else {
                            console.warn('Пользователь с ID ' + userIdParam + ' не найден в списке');
                        }
                    }
                }, 100); // проверяем каждые 100 мс
            }
        })();
    </script>
</body>
</html>