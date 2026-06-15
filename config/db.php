<?php
/**
 * Подключение к базе данных через PDO.
 * Параметры берутся из config/app.php (секция 'db').
 *
 * Безопасность: PDO + подготовленные выражения исключают SQL-инъекции,
 * режим ошибок — исключения, эмуляция подготовки отключена.
 */

require_once __DIR__ . '/../includes/functions.php';

$dbCfg = cfg('db');

$dsn = "mysql:host={$dbCfg['host']};dbname={$dbCfg['name']};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbCfg['user'], $dbCfg['pass'], $options);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Ошибка подключения к базе данных. Проверьте, что MySQL запущен и схема импортирована.');
}
