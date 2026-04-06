// Глобальные переменные
let selectedUserId = null;
let stickerListVisible = false;

// Ждём полной загрузки DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('Chat.js loaded');

    // Элементы
    const stickerTrigger = document.getElementById('sticker-trigger');
    const stickerList = document.getElementById('sticker-list');
    const messageInput = document.getElementById('message');
    const sendButton = document.getElementById('send-button');
    const allUsers = document.getElementById('all-users');

    // Проверяем, что все элементы найдены
    if (!stickerTrigger) console.error('sticker-trigger not found');
    if (!stickerList) console.error('sticker-list not found');
    if (!messageInput) console.error('message input not found');
    if (!sendButton) console.error('send button not found');

    // Переключение списка стикеров
    window.toggleStickerList = function() {
        stickerList.classList.toggle('show');
        stickerListVisible = stickerList.classList.contains('show');
        console.log('Sticker list toggled, visible:', stickerListVisible);
    };

    // Вставка стикера
    window.insertSticker = function(sticker) {
        if (!messageInput) return;
        messageInput.value += sticker;
        messageInput.focus();
        console.log('Sticker inserted:', sticker);
    };

    // Отправка сообщения (ваша существующая функция)
    window.sendMessage = function() {
        if (!selectedUserId) {
            alert('Выберите пользователя');
            return;
        }
        const message = messageInput.value.trim();
        if (message === '') {
            alert('Введите сообщение');
            return;
        }

        fetch('/socset/chat/sendMessage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `receiver_id=${selectedUserId}&message=${encodeURIComponent(message)}`
        })
        .then(response => response.text())
        .then(result => {
            if (result === 'success') {
                messageInput.value = '';
                loadMessages(selectedUserId);
            } else {
                alert('Ошибка при отправке');
            }
        })
        .catch(error => {
            console.error('Send error:', error);
            alert('Ошибка сети');
        });
    };

    // Загрузка сообщений
    window.loadMessages = function(userId) {
        fetch(`/socset/chat/getMessages.php?user_id=${userId}`)
            .then(response => response.json())
            .then(messages => {
                const container = document.getElementById('chat-messages');
                container.innerHTML = '';
                messages.forEach(msg => {
                    const div = document.createElement('div');
                    div.textContent = msg.message;
                    div.classList.add(msg.sender_id == currentUserId ? 'my-message' : 'other-message');
                    container.appendChild(div);
                });
                container.scrollTop = container.scrollHeight;
            })
            .catch(error => console.error('Load messages error:', error));
    };

    // Загрузка пользователей
    function getUsers() {
        fetch('/socset/chat/getUsers.php')
            .then(response => response.json())
            .then(users => {
                allUsers.innerHTML = '';
                users.forEach(user => {
                    if (user.id != currentUserId) {
                        const li = document.createElement('li');
                        li.textContent = user.username;
                        li.dataset.userId = user.id;
                        li.onclick = function() {
                            selectedUserId = user.id;
                            document.querySelectorAll('#all-users li').forEach(item => item.classList.remove('selected'));
                            this.classList.add('selected');
                            loadMessages(selectedUserId);
                        };
                        allUsers.appendChild(li);
                    }
                });
            })
            .catch(error => console.error('Get users error:', error));
    }

    // Автообновление
    function startAutoRefresh() {
        setInterval(() => {
            if (selectedUserId) loadMessages(selectedUserId);
        }, 3000);
    }

    // Отправка по Enter
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Инициализация
    getUsers();
    startAutoRefresh();
});