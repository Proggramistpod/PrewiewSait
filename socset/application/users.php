<?php
//session_start();
include_once "db.php";

function setSession($id, $us_name, $admin, $age) {
    $_SESSION['id'] = $id;
    $_SESSION['login'] = $us_name;
    $_SESSION['admin'] = $admin;
    $_SESSION['age'] = $age;
}

// Регистрация нового пользователя
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['button-reg'])) {
    $us_name = trim($_POST['login']);
    $email   = trim($_POST['email']);
    $age     = $_POST['age'];
    $pass_first = $_POST['pass-first'];
    $pass_second = $_POST['pass-second'];

    if ($pass_first !== $pass_second) {
        echo "Пароли не совпадают.";
    } else {
        $hashed_password = password_hash($pass_first, PASSWORD_DEFAULT);

        $check_email_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email_stmt->bind_param("s", $email);
        $check_email_stmt->execute();
        $check_email_result = $check_email_stmt->get_result();

        if ($check_email_result->num_rows > 0) {
            echo "Пользователь с таким адресом электронной почты уже существует.";
        } else {
            $admin = 0;
            $stmt = $conn->prepare("INSERT INTO users (admin, us_name, email, age, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $admin, $us_name, $email, $age, $hashed_password);

            if ($stmt->execute()) {
                $new_user_id = $conn->insert_id;
                setSession($new_user_id, $us_name, $admin, $age);
                header("Location: socset/profile/accaunt.php");
                exit();
            } else {
                echo "Ошибка при регистрации: " . $conn->error;
            }
            $stmt->close();
        }
        $check_email_stmt->close();
    }
}

// Авторизация пользователя
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['button-log'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, us_name, admin, age, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            setSession($row['id'], $row['us_name'], $row['admin'], $row['age']);
            header("Location: /socset/profile/accaunt.php");
            exit();
        } else {
            echo "Неверный пароль.";
        }
    } else {
        echo "Пользователь с таким адресом электронной почты не найден.";
    }
    $stmt->close();
}

// Редактирование профиля пользователя
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['button-upd'])) {
    // Проверяем, авторизован ли пользователь
    if (!isset($_SESSION['id'])) {
        echo "Ошибка: пользователь не авторизован.";
        exit;
    }

    $id = $_SESSION['id'];
    $us_name = trim($_POST['user-name']);
    $email = trim($_POST['user-email']);
    $age = trim($_POST['user-age']);
    $info = trim($_POST['user-info']);
    $pass_first = $_POST['pass-first'];
    $pass_second = $_POST['pass-second'];

    // Валидация данных
    $errors = [];
    if (empty($us_name)) $errors[] = "ФИО не может быть пустым.";
    if (!is_numeric($age) || $age <= 0) $errors[] = "Возраст должен быть положительным числом.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Некорректный email.";

    // Проверка пароля, если введён
    $update_password = false;
    if (!empty($pass_first) || !empty($pass_second)) {
        if ($pass_first !== $pass_second) {
            $errors[] = "Пароли не совпадают.";
        } elseif (strlen($pass_first) < 6) {
            $errors[] = "Пароль должен содержать минимум 6 символов.";
        } else {
            $update_password = true;
            $hashed_password = password_hash($pass_first, PASSWORD_DEFAULT);
        }
    }

    // Проверка уникальности email (исключая текущего пользователя)
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check_stmt->bind_param("si", $email, $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        if ($check_result->num_rows > 0) {
            $errors[] = "Этот email уже используется другим пользователем.";
        }
        $check_stmt->close();
    }

    // Если есть ошибки, выводим их и прекращаем выполнение
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        exit;
    }

    // Обновляем данные в БД с использованием подготовленного запроса
    if ($update_password) {
        $stmt = $conn->prepare("UPDATE users SET us_name = ?, email = ?, age = ?, info = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssissi", $us_name, $email, $age, $info, $hashed_password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET us_name = ?, email = ?, age = ?, info = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $us_name, $email, $age, $info, $id);
    }

    if ($stmt->execute()) {
        // Обновляем сессию
        $_SESSION['login'] = $us_name;
        $_SESSION['age'] = $age;
        $_SESSION['email'] = $email;
        $_SESSION['info'] = $info;

        // Редирект в личный кабинет
        header("Location: /profile/accaunt.php");
        exit();
    } else {
        echo "Ошибка при обновлении данных: " . $conn->error;
    }
    $stmt->close();
}
?>