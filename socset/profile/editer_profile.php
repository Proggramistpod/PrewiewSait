<?php
session_start();
include_once "../application/db.php";

// Проверка авторизации
if (!isset($_SESSION['id'])) {
    header("Location: ../auth.php");
    exit;
}

$user_id = $_SESSION['id'];
$errors = [];

// Получаем текущие данные пользователя из БД
$query = "SELECT us_name, age, email, info FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_name, $current_age, $current_email, $current_info);
$stmt->fetch();
$stmt->close();

// Если форма отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['button-upd'])) {
    $name = trim($_POST['user-name']);
    $age = trim($_POST['user-age']);
    $email = trim($_POST['user-email']);
    $info = trim($_POST['user-info']);
    $pass_first = $_POST['pass-first'];
    $pass_second = $_POST['pass-second'];

    // Валидация
    if (empty($name)) $errors[] = "ФИО не может быть пустым.";
    if (!is_numeric($age) || $age <= 0) $errors[] = "Возраст должен быть положительным числом.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Некорректный email.";

    // Проверка пароля (если заполнен)
    if (!empty($pass_first) || !empty($pass_second)) {
        if ($pass_first !== $pass_second) {
            $errors[] = "Пароли не совпадают.";
        } elseif (strlen($pass_first) < 6) {
            $errors[] = "Пароль должен содержать минимум 6 символов.";
        }
    }

    // Проверка уникальности email (исключая текущего пользователя)
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Этот email уже используется другим пользователем.";
        }
        $stmt->close();
    }

    // Если ошибок нет — обновляем данные
    if (empty($errors)) {
        if (!empty($pass_first)) {
            // Смена пароля
            $hashed = password_hash($pass_first, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET us_name = ?, age = ?, email = ?, info = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sissi", $name, $age, $email, $info, $hashed, $user_id);
        } else {
            // Без смены пароля
            $stmt = $conn->prepare("UPDATE users SET us_name = ?, age = ?, email = ?, info = ? WHERE id = ?");
            $stmt->bind_param("sissi", $name, $age, $email, $info, $user_id);
        }

        if ($stmt->execute()) {
            // Обновляем сессию
            $_SESSION['login'] = $name;
            $_SESSION['age'] = $age;
            $_SESSION['email'] = $email;
            $_SESSION['info'] = $info;

            // Перенаправляем в личный кабинет
            header("Location: /profile/accaunt.php");
            exit;
        } else {
            $errors[] = "Ошибка при обновлении: " . $conn->error;
        }
        $stmt->close();
    }
}

// Для отображения в форме используем отправленные данные (если были ошибки) или текущие из БД
$display_name = $_POST['user-name'] ?? $current_name;
$display_age = $_POST['user-age'] ?? $current_age;
$display_email = $_POST['user-email'] ?? $current_email;
$display_info = $_POST['user-info'] ?? $current_info;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать профиль</title>
    <link rel="stylesheet" href="../css/editer-styles.css">
    <link rel="stylesheet" href="../css/menu.css">
</head>
<body>
    <header>
        <h1>Редактировать профиль</h1>
    </header>

    <?php include("../menu.php"); ?>

    <section id="edit-profile-form">
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="editer_profile.php" method="post">
            <label for="user-name">ФИО:</label><br>
            <input type="text" id="user-name" name="user-name" value="<?php echo htmlspecialchars($display_name); ?>"><br>

            <label for="user-age">Возраст:</label><br>
            <input type="text" id="user-age" name="user-age" value="<?php echo htmlspecialchars($display_age); ?>"><br>

            <label for="user-email">Email:</label><br>
            <input type="email" id="user-email" name="user-email" value="<?php echo htmlspecialchars($display_email); ?>"><br>

            <label for="user-info">О себе:</label><br>
            <textarea id="user-info" name="user-info"><?php echo htmlspecialchars($display_info); ?></textarea><br>

            <label for="pass-first">Новый пароль (оставьте пустым, если не хотите менять):</label><br>
            <input type="password" id="pass-first" name="pass-first"><br>

            <label for="pass-second">Повторите новый пароль:</label><br>
            <input type="password" id="pass-second" name="pass-second"><br>

            <button name="button-upd" type="submit">Сохранить изменения</button>
        </form>
    </section>

    <script src="../js/profile.js" defer></script>
</body>
</html>