<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin(); $pageTitle = 'Manage reservations';
if (is_post()) {
    verify_csrf();
    $id = (int)($_POST['booking_id'] ?? 0);
    $action = (string)($_POST['action'] ?? '');
    if (!in_array($action, ['confirm', 'cancel'], true) || $id < 1) { flash('error', 'Invalid booking action.'); redirect('admin/bookings.php'); }
    $pdo = db();
    try {
        $pdo->beginTransaction();
        // Lock booking first, then associated room. Admin confirmation requests use the room lock to avoid overlaps.
        $stmt = $pdo->prepare('SELECT room_id, check_in, check_out, guests, booking_status FROM bookings WHERE booking_id=? FOR UPDATE');
        $stmt->execute([$id]); $booking = $stmt->fetch();
        if (!$booking) throw new RuntimeException('Booking not found.');
        if ($action === 'cancel' && $booking['booking_status'] !== 'cancelled') {
            $stmt = $pdo->prepare("UPDATE bookings SET booking_status='cancelled' WHERE booking_id=?"); $stmt->execute([$id]);
            $message = 'Booking cancelled.';
        } elseif ($action === 'confirm' && $booking['booking_status'] === 'pending') {
            if (!valid_dates($booking['check_in'], $booking['check_out'])) throw new RuntimeException('Cannot confirm an expired or invalid stay.');
            $lock = $pdo->prepare('SELECT room_id, room_status, max_guests FROM rooms WHERE room_id=? FOR UPDATE');
            $lock->execute([$booking['room_id']]); $room = $lock->fetch();
            if (!$room || $room['room_status'] !== 'active') throw new RuntimeException('This room is inactive. Activate it before confirmation.');
            if ((int)$booking['guests'] > (int)$room['max_guests']) throw new RuntimeException('Booking guest count exceeds the current room capacity.');
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_id=? AND booking_id<>? AND booking_status='confirmed' AND check_in<? AND check_out>?");
            $stmt->execute([$booking['room_id'], $id, $booking['check_out'], $booking['check_in']]);
            if ((int)$stmt->fetchColumn() !== 0) throw new RuntimeException('Cannot confirm: this room already has a confirmed reservation for those dates.');
            $stmt = $pdo->prepare("UPDATE bookings SET booking_status='confirmed' WHERE booking_id=? AND booking_status='pending'");
            $stmt->execute([$id]); $message = 'Booking confirmed.';
        } else throw new RuntimeException('This action is not allowed for the current booking status.');
        $pdo->commit(); flash('success', $message);
    } catch (Throwable $ex) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('error', $ex instanceof RuntimeException ? $ex->getMessage() : 'Unable to update booking right now.');
        if (!($ex instanceof RuntimeException)) error_log($ex->getMessage());
    }
    redirect('admin/bookings.php');
}
$status = (string)($_GET['status'] ?? '');
if ($status && !in_array($status, ['pending', 'confirmed', 'cancelled'], true)) $status = '';
$sql = 'SELECT b.*, u.name, u.email, u.contact_number, r.room_name FROM bookings b JOIN users u ON b.user_id=u.user_id JOIN rooms r ON b.room_id=r.room_id';
if ($status) $sql .= ' WHERE b.booking_status = ?';
$sql .= ' ORDER BY FIELD(b.booking_status,\'pending\',\'confirmed\',\'cancelled\'), b.booking_date DESC, b.booking_id DESC';
$stmt = db()->prepare($sql); $stmt->execute($status ? [$status] : []); $bookings = $stmt->fetchAll();
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="page-banner smaller"><div class="container"><span class="eyebrow">VILLA MANAGEMENT</span><h1>Guest <em>reservations.</em></h1></div></div><section class="section admin-section"><div class="container"><nav class="admin-tabs"><a href="<?= e(url('admin/index.php')) ?>">Overview</a><a href="<?= e(url('admin/rooms.php')) ?>">Manage rooms</a><a class="selected" href="<?= e(url('admin/bookings.php')) ?>">Reservations</a></nav><div class="section-heading"><div><span class="eyebrow">BOOKING REQUESTS</span><h2>Reservation management</h2></div><form method="get"><label class="select-compact">Status <select name="status" onchange="this.form.submit()"><option value="">All bookings</option><?php foreach (['pending','confirmed','cancelled'] as $option): ?><option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option><?php endforeach; ?></select></label></form></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Booking</th><th>Guest</th><th>Room</th><th>Stay</th><th>Guests</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach ($bookings as $booking): ?><tr><td>#<?= (int)$booking['booking_id'] ?></td><td><strong><?= e($booking['guest_name']) ?></strong><small><?= e($booking['guest_email']) ?><br><?= e($booking['guest_contact']) ?></small></td><td><?= e($booking['room_name']) ?></td><td><?= e($booking['check_in']) ?><br><small>to <?= e($booking['check_out']) ?></small></td><td><?= (int)$booking['guests'] ?></td><td><?= e(money((float)$booking['total_amount'])) ?></td><td><span class="status status-<?= e($booking['booking_status']) ?>"><?= e(ucfirst($booking['booking_status'])) ?></span></td><td><div class="actions"><?php if ($booking['booking_status'] === 'pending'): ?><form method="post" data-confirm="Confirm booking #<?= (int)$booking['booking_id'] ?>?"><?= csrf_field() ?><input type="hidden" name="booking_id" value="<?= (int)$booking['booking_id'] ?>"><input type="hidden" name="action" value="confirm"><button class="action-confirm" type="submit">Confirm</button></form><?php endif; ?><?php if ($booking['booking_status'] !== 'cancelled'): ?><form method="post" data-confirm="Cancel booking #<?= (int)$booking['booking_id'] ?>?"><?= csrf_field() ?><input type="hidden" name="booking_id" value="<?= (int)$booking['booking_id'] ?>"><input type="hidden" name="action" value="cancel"><button type="submit">Cancel</button></form><?php endif; ?></div></td></tr><?php endforeach; ?><?php if (!$bookings): ?><tr><td colspan="8">No bookings found.</td></tr><?php endif; ?></tbody></table></div></div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
