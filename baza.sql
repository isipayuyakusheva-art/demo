-- база данных портала "Мой Не Сам"

CREATE DATABASE IF NOT EXISTS moynesam CHARACTER SET utf8mb4;
USE moynesam;

-- пользователи
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fio VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(150) NOT NULL,
    role VARCHAR(10) NOT NULL DEFAULT 'user'
);

-- виды услуг
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL
);

INSERT INTO services (name) VALUES
('Общий клининг'),
('Генеральная уборка'),
('Послестроительная уборка'),
('Химчистка ковров и мебели');

-- заявки
CREATE TABLE requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    service_id INT NOT NULL,
    req_date DATE NOT NULL,
    req_time TIME NOT NULL,
    payment VARCHAR(10) NOT NULL,
    status VARCHAR(10) NOT NULL DEFAULT 'new',
    cancel_reason VARCHAR(255) DEFAULT NULL
);

-- администратор (логин: adminka, пароль: password)
INSERT INTO users (login, password, fio, phone, email, role) VALUES
('adminka', '$2y$12$Bvk0M1CyYwlHrMaXGbSFe.rCXJdXflReenUMRvI7lvuD6VJXKXNrG',
'Администратор', '+7(900)-000-00-00', 'admin@mail.ru', 'admin');
