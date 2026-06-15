<?php
/** Панель администратора: список всех записей + смена статуса (с причиной отмены). */
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/config/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $requestId = (int)($_POST['request_id'] ?? 0);
    $newStatus = $_POST['status'] ?? '';
    $reason    = trim($_POST['cancel_reason'] ?? '');
    $allowed   = ['in_progress', 'done', 'cancelled'];

    if ($requestId > 0 && in_array($newStatus, $allowed, true)) {
        if ($newStatus === 'cancelled' && $reason === '') {
            set_flash('error', 'Для отмены укажите причину.');
        } else {
            $stmt = $pdo->prepare('UPDATE requests SET status = ?, cancel_reason = ? WHERE id = ?');
            $stmt->execute([$newStatus, $newStatus === 'cancelled' ? $reason : null, $requestId]);
            set_flash('success', 'Статус записи #' . $requestId . ' обновлён.');
        }
    } else {
        set_flash('error', 'Некорректные данные.');
    }
    header('Location: admin.php');
    exit;
}

$requests = $pdo->query(
    'SELECT r.*, c.name AS category_name, u.full_name, u.email
     FROM requests r
     JOIN categories c ON c.id = r.category_id
     JOIN users u      ON u.id = r.user_id
     ORDER BY r.created_at DESC'
)->fetchAll();

$pageTitle = 'Панель администратора';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1>Панель администратора</h1>
    <span class="muted">Всего записей: <?= count($requests) ?></span>
</div>

<?php if (!$requests): ?>
    <div class="empty"><p>Записей пока нет.</p></div>
<?php else: ?>
    <div class="table-wrap">
        <table class="data-table admin-table">
            <thead>
                <tr>
                    <th>№</th><th>Заявитель</th><th>Контакты</th>
                    <th><?= e(cfg('field_category.label')) ?></th>
                    <th>Дата/время</th>
                    <th><?= e(cfg('field_toggle.label')) ?></th>
                    <th>Статус</th><th>Управление</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td>#<?= (int)$r['id'] ?></td>
                    <td>
                        <strong><?= e($r['full_name']) ?></strong><br>
                        <span class="muted small"><?= e($r['address']) ?></span>
                    </td>
                    <td>
                        <?= e($r['phone']) ?><br>
                        <span class="muted small"><?= e($r['email']) ?></span>
                    </td>
                    <td><?= e($r['category_name']) ?></td>
                    <td><?= e(date('d.m.Y', strtotime($r['desired_date']))) ?><br><?= e(substr($r['desired_time'], 0, 5)) ?></td>
                    <td><?= e(toggle_label($r['toggle_value'])) ?></td>
                    <td>
                        <span class="badge badge-<?= e($r['status']) ?>"><?= e(status_label($r['status'])) ?></span>
                        <?php if ($r['status'] === 'cancelled' && $r['cancel_reason']): ?>
                            <div class="muted small">Причина: <?= e($r['cancel_reason']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="post" class="status-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                            <select name="status" class="js-status">
                                <option value="in_progress" <?= $r['status']==='in_progress'?'selected':'' ?>>В работе</option>
                                <option value="done"        <?= $r['status']==='done'?'selected':'' ?>>Выполнено</option>
                                <option value="cancelled"   <?= $r['status']==='cancelled'?'selected':'' ?>>Отменено</option>
                            </select>
                            <input type="text" name="cancel_reason" class="js-reason" placeholder="Причина отмены"
                                   value="<?= e($r['cancel_reason'] ?? '') ?>"
                                   style="<?= $r['status']==='cancelled' ? '' : 'display:none' ?>">
                            <button type="submit" class="btn btn-sm">OK</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
