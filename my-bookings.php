<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_login(); $pageTitle = 'My bookings'; $activePage = 'bookings';
if (is_post()) {
    verify_csrf();
    $bookingId = (int)($_POST['booking_id'] ?? 0);
    $stmt = db()->prepare("UPDATE bookings SET booking_status = 'cancelled' WHERE booking_id = ? AND user_id = ? AND booking_status = 'pending'");
    $stmt->execute([$bookingId, $user['user_id']]);
    flash($stmt->rowCount() ? 'success' : 'error', $stmt->rowCount() ? 'Your pending request was cancelled.' : 'Only your own pending bookings can be cancelled.');
    redirect('my-bookings.php');
}
$stmt = db()->prepare('SELECT b.*, r.room_name, r.room_type, r.image_name FROM bookings b JOIN rooms r ON b.room_id = r.room_id WHERE b.user_id = ? ORDER BY b.booking_date DESC, b.booking_id DESC');
$stmt->execute([$user['user_id']]); $bookings = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="page-banner smaller"><div class="container"><span class="eyebrow">WELCOME BACK, <?= e(strtoupper($user['name'])) ?></span><h1>Your coastal <em>escapes.</em></h1><p>All your reservation requests, in one lovely place.</p></div></div>
<section class="section"><div class="container"><div class="section-heading"><div><span class="eyebrow">YOUR RESERVATIONS</span><h2>My bookings</h2></div><a class="btn btn-primary" href="<?= e(url('rooms.php')) ?>">Plan another stay ↗</a></div>
<?php if (!$bookings): ?><div class="empty-state"><h3>Your story starts here</h3><p>You haven't made any booking requests yet. Discover your favourite room by the sea.</p><a class="btn btn-primary" href="<?= e(url('rooms.php')) ?>">Explore rooms ↗</a></div><?php endif; ?>
<div class="booking-list"><?php foreach ($bookings as $booking): ?><article class="booking-item"><img src="<?= e(room_image($booking)) ?>" data-fallback="<?= e(room_image_fallback($booking)) ?>" loading="lazy" alt="Room photograph"><div class="booking-item-info"><div class="room-type">BOOKING #<?= (int)$booking['booking_id'] ?> · <?= e($booking['room_type']) ?></div><h3><?= e($booking['room_name']) ?></h3><p><?= e($booking['check_in']) ?> → <?= e($booking['check_out']) ?> · <?= nights($booking['check_in'], $booking['check_out']) ?> nights · <?= (int)$booking['guests'] ?> guests</p><strong><?= e(money((float)$booking['total_amount'])) ?></strong></div><div class="booking-item-side"><span class="status status-<?= e($booking['booking_status']) ?>"><?= e(booking_status_label($booking['booking_status'])) ?></span><?php if ($booking['booking_status'] === 'pending'): ?><form method="post" data-confirm="Cancel this pending booking request?"><?= csrf_field() ?><input type="hidden" name="booking_id" value="<?= (int)$booking['booking_id'] ?>"><button class="text-danger" type="submit">Cancel request</button></form><?php endif; ?></div></article><?php endforeach; ?></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
