<?php
include("application/users.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/log.css">
    <title>Регистрация</title>
    
</head>

<body>
    <?php include "menu.php"; ?>

    <!-- Начало формы регистрации -->
    <center>
        <div class="container">
            <form class="reg" method="post" action="registration.php">
                <h3>Регистрация</h3>

                <div class="mb-3">
                    <label for="name" class="form-label">ФИО</label>
                    <input name="login" type="text" class="form-control" id="name" placeholder="Введите ваше ФИО">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Адрес электронной почты</label>
                    <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">Мы никогда никому не передадим вашу почту.</div>
                </div>

                <div class="mb-3">
                    <label for="age" class="form-label">Возраст</label>
                    <input name="age" type="number" class="form-control" id="age">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input name="pass-first" type="password" class="form-control" id="password">
                </div>

                <div class="mb-3">
                    <label for="password2" class="form-label">Введите пароль повторно</label>
                    <input name="pass-second" type="password" class="form-control" id="password2">
                </div>

                <button name="button-reg" type="submit" class="btn btn-primary">Отправить</button>
                <a href="auth.php">Авторизоваться</a>
                <div class="form-text1">Если вы уже зарегистрированы, нажмите на кнопку выше.</div>
            </form>
        </div>
    </center>
    <!-- Конец формы регистрации -->
</body>
</html>