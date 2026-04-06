document.addEventListener("DOMContentLoaded", async function () {
    // Получаем элементы DOM (исправлен ID)
    const accountCreationDate = document.getElementById("account-creation-date");
    const userEmail = document.getElementById("user-email");
    const userInfo = document.getElementById("user-info");

    // Функция для загрузки информации о пользователе
    async function loadProfileInfo() {
        try {
            const response = await fetch('getProfileInfo.php', {
                method: 'POST',
            });

            const data = await response.text(); // добавлен знак =

            // Разбиваем строку на отдельные значения
            const [email, created, info] = data.split('|');

            // Устанавливаем значения полей профиля (используем innerText для span)
            if (userEmail) userEmail.innerText = email;      // было: userEmail.innerText email;
            if (accountCreationDate) accountCreationDate.innerText = created; // было: accountCreationDate.innerText created;
            if (userInfo) userInfo.innerText = info;         // было: userInfo.innerText info;
        } catch (error) {
            console.error(error.message); // убрано лишнее "ge"
        }
    }

    // Загружаем информацию о пользователе при загрузке страницы
    loadProfileInfo();
});