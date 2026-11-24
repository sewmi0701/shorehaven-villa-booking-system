<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$editing = $id > 0;
$room = ['room_name'=>'', 'room_type'=>'Deluxe', 'description'=>'', 'price_per_night'=>'', 'max_guests'=>'2', 'facilities'=>'Wi-Fi, Air Conditioning, Private Bathroom', 'room_status'=>'active', 'image_name'=>'ocean'];
if ($editing) {
    $stmt = db()->prepare('SELECT * FROM rooms WHERE room_id = ?'); $stmt->execute([$id]);
    $room = $stmt->fetch();
    if (!$room) { flash('error', 'Room not found.'); redirect('admin/rooms.php'); }
}
$pageTitle = $editing ? 'Edit room' : 'Add a room'; $errors = [];
if (is_post()) {
    verify_csrf();
    foreach (array_keys($room) as $key) if (isset($_POST[$key])) $room[$key] = trim((string)$_POST[$key]);
    $room['price_per_night'] = (float)($room['price_per_night'] ?? 0);
    $room['max_guests'] = (int)($room['max_guests'] ?? 0);
    if (strlen($room['room_name']) < 3 || strlen($room['room_name']) > 120) $errors[] = 'Room name must contain 3–120 characters.';
    if (!in_array($room['room_type'], ['Suite', 'Deluxe', 'Family', 'Standard'], true)) $errors[] = 'Choose a room type.';
    if (strlen($room['description']) < 20 || strlen($room['description']) > 2000) $errors[] = 'Description must contain 20–2000 characters.';
    if ($room['price_per_night'] <= 0 || $room['price_per_night'] > 10000000) $errors[] = 'Enter a valid positive nightly price.';
    if ($room['max_guests'] < 1 || $room['max_guests'] > 20) $errors[] = 'Capacity must be between 1 and 20.';
    if (strlen($room['facilities']) > 500) $errors[] = 'Facilities text is too long.';
    if (!in_array($room['room_status'], ['active', 'inactive'], true)) $errors[] = 'Choose a valid room status.';
    if (!in_array($room['image_name'], ['ocean','palm','garden','sunset','villa'], true)) $errors[] = 'Choose a room image.';
    if (!$errors) {
        $values = [$room['room_name'], $room['room_type'], $room['description'], $room['price_per_night'], $room['max_guests'], $room['facilities'], $room['room_status'], $room['image_name']];
        if ($editing) {
            $stmt = db()->prepare('UPDATE rooms SET room_name=?, room_type=?, description=?, price_per_night=?, max_guests=?, facilities=?, room_status=?, image_name=? WHERE room_id=?');
            $values[] = $id;
        } else $stmt = db()->prepare('INSERT INTO rooms (room_name, room_type, description, price_per_night, max_guests, facilities, room_status, image_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute($values);
        flash('success', $editing ? 'Room updated successfully.' : 'New room created successfully.');
        redirect('admin/rooms.php');
    }
}
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="page-banner smaller"><div class="container"><span class="eyebrow">VILLA MANAGEMENT</span><h1><?= $editing ? 'Edit' : 'Create' ?> <em>a room.</em></h1></div></div><section class="section admin-section"><div class="container form-page"><a class="text-link" href="<?= e(url('admin/rooms.php')) ?>">← Back to all rooms</a><h2><?= $editing ? 'Update accommodation' : 'Add new accommodation' ?></h2><?php display_errors($errors); ?><form class="stack-form admin-form" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>"><label>Room name<input name="room_name" maxlength="120" value="<?= e((string)$room['room_name']) ?>" required></label><div class="two-cols"><label>Room type<select name="room_type"><?php foreach (['Suite','Deluxe','Family','Standard'] as $option): ?><option <?= $room['room_type'] === $option ? 'selected' : '' ?>><?= $option ?></option><?php endforeach; ?></select></label><label>Max guests<input name="max_guests" type="number" min="1" max="20" value="<?= e((string)$room['max_guests']) ?>" required></label></div><label>Description<textarea name="description" rows="5" minlength="20" maxlength="2000" required><?= e((string)$room['description']) ?></textarea></label><label>Facilities (comma-separated)<input name="facilities" maxlength="500" value="<?= e((string)$room['facilities']) ?>"></label><div class="two-cols"><label>Price per night (LKR)<input name="price_per_night" type="number" min="1" max="10000000" step="0.01" value="<?= e((string)$room['price_per_night']) ?>" required></label><label>Room status<select name="room_status"><option value="active" <?= $room['room_status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $room['room_status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></label></div><label>Room photo style<select name="image_name"><?php foreach (['ocean'=>'Ocean view', 'palm'=>'Palm & garden', 'garden'=>'Family / twin beds', 'sunset'=>'Beach-side balcony', 'villa'=>'Villa illustration'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= $room['image_name'] === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label><button class="btn btn-primary" type="submit"><?= $editing ? 'Save changes' : 'Create room' ?> ↗</button></form></div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
