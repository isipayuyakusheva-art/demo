<?php
/** Шапка. Перед подключением должен быть подключён functions.php. */
$pageTitle = $pageTitle ?? cfg('app_name');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= e(cfg('app_name')) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Акцентный цвет берётся из config/app.php -->
    <style>
        :root{
            --accent: <?= e(cfg('theme.accent')) ?>;
            --accent-dark: <?= e(cfg('theme.accent_dark')) ?>;
        }
    </style>
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= is_admin() ? 'admin.php' : 'dashboard.php' ?>" class="logo">
            <span class="logo-mark"><?= e(cfg('app_short')) ?></span>
            <span class="logo-text"><?= e(cfg('app_name')) ?></span>
        </a>
        <nav class="nav">
            <?php if (is_logged_in()): ?>
                <?php if (is_admin()): ?>
                    <a href="admin.php">Заявки</a>
                <?php else: ?>
                    <a href="dashboard.php"><?= e(cfg('entity.many')) ?></a>
                    <a href="create.php" class="btn btn-sm"><?= e(cfg('entity.create')) ?></a>
                <?php endif; ?>
                <span class="nav-user"><?= e(current_user()['full_name']) ?></span>
                <a href="logout.php" class="nav-logout">Выйти</a>
            <?php else: ?>
                <a href="login.php">Вход</a>
                <a href="register.php" class="btn btn-sm">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container main">
    <?php foreach (get_flashes() as $f): ?>
        <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
