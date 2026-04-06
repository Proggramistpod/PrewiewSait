document.addEventListener("DOMContentLoaded", function() {
    // Получаем кнопку "Загрузить музыку" и модальное окно
    var uploadButton = document.getElementById("uploadButton");
    var modal = document.getElementById("myModal");

    // При клике на кнопку "Загрузить музыку" показываем модальное окно
    uploadButton.onclick = function() {
        modal.style.display = "block";
    }

    // Получаем кнопку закрытия модального окна
    var closeButton = document.getElementsByClassName("close")[0];

    // При клике на кнопку закрытия скрываем модальное окно
    closeButton.onclick = function() {
        modal.style.display = "none";
    }

    // При клике вне модального окна также скрываем его
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Получаем все аудио элементы на странице
    var audioElements = document.querySelectorAll('audio');

    // Проходимся по каждому аудио элементу
    audioElements.forEach(function(audioElement, index) {
        // Добавляем обработчик события 'ended' для каждого аудио элемента
        audioElement.addEventListener('ended', function() {
            // Находим следующий аудио элемент
            var nextAudioElement = audioElements[index + 1];
            // Если следующий аудио элемент существует, запускаем его воспроизведение
            if (nextAudioElement) {
                nextAudioElement.play();
            }
        });
    });
});