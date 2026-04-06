// Ожидание полной загрузки DOM
document.addEventListener("DOMContentLoaded", async function() {
    // Получаем ссылки на кнопки, элемент выбора файла и изображение профиля
    const updatePictureButton = document.getElementById("update-picture");
    const deletePictureButton = document.getElementById("delete-picture");
    const fileInput = document.getElementById("file-input");
    const profilePicture = document.getElementById("profile-picture");

    // Функция для удаления профильной картинки
    async function deleteProfilePicture() {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "upload-profile-picture.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        resolve(xhr.responseText);
                    } else {
                        reject(new Error("Ошибка удаления изображения"));
                    }
                }
            };
            xhr.send("action=delete");
        });
    }

    // Обработчик клика на кнопку "Обновить картинку"
    if (updatePictureButton) {
        updatePictureButton.addEventListener("click", function() {
            fileInput.click();
        });
    }

    // Обработчик изменения в поле ввода файла
    if (fileInput) {
        fileInput.addEventListener("change", async function() {
            if (fileInput.files.length === 0) return;

            const formData = new FormData();
            formData.append("action", "update");
            formData.append("image", fileInput.files[0]);

            try {
                const response = await uploadProfilePicture(formData);
                if (response.startsWith("success")) {
                    const path = response.split("|")[1]; // ожидаем формат "success|путь/к/файлу"
                    if (path) profilePicture.src = path;
                    location.reload(); // Перезагрузка страницы для обновления данных
                } else {
                    console.error("Ошибка загрузки:", response);
                }
            } catch (error) {
                console.error(error.message);
            }
        });
    }

    // Обработчик клика на кнопку "Удалить картинку"
    if (deletePictureButton) {
        deletePictureButton.addEventListener("click", async function() {
            try {
                const response = await deleteProfilePicture();
                if (response.startsWith("success")) {
                    // После удаления получаем актуальный путь (или ставим заглушку)
                    const updatedPath = await getProfilePicture();
                    if (updatedPath && updatedPath !== "null") {
                        profilePicture.src = updatedPath;
                    } else {
                        profilePicture.src = "avatars/placeholder.jpg";
                    }
                    location.reload(); // Перезагрузка страницы
                } else {
                    console.error("Ошибка удаления:", response);
                }
            } catch (error) {
                console.error(error.message);
            }
        });
    }

    // Функция для получения пути к текущей профильной картинке
    async function getProfilePicture() {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "upload-profile-picture.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        resolve(xhr.responseText);
                    } else {
                        reject(new Error("Ошибка получения пути к изображению"));
                    }
                }
            };
            xhr.send("action=getProfilePicture");
        });
    }

    // Функция для загрузки профильной картинки на сервер
    async function uploadProfilePicture(formData) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "upload-profile-picture.php", true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    resolve(xhr.responseText);
                } else {
                    reject(new Error("Ошибка загрузки изображения"));
                }
            };
            xhr.onerror = function() {
                reject(new Error("Ошибка сети при загрузке"));
            };
            xhr.send(formData);
        });
    }

    // Получаем текущий путь к профильной картинке и устанавливаем его
    try {
        const profilePicturePath = await getProfilePicture();
        if (profilePicturePath && profilePicturePath !== "null") {
            profilePicture.src = profilePicturePath;
        } else {
            profilePicture.src = "avatars/placeholder.jpg";
        }
    } catch (error) {
        console.error("Не удалось загрузить путь к аватару:", error);
        profilePicture.src = "avatars/placeholder.jpg";
    }
});