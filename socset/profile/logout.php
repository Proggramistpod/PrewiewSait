<?php
// Запускаем сессию, если она ещё не активна
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Полностью очищаем массив сессии
$_SESSION = array();

// Если используются cookie сессии — удаляем их
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Уничтожаем сессию на сервере
session_destroy();

// Перенаправляем на главную страницу (корень проекта)
header("Location: /socset/");
exit;
?>