<?php
/** Авторизация: проверка по хэшу, сообщения об ошибке ввода (Модуль №2). */
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/config/db.php';

if (is_logged_in()) {
    header('Location: ' . (is_admin() ? 'admin.php' : 'dashboard.php'));
    exit;
}

$error = '';
$loginValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $loginValue = trim($_POST['login'] ?? '');
    $password   = $_POST['password'] ?? '';

    if ($loginValue === '' || $password === '') {
        $error = 'Введите логин и пароль.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE login = ?');
        $stmt->execute([$loginValue]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            login_user($user);
            header('Location: ' . ($user['role'] === 'admin' ? 'admin.php' : 'dashboard.php'));
            exit;
        }
        $error = 'Неверный логин или пароль.';
    }
}

$pageTitle = 'Вход';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-card">
    <h1>Вход в систему</h1>
    <p class="muted">Авторизуйтесь, чтобы продолжить.</p>
    <?php if ($error): ?><div class="flash flash-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" novalidate>
        <?= csrf_field() ?>
        <label>Логин
            <input type="text" name="login" value="<?= e($loginValue) ?>" required autofocus>
        </label>
        <label>Пароль
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="btn btn-block">Войти</button>
    </form>
    <p class="muted center">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    <p class="hint">Администратор: <code><?= e(cfg('admin.login')) ?></code> / <code><?= e(cfg('admin.password')) ?></code></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
