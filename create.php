<?php
require 'db.php';
require 'functions.php';

if (!isLogged()) { header('Location: login.php'); exit; }
if (isAdmin()) { header('Location: admin.php'); exit; }

// список услуг
$services = $pdo->query('SELECT * FROM services')->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $service_id = $_POST['service_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $payment = $_POST['payment'] ?? '';

    // все поля обязательны
    if ($address == '' || $phone == '' || $service_id == '' || $date == '' || $time == '' || $payment == '') {
        $errors[] = 'Заполните все поля';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO requests (user_id, address, phone, service_id, req_date, req_time, payment, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, "new")');
        $stmt->execute([$_SESSION['user_id'], $address, $phone, $service_id, $date, $time, $payment]);
        header('Location: cabinet.php');
        exit;
    }
}

$title = 'Новая заявка';
include 'header.php';
?>
<div class="form-box">
    <h1>Формирование заявки</h1>
    <?php foreach ($errors as $e): ?>
        <p class="error"><?= h($e) ?></p>
    <?php endforeach; ?>
    <form method="post">
        <label>Адрес</label>
        <input type="text" name="address">
        <label>Контактный телефон</label>
        <input type="text" name="phone">
        <label>Вид услуги</label>
        <select name="service_id">
            <option value="">— выберите —</option>
            <?php foreach ($services as $s): ?>
                <option value="<?= $s['id'] ?>"><?= h($s['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Желаемая дата</label>
        <input type="date" name="date">
        <label>Желаемое время</label>
        <input type="time" name="time">
        <label>Тип оплаты</label>
        <div>
            <label class="radio"><input type="radio" name="payment" value="cash"> Наличные</label>
            <label class="radio"><input type="radio" name="payment" value="card"> Банковская карта</label>
        </div>
        <button type="submit">Создать заявку</button>
    </form>
</div>
<?php include 'footer.php'; ?>
