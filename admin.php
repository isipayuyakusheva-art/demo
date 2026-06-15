<?php
require 'db.php';
require 'functions.php';

// доступ только администратору
if (!isAdmin()) { header('Location: login.php'); exit; }

// смена статуса заявки
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $reason = trim($_POST['cancel_reason']);

    // при отмене обязательна причина
    if ($status == 'cancel' && $reason == '') {
        // просто не обновляем, причина не указана
    } else {
        $stmt = $pdo->prepare('UPDATE requests SET status = ?, cancel_reason = ? WHERE id = ?');
        $stmt->execute([$status, $status == 'cancel' ? $reason : null, $id]);
    }
    header('Location: admin.php');
    exit;
}

// все заявки
$requests = $pdo->query('SELECT requests.*, services.name AS service, users.fio, users.email
    FROM requests
    JOIN services ON services.id = requests.service_id
    JOIN users ON users.id = requests.user_id
    ORDER BY requests.id DESC')->fetchAll();

$title = 'Панель администратора';
include 'header.php';
?>
<h1>Панель администратора</h1>
<table>
    <tr>
        <th>№</th>
        <th>Заявитель</th>
        <th>Телефон</th>
        <th>Услуга</th>
        <th>Дата/время</th>
        <th>Оплата</th>
        <th>Статус</th>
        <th>Управление</th>
    </tr>
    <?php foreach ($requests as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= h($r['fio']) ?><br><small><?= h($r['address']) ?></small></td>
            <td><?= h($r['phone']) ?></td>
            <td><?= h($r['service']) ?></td>
            <td><?= date('d.m.Y', strtotime($r['req_date'])) ?> <?= substr($r['req_time'], 0, 5) ?></td>
            <td><?= paymentName($r['payment']) ?></td>
            <td>
                <?= statusName($r['status']) ?>
                <?php if ($r['status'] == 'cancel' && $r['cancel_reason']): ?>
                    <br><small>Причина: <?= h($r['cancel_reason']) ?></small>
                <?php endif; ?>
            </td>
            <td>
                <form method="post" class="status-form">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <select name="status">
                        <option value="work" <?= $r['status'] == 'work' ? 'selected' : '' ?>>В работе</option>
                        <option value="done" <?= $r['status'] == 'done' ? 'selected' : '' ?>>Выполнено</option>
                        <option value="cancel" <?= $r['status'] == 'cancel' ? 'selected' : '' ?>>Отменено</option>
                    </select>
                    <input type="text" name="cancel_reason" placeholder="Причина отмены" value="<?= h($r['cancel_reason']) ?>">
                    <button type="submit">OK</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php include 'footer.php'; ?>
