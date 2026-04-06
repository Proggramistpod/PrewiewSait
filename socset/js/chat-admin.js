// Глобальные переменные
let selectedUserId = 0; // 0 означает "всем администраторам" (или общий чат)
let stickerListVisible = false;

// Функция для переключения видимости списка стикеров
function toggleStickerList() {
    const stickerList = document.getElementById('sticker-list');
    if (stickerList) {
        stickerList.classList.toggle('show');
        stickerListVisible = stickerList.classList.contains('show');
    }
}

// Функция для вставки стикера в поле ввода сообщения
function insertSticker(sticker) {
    const messageInput = document.getElementById('message');
    if (messageInput) {
        messageInput.value += sticker;
        messageInput.focus();
    }
}

// Функция для получения списка пользователей (для администратора – все пользователи)
function getUsers() {
    fetch('/socset/AdminChat/getUsers.php') // Путь к скрипту получения пользователей
        .then(response => {
            if (!response.ok) throw new Error('Ошибка сети при загрузке пользователей');
            return response.json();
        })
        .then(users => {
            const userList = document.getElementById('all-users');
            if (!userList) return;
            userList.innerHTML = '';

            // Добавляем каждого пользователя в список
            users.forEach(user => {
                // Не добавляем самого администратора? Если нужно, можно проверить:
                // if (user.id == currentUserId) return; // если currentUserId доступен

                const listItem = document.createElement('li');
                listItem.textContent = user.username;
                listItem.dataset.userId = user.id;

                listItem.onclick = () => {
                    // При выборе пользователя обновляем ID выбранного пользователя и загружаем его сообщения
                    selectedUserId = user.id;
                    loadMessages(selectedUserId);

                    // Убираем стиль выбранного у всех пользователей
                    document.querySelectorAll('#all-users li').forEach(item => {
                        item.classList.remove('selected');
                    });

                    // Добавляем стиль выбранного пользователю
                    listItem.classList.add('selected');
                };

                userList.appendChild(listItem);
            });
        })
        .catch(error => {
            console.error('Ошибка загрузки пользователей:', error);
        });
}

// Функция для загрузки сообщений с выбранным пользователем
function loadMessages(receiverUserId) {
    const chatMessages = document.getElementById('chat-messages');
    if (!chatMessages) return;

    // Показываем индикатор загрузки (опционально)
    chatMessages.innerHTML = '<p>Загрузка сообщений...</p>';

    // Определяем URL в зависимости от того, общий чат (receiverUserId=0) или личный
    let url;
    if (receiverUserId === 0) {
        url = '/socset/AdminChat/getCommonMessages.php'; // для общих сообщений
    } else {
        url = `/socset/AdminChat/getMessages.php?user_id=${receiverUserId}`;
    }

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Ошибка загрузки сообщений');
            return response.json(); // ожидаем JSON
        })
        .then(messages => {
            chatMessages.innerHTML = '';
            if (messages.length === 0) {
                chatMessages.innerHTML = '<p>Нет сообщений</p>';
                return;
            }
            messages.forEach(msg => {
                const messageElement = document.createElement('div');
                messageElement.textContent = msg.message; // предполагаем, что объект содержит поле message
                // Можно добавить классы в зависимости от отправителя
                if (msg.sender_id == currentUserId) {
                    messageElement.classList.add('my-message');
                } else {
                    messageElement.classList.add('other-message');
                }
                chatMessages.appendChild(messageElement);
            });
            // Прокрутка вниз
            chatMessages.scrollTop = chatMessages.scrollHeight;
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            chatMessages.innerHTML = '<p style="color:red;">Ошибка загрузки сообщений</p>';
        });
}

// Функция для отправки сообщения
function sendMessage() {
    const messageInput = document.getElementById('message');
    if (!messageInput) return;
    const messageText = messageInput.value.trim();

    if (messageText === '') {
        alert('Введите сообщение');
        return;
    }

    // Определяем получателя: если selectedUserId == 0, отправляем всем (или общий чат)
    const receiverId = selectedUserId;

    // Отправляем запрос на сервер
    fetch('/socset/AdminChat/sendMessage.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `receiver_id=${receiverId}&message=${encodeURIComponent(messageText)}`
    })
        .then(response => response.text())
        .then(responseText => {
            console.log('Response from sendMessage.php:', responseText);
            if (responseText === 'success') {
                messageInput.value = ''; // Очищаем поле
                loadMessages(receiverId); // Обновляем сообщения
            } else {
                alert('Ошибка при отправке сообщения');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Ошибка сети');
        });
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    console.log('chat-admin.js loaded');

    // Проверяем, что пользователь админ (переменная из PHP)
    if (typeof isAdmin === 'undefined' || !isAdmin) {
        console.error('Доступ запрещён: не администратор');
        return;
    }

    // Загружаем список пользователей
    getUsers();

    // Если нужно, загружаем общие сообщения (по умолчанию, например, общий чат)
    loadMessages(0);

    // Обработчик для кнопки со стикерами (если есть)
    const stickerButton = document.getElementById('sticker-button');
    if (stickerButton) {
        stickerButton.addEventListener('click', toggleStickerList);
    }

    // Обработчик для отправки по Enter
    const messageInput = document.getElementById('message');
    if (messageInput) {
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
});