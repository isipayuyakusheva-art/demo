<?php
/** Личный кабинет: история записей пользователя + кнопка новой. */
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/config/db.php';
require_login();
if (is_admin()) { header('Location: admin.php'); exit; }

$user = current_user();

$stmt = $pdo->prepare(
    'SELECT r.*, c.name AS category_name
     FROM requests r
     JOIN categories c ON c.id = r.category_id
     WHERE r.user_id = ?
     ORDER BY r.created_at DESC'
);
$stmt->execute([$user['id']]);
$requests = $stmt->fetchAll();

$pageTitle = cfg('entity.many');
require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1><?= e(cfg('entity.many')) ?></h1>
    <a href="create.php" class="btn">+ <?= e(cfg('entity.create')) ?></a>
</div>

<?php if (!$requests): ?>
    <div class="empty">
        <p>У вас пока нет записей.</p>
        <a href="create.php" class="btn"><?= e(cfg('entity.create')) ?></a>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>№</th>
                    <th><?= e(cfg('field_category.label')) ?></th>
                    <th><?= e(cfg('field_address.label')) ?></th>
                    <th>Дата и время</th>
                    <th><?= e(cfg('field_toggle.label')) ?></th>
                    <th>Статус</th>
                    <th>Комментарий</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td>#<?= (int)$r['id'] ?></td>
                    <td><?= e($r['category_name']) ?></td>
                    <td><?= e($r['address']) ?></td>
                    <td><?= e(date('d.m.Y', strtotime($r['desired_date']))) ?>, <?= e(substr($r['desired_time'], 0, 5)) ?></td>
                    <td><?= e(toggle_label($r['toggle_value'])) ?></td>
                    <td><span class="badge badge-<?= e($r['status']) ?>"><?= e(status_label($r['status'])) ?></span></td>
                    <td><?= $r['cancel_reason'] ? e($r['cancel_reason']) : '—' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
