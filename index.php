<?php
/** Маршрутизация по состоянию авторизации. */
require __DIR__ . '/includes/functions.php';

if (is_admin())          header('Location: admin.php');
elseif (is_logged_in())  header('Location: dashboard.php');
else                     header('Location: login.php');
exit;
