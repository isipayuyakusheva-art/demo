<?php if (!isset($title)) $title = 'Мой Не Сам'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="container nav">
        <a href="<?= isAdmin() ? 'admin.php' : 'cabinet.php' ?>" class="logo">Мой Не Сам</a>
        <nav>
            <?php if (isLogged()): ?>
                <?php if (!isAdmin()): ?>
                    <a href="cabinet.php">Мои заявки</a>
                    <a href="create.php">Новая заявка</a>
                <?php endif; ?>
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <a href="login.php">Вход</a>
                <a href="register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
