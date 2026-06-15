<?php
require 'db.php';
require 'functions.php';

if (isLogged()) { header('Location: cabinet.php'); exit; }

$error = '';
$login = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    if ($login == '' || $password == '') {
        $error = 'Введите логин и пароль';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE login = ?');
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        // проверяем пароль по хэшу
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fio'] = $user['fio'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] == 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: cabinet.php');
            }
            exit;
        } else {
            $error = 'Неверный логин или пароль';
        }
    }
}

$title = 'Вход';
include 'header.php';
?>
<div class="form-box">
    <h1>Вход в систему</h1>
    <?php if ($error): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Логин</label>
        <input type="text" name="login" value="<?= h($login) ?>">
        <label>Пароль</label>
        <input type="password" name="password">
        <button type="submit">Войти</button>
    </form>
    <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
</div>
<?php include 'footer.php'; ?>
