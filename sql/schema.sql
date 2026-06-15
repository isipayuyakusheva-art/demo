-- ============================================================
--  УНИВЕРСАЛЬНАЯ СХЕМА БД для демоэкзамена 09.02.07 (ДЭ БУ)
--  Модули №1 + №2 объединены.
-- ============================================================
--  Импорт:
--    XAMPP → phpMyAdmin → Импорт → выбрать этот файл
--    или:   mysql -u root < schema.sql
--
--  Под новую тему меняется ТОЛЬКО:
--    1) имя БД (если нужно) — см. ниже и config/app.php;
--    2) список вариантов в таблице `categories` (INSERT в конце).
--  Структуру таблиц менять не требуется.
-- ============================================================

DROP DATABASE IF EXISTS universal_db;
CREATE DATABASE universal_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE universal_db;

-- ------------------------------------------------------------
--  Пользователи (заказчики + администратор)
-- ------------------------------------------------------------
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    login         VARCHAR(50)  NOT NULL UNIQUE,   -- логин уникален (Модуль №2)
    password_hash VARCHAR(255) NOT NULL,          -- bcrypt-хэш пароля
    full_name     VARCHAR(150) NOT NULL,          -- ФИО
    phone         VARCHAR(30)  NOT NULL,
    email         VARCHAR(150) NOT NULL,
    role          ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Справочник КАТЕГОРИЙ (это и есть «список услуг/товаров/...»)
--  ⭐ Под новую тему меняем только строки INSERT ниже.
-- ------------------------------------------------------------
CREATE TABLE categories (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Записи / заявки (главная сущность)
--  Поля универсальны для тем «портал услуг/заказов/броней».
-- ------------------------------------------------------------
CREATE TABLE requests (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT NOT NULL,
    address       VARCHAR(255) NOT NULL,                 -- адрес/локация
    phone         VARCHAR(30)  NOT NULL,                 -- контактный телефон
    category_id   INT NOT NULL,                          -- выбранная категория
    toggle_value  VARCHAR(20)  NOT NULL,                 -- бинарный выбор (оплата и т.п.)
    desired_date  DATE NOT NULL,
    desired_time  TIME NOT NULL,
    status        ENUM('new','in_progress','done','cancelled') NOT NULL DEFAULT 'new',
    cancel_reason VARCHAR(255) DEFAULT NULL,             -- причина отмены
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_req_user FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    CONSTRAINT fk_req_cat  FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

-- ============================================================
--  СИДЫ (стартовые данные)
-- ============================================================

-- Администратор: логин adminka / пароль password
-- Демо-пользователь: логин user / пароль user1234
INSERT INTO users (login, password_hash, full_name, phone, email, role) VALUES
    ('adminka', '$2y$12$Bvk0M1CyYwlHrMaXGbSFe.rCXJdXflReenUMRvI7lvuD6VJXKXNrG',
     'Администратор Портала', '+7(900)-000-00-00', 'admin@example.ru', 'admin'),
    ('user',    '$2y$12$caW4yRIcPg3sjUEEpWKVuOaZ5kzKEruBsASeqMwCeb1cpK1KRLFH.',
     'Иванов Иван Иванович',  '+7(901)-111-11-11', 'ivanov@example.ru', 'user');

-- ⭐ СПИСОК КАТЕГОРИЙ ТЕМЫ — меняйте под своё задание ⭐
--   Кафе:      'Пицца', 'Суши', 'Десерты', 'Напитки'
--   Отель:     'Стандарт', 'Люкс', 'Семейный', 'Апартаменты'
--   Автосервис:'Замена масла', 'Шиномонтаж', 'Диагностика', 'Кузовной ремонт'
INSERT INTO categories (name) VALUES
    ('Общий клининг'),
    ('Генеральная уборка'),
    ('Послестроительная уборка'),
    ('Химчистка ковров и мебели');
