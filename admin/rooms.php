<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin(); $pageTitle = 'Manage rooms';
if (is_post()) {
    verify_csrf();
    $id = (int)($_POST['room_id'] ?? 0);
    $action = (string)($_POST['action'] ?? '');
    if ($id && in_array($action, ['deactivate', 'activate'], true)) {
        $next = $action === 'activate' ? 'active' : 'inactive';
        $stmt = db()->prepare('UPDATE rooms SET room_status = ? WHERE room_id = ?');
        $stmt->execute([$next, $id]);
        flash('success', 'Room status updated.');
    } else flash('error', 'Invalid room action.');
    redirect('admin/rooms.php');
}
$rooms = db()->query('SELECT * FROM rooms ORDER BY room_id DESC')->fetchAll();
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="page-banner smaller"><div class="container"><span class="eyebrow">VILLA MANAGEMENT</span><h1>Room <em>collection.</em></h1></div></div><section class="section admin-section"><div class="container"><nav class="admin-tabs"><a href="<?= e(url('admin/index.php')) ?>">Overview</a><a class="selected" href="<?= e(url('admin/rooms.php')) ?>">Manage rooms</a><a href="<?= e(url('admin/bookings.php')) ?>">Reservations</a></nav><div class="section-heading"><div><span class="eyebrow">ACCOMMODATION</span><h2>All rooms</h2></div><a class="btn btn-primary" href="<?= e(url('admin/room-form.php')) ?>">+ Add a room</a></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Room</th><th>Type</th><th>Guests</th><th>Price/night</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach ($rooms as $room): ?><tr><td><div class="table-room"><img src="<?= e(room_image($room)) ?>" data-fallback="<?= e(room_image_fallback($room)) ?>" loading="lazy" alt=""><strong><?= e($room['room_name']) ?></strong></div></td><td><?= e($room['room_type']) ?></td><td><?= (int)$room['max_guests'] ?></td><td><?= e(money((float)$room['price_per_night'])) ?></td><td><span class="status status-<?= e($room['room_status']) ?>"><?= e(ucfirst($room['room_status'])) ?></span></td><td><div class="actions"><a href="<?= e(url('admin/room-form.php?id=' . $room['room_id'])) ?>">Edit</a><form method="post" data-confirm="<?= $room['room_status'] === 'active' ? 'Deactivate this room? Existing bookings will be preserved.' : 'Reactivate this room?' ?>"><?= csrf_field() ?><input type="hidden" name="room_id" value="<?= (int)$room['room_id'] ?>"><input type="hidden" name="action" value="<?= $room['room_status'] === 'active' ? 'deactivate' : 'activate' ?>"><button type="submit"><?= $room['room_status'] === 'active' ? 'Deactivate' : 'Activate' ?></button></form></div></td></tr><?php endforeach; ?></tbody></table></div><p class="hint">Rooms are deactivated instead of permanently deleted so earlier bookings remain in your records.</p></div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
