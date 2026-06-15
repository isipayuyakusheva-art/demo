<?php
require 'db.php';
require 'functions.php';

// доступ только авторизованным
if (!isLogged()) { header('Location: login.php'); exit; }
if (isAdmin()) { header('Location: admin.php'); exit; }

// заявки текущего пользователя
$stmt = $pdo->prepare('SELECT requests.*, services.name AS service FROM requests
    JOIN services ON services.id = requests.service_id
    WHERE user_id = ? ORDER BY requests.id DESC');
$stmt->execute([$_SESSION['user_id']]);
$requests = $stmt->fetchAll();

$title = 'Мои заявки';
include 'header.php';
?>
<div class="head">
    <h1>Мои заявки</h1>
    <a href="create.php" class="btn">Оставить новую заявку</a>
</div>

<?php if (!$requests): ?>
    <p>У вас пока нет заявок.</p>
<?php else: ?>
    <table>
        <tr>
            <th>№</th>
            <th>Услуга</th>
            <th>Адрес</th>
            <th>Дата и время</th>
            <th>Оплата</th>
            <th>Статус</th>
        </tr>
        <?php foreach ($requests as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= h($r['service']) ?></td>
                <td><?= h($r['address']) ?></td>
                <td><?= date('d.m.Y', strtotime($r['req_date'])) ?> <?= substr($r['req_time'], 0, 5) ?></td>
                <td><?= paymentName($r['payment']) ?></td>
                <td>
                    <?= statusName($r['status']) ?>
                    <?php if ($r['status'] == 'cancel' && $r['cancel_reason']): ?>
                        <br><small>Причина: <?= h($r['cancel_reason']) ?></small>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<?php include 'footer.php'; ?>
