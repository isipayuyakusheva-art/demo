<?php
require 'db.php';
require 'functions.php';

if (isLogged()) { header('Location: cabinet.php'); exit; }

$errors = [];
$login = $fio = $phone = $email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $fio = trim($_POST['fio']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    // все поля обязательны
    if ($login == '' || $password == '' || $fio == '' || $phone == '' || $email == '') {
        $errors[] = 'Заполните все поля';
    }
    if ($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный адрес электронной почты';
    }

    // логин должен быть уникальным
    if ($login != '') {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE login = ?');
        $stmt->execute([$login]);
        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким логином уже существует';
        }
    }

    if (!$errors) {
        // пароль храним в виде хэша
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (login, password, fio, phone, email) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$login, $hash, $fio, $phone, $email]);
        header('Location: login.php');
        exit;
    }
}

$title = 'Регистрация';
include 'header.php';
?>
<div class="form-box">
    <h1>Регистрация</h1>
    <?php foreach ($errors as $e): ?>
        <p class="error"><?= h($e) ?></p>
    <?php endforeach; ?>
    <form method="post">
        <label>Логин</label>
        <input type="text" name="login" value="<?= h($login) ?>">
        <label>Пароль</label>
        <input type="password" name="password">
        <label>ФИО</label>
        <input type="text" name="fio" value="<?= h($fio) ?>">
        <label>Телефон</label>
        <input type="text" name="phone" value="<?= h($phone) ?>">
        <label>E-mail</label>
        <input type="text" name="email" value="<?= h($email) ?>">
        <button type="submit">Зарегистрироваться</button>
    </form>
    <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
</div>
<?php include 'footer.php'; ?>
