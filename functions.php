<?php
session_start();

// экранирование вывода (защита от XSS)
function h($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

// проверка авторизации
function isLogged() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLogged() && $_SESSION['role'] === 'admin';
}

// название статуса для вывода
function statusName($status) {
    if ($status == 'new') return 'Новая заявка';
    if ($status == 'work') return 'В работе';
    if ($status == 'done') return 'Выполнено';
    if ($status == 'cancel') return 'Отменено';
    return $status;
}

// тип оплаты
function paymentName($p) {
    return $p == 'cash' ? 'Наличные' : 'Банковская карта';
}
