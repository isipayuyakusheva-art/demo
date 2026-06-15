<?php
/** Регистрация: уникальный логин, серверная валидация, хэширование пароля. */
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/config/db.php';

if (is_logged_in()) { header('Location: dashboard.php'); exit; }

$errors = [];
$old = ['login' => '', 'full_name' => '', 'phone' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $old['login']     = trim($_POST['login'] ?? '');
    $old['full_name'] = trim($_POST['full_name'] ?? '');
    $old['phone']     = trim($_POST['phone'] ?? '');
    $old['email']     = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $password2        = $_POST['password2'] ?? '';

    if (!v_login($old['login']))     $errors['login']     = 'Логин: 3–50 символов (латиница, цифры, _).';
    if (!v_name($old['full_name']))  $errors['full_name'] = 'ФИО: только кириллица и пробелы.';
    if (!v_phone($old['phone']))     $errors['phone']     = 'Телефон в формате +7(XXX)-XXX-XX-XX.';
    if (!v_email($old['email']))     $errors['email']     = 'Некорректный адрес электронной почты.';
    if (!v_password($password))      $errors['password']  = 'Пароль не короче 6 символов.';
    if ($password !== $password2)    $errors['password2'] = 'Пароли не совпадают.';

    // Уникальность логина
    if (empty($errors['login'])) {
        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE login = ?');
        $stmt->execute([$old['login']]);
        if ($stmt->fetch()) $errors['login'] = 'Такой логин уже занят.';
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO users (login, password_hash, full_name, phone, email, role)
             VALUES (?, ?, ?, ?, ?, "user")'
        );
        $stmt->execute([$old['login'], $hash, $old['full_name'], $old['phone'], $old['email']]);
        set_flash('success', 'Регистрация прошла успешно. Теперь войдите.');
        header('Location: login.php');
        exit;
    }
}

$pageTitle = 'Регистрация';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-card">
    <h1>Регистрация</h1>
    <p class="muted">Создайте аккаунт для доступа к порталу.</p>
    <form method="post" novalidate>
        <?= csrf_field() ?>
        <label>Логин
            <input type="text" name="login" value="<?= e($old['login']) ?>" required>
            <?php if (isset($errors['login'])): ?><span class="err"><?= e($errors['login']) ?></span><?php endif; ?>
        </label>
        <label>ФИО
            <input type="text" name="full_name" value="<?= e($old['full_name']) ?>" placeholder="Иванов Иван Иванович" required>
            <?php if (isset($errors['full_name'])): ?><span class="err"><?= e($errors['full_name']) ?></span><?php endif; ?>
        </label>
        <label>Телефон
            <input type="text" name="phone" value="<?= e($old['phone']) ?>" placeholder="+7(900)-123-45-67" data-phone required>
            <?php if (isset($errors['phone'])): ?><span class="err"><?= e($errors['phone']) ?></span><?php endif; ?>
        </label>
        <label>E-mail
            <input type="email" name="email" value="<?= e($old['email']) ?>" placeholder="mail@example.ru" required>
            <?php if (isset($errors['email'])): ?><span class="err"><?= e($errors['email']) ?></span><?php endif; ?>
        </label>
        <label>Пароль
            <input type="password" name="password" required>
            <?php if (isset($errors['password'])): ?><span class="err"><?= e($errors['password']) ?></span><?php endif; ?>
        </label>
        <label>Повтор пароля
            <input type="password" name="password2" required>
            <?php if (isset($errors['password2'])): ?><span class="err"><?= e($errors['password2']) ?></span><?php endif; ?>
        </label>
        <button type="submit" class="btn btn-block">Зарегистрироваться</button>
    </form>
    <p class="muted center">Уже есть аккаунт? <a href="login.php">Войти</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
