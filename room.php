<?php
require __DIR__ . '/includes/bootstrap.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$stmt = db()->prepare("SELECT * FROM rooms WHERE room_id = ? AND room_status = 'active'");
$stmt->execute([$id]); $room = $stmt->fetch();
if (!$room) { http_response_code(404); $pageTitle = 'Room not found'; require __DIR__ . '/includes/header.php'; echo '<div class="container section"><h1>Room not found</h1><p><a href="' . e(url('rooms.php')) . '">Browse available rooms</a></p></div>'; require __DIR__ . '/includes/footer.php'; exit; }
$pageTitle = $room['room_name']; $activePage = 'rooms';
$in = trim((string)($_GET['check_in'] ?? '')); $out = trim((string)($_GET['check_out'] ?? ''));
$guests = max(1, min(20, (int)($_GET['guests'] ?? 2)));
require __DIR__ . '/includes/header.php';
?>
<div class="container room-detail"><div class="crumb"><a href="<?= e(url('rooms.php')) ?>">Our rooms</a><span>/</span><?= e($room['room_name']) ?></div><div class="detail-heading"><div><span class="eyebrow"><?= e(strtoupper($room['room_type'])) ?> · MIRISSA</span><h1><?= e($room['room_name']) ?></h1></div><span class="price-big">From <strong><?= e(money((float)$room['price_per_night'])) ?></strong> / night</span></div>
<div class="detail-visual"><img src="<?= e(room_image($room)) ?>" data-fallback="<?= e(room_image_fallback($room)) ?>" loading="lazy" alt="Sample room photograph for <?= e($room['room_name']) ?>"></div>
<div class="detail-grid"><div><span class="eyebrow">A SPACE TO FEEL AT HOME</span><h2>Stay for the <em>feeling.</em></h2><p class="lead"><?= e($room['description']) ?></p><div class="detail-facts"><div><strong>♙ Sleeps up to <?= (int)$room['max_guests'] ?></strong><small>Guests</small></div><div><strong>☾ Coastal comfort</strong><small>Private retreat</small></div><div><strong>✺ Thoughtful touches</strong><small>All essentials included</small></div></div><h3>Room amenities</h3><div class="amenity-grid"><?php foreach (array_filter(array_map('trim', explode(',', $room['facilities']))) as $facility): ?><div>✳ <?= e($facility) ?></div><?php endforeach; ?></div></div>
<aside class="booking-widget" data-night-price="<?= e((string)$room['price_per_night']) ?>"><span class="eyebrow">YOUR NEXT ESCAPE</span><h3>Reserve this room</h3><p class="muted">Choose your dates to see your estimated total.</p><form method="get" action="<?= e(url('book.php')) ?>" class="stack-form"><input type="hidden" name="room_id" value="<?= (int)$room['room_id'] ?>"><label>Check-in<input type="date" name="check_in" min="<?= date('Y-m-d') ?>" value="<?= e($in) ?>" required></label><label>Check-out<input type="date" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= e($out) ?>" required></label><label>Guests<select name="guests"><?php for ($n = 1; $n <= (int)$room['max_guests']; $n++): ?><option value="<?= $n ?>" <?= $n === $guests ? 'selected' : '' ?>><?= $n ?> <?= $n === 1 ? 'guest' : 'guests' ?></option><?php endfor; ?></select></label><div class="estimate" aria-live="polite">Select your dates for an estimate</div><button type="submit" class="btn btn-primary btn-wide">Continue to booking ↗</button><small class="muted">No online payment required · Booking subject to confirmation</small></form></aside>
</div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
