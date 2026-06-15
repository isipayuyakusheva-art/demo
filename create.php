<?php
/** Форма создания записи. Все поля обязательны (Модуль №2). Подписи — из конфига. */
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/config/db.php';
require_login();
if (is_admin()) { header('Location: admin.php'); exit; }

$user = current_user();
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$toggleOptions = cfg('field_toggle.options');

$errors = [];
$old = ['address' => '', 'phone' => '', 'category_id' => '', 'desired_date' => '', 'desired_time' => '', 'toggle_value' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $old['address']      = trim($_POST['address'] ?? '');
    $old['phone']        = trim($_POST['phone'] ?? '');
    $old['category_id']  = $_POST['category_id'] ?? '';
    $old['desired_date'] = $_POST['desired_date'] ?? '';
    $old['desired_time'] = $_POST['desired_time'] ?? '';
    $old['toggle_value'] = $_POST['toggle_value'] ?? '';

    if (cfg('field_address.enabled') && $old['address'] === '')      $errors['address']      = 'Заполните поле.';
    if (!v_phone($old['phone']))                                     $errors['phone']        = 'Телефон в формате +7(XXX)-XXX-XX-XX.';
    if (!ctype_digit((string)$old['category_id']))                  $errors['category_id']  = 'Выберите вариант из списка.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $old['desired_date']))  $errors['desired_date'] = 'Укажите дату.';
    if (!preg_match('/^\d{2}:\d{2}$/', $old['desired_time']))        $errors['desired_time'] = 'Укажите время.';
    if (!array_key_exists($old['toggle_value'], $toggleOptions))     $errors['toggle_value'] = 'Сделайте выбор.';

    if (empty($errors['category_id'])) {
        $check = $pdo->prepare('SELECT 1 FROM categories WHERE id = ?');
        $check->execute([(int)$old['category_id']]);
        if (!$check->fetch()) $errors['category_id'] = 'Выбранный вариант недоступен.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO requests (user_id, address, phone, category_id, toggle_value, desired_date, desired_time, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, "new")'
        );
        $stmt->execute([
            $user['id'], $old['address'], $old['phone'], (int)$old['category_id'],
            $old['toggle_value'], $old['desired_date'], $old['desired_time'],
        ]);
        set_flash('success', 'Запись успешно создана!');
        header('Location: dashboard.php');
        exit;
    }
}

$pageTitle = cfg('entity.title');
require __DIR__ . '/includes/header.php';
?>
<div class="form-card">
    <h1><?= e(cfg('entity.title')) ?></h1>
    <p class="muted">Все поля обязательны для заполнения.</p>
    <form method="post" novalidate>
        <?= csrf_field() ?>

        <?php if (cfg('field_address.enabled')): ?>
        <label><?= e(cfg('field_address.label')) ?>
            <input type="text" name="address" value="<?= e($old['address']) ?>" placeholder="<?= e(cfg('field_address.placeholder')) ?>" required>
            <?php if (isset($errors['address'])): ?><span class="err"><?= e($errors['address']) ?></span><?php endif; ?>
        </label>
        <?php endif; ?>

        <label>Контактный телефон
            <input type="text" name="phone" value="<?= e($old['phone']) ?>" placeholder="+7(900)-123-45-67" data-phone required>
            <?php if (isset($errors['phone'])): ?><span class="err"><?= e($errors['phone']) ?></span><?php endif; ?>
        </label>

        <label><?= e(cfg('field_category.label')) ?>
            <select name="category_id" required>
                <option value="">— выберите —</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ((string)$c['id'] === (string)$old['category_id']) ? 'selected' : '' ?>>
                        <?= e($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category_id'])): ?><span class="err"><?= e($errors['category_id']) ?></span><?php endif; ?>
        </label>

        <div class="row">
            <?php if (cfg('field_date.enabled')): ?>
            <label><?= e(cfg('field_date.label')) ?>
                <input type="date" name="desired_date" value="<?= e($old['desired_date']) ?>" required>
                <?php if (isset($errors['desired_date'])): ?><span class="err"><?= e($errors['desired_date']) ?></span><?php endif; ?>
            </label>
            <?php endif; ?>
            <?php if (cfg('field_time.enabled')): ?>
            <label><?= e(cfg('field_time.label')) ?>
                <input type="time" name="desired_time" value="<?= e($old['desired_time']) ?>" required>
                <?php if (isset($errors['desired_time'])): ?><span class="err"><?= e($errors['desired_time']) ?></span><?php endif; ?>
            </label>
            <?php endif; ?>
        </div>

        <fieldset class="radio-group">
            <legend><?= e(cfg('field_toggle.label')) ?></legend>
            <?php foreach ($toggleOptions as $val => $label): ?>
                <label class="radio">
                    <input type="radio" name="toggle_value" value="<?= e($val) ?>" <?= $old['toggle_value'] === $val ? 'checked' : '' ?>>
                    <?= e($label) ?>
                </label>
            <?php endforeach; ?>
            <?php if (isset($errors['toggle_value'])): ?><span class="err"><?= e($errors['toggle_value']) ?></span><?php endif; ?>
        </fieldset>

        <div class="actions">
            <a href="dashboard.php" class="btn btn-ghost">Отмена</a>
            <button type="submit" class="btn">Создать</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
