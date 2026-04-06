<?php
session_start();
include("application/db.php");
include("application/users.php");  

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-log'])) {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Заполните все поля";
    } else {
        // Предполагаем, что пароль хранится захешированным (password_hash)
        $stmt = $conn->prepare("SELECT id, us_name, password, admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Успешный вход → записываем ВСЁ нужное в сессию
                $_SESSION['id']    = $user['id'];
                $_SESSION['login'] = $user['us_name'];
                $_SESSION['email'] = $email;
                $_SESSION['admin'] = (int)$user['admin'];   
                header("Location: index.php");   
                exit;
            } else {
                $error = "Неверный пароль";
            }
        } else {
            $error = "Пользователь с таким email не найден";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/log.css">
    <title>Авторизация</title>
</head>
<body>
    <?php include("menu.php"); ?>

    <div class="container">
        <form class="reg" method="post" action="auth.php">
            <h3>Авторизация</h3>

            <?php if ($error): ?>
                <div style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Адрес электронной почты</label>
                <input name="email" type="email" class="form-control" id="exampleInputEmail1" required>
                <div id="emailHelp" class="form-text">Мы никогда и никому не передадим вашу почту</div>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Пароль</label>
                <input name="password" type="password" class="form-control" id="exampleInputPassword1" required>
            </div>
            <button name="button-log" type="submit" class="btn btn-primary">Войти</button>
            <a href="registration.php">Зарегистрироваться</a>
            <div class="form-text1">Если еще не регистрировались</div>
        </form>
    </div>
</body>
</html>