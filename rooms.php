<?php
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Our rooms'; $activePage = 'rooms';
$in = trim((string)($_GET['check_in'] ?? ''));
$out = trim((string)($_GET['check_out'] ?? ''));
$guests = max(1, min(20, (int)($_GET['guests'] ?? 1)));
$type = trim((string)($_GET['type'] ?? ''));
$maxPrice = max(0, (float)($_GET['max_price'] ?? 0));
$errors = [];
if (($in !== '' || $out !== '') && !valid_dates($in, $out)) $errors[] = 'Choose a valid check-in and check-out date (up to 60 nights, from today).';
if ($type !== '' && !in_array($type, ['Suite', 'Deluxe', 'Family', 'Standard'], true)) $type = '';
$rooms = $errors ? [] : available_rooms($in, $out, $guests, $type, $maxPrice);
require __DIR__ . '/includes/header.php';
?>
<div class="page-banner"><div class="container"><span class="eyebrow">FIND YOUR PLACE IN PARADISE</span><h1>Rooms with <em>a view.</em></h1><p>Simple comforts, thoughtful details and a slower kind of luxury.</p></div></div>
<section class="section"><div class="container">
  <form class="filter-panel" method="get" action="<?= e(url('rooms.php')) ?>">
    <label>Check in<input name="check_in" type="date" value="<?= e($in) ?>" min="<?= date('Y-m-d') ?>"></label>
    <label>Check out<input name="check_out" type="date" value="<?= e($out) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>"></label>
    <label>Guests<select name="guests"><?php for ($i = 1; $i <= 6; $i++): ?><option value="<?= $i ?>" <?= $guests === $i ? 'selected' : '' ?>><?= $i ?> guest<?= $i > 1 ? 's' : '' ?></option><?php endfor; ?></select></label>
    <label>Room type<select name="type"><option value="">All types</option><?php foreach (['Suite', 'Deluxe', 'Family', 'Standard'] as $choice): ?><option value="<?= $choice ?>" <?= $type === $choice ? 'selected' : '' ?>><?= $choice ?></option><?php endforeach; ?></select></label>
    <label>Max / night<input type="number" min="0" step="1000" name="max_price" placeholder="Any price" value="<?= $maxPrice > 0 ? e((string)$maxPrice) : '' ?>"></label>
    <button class="btn btn-primary" type="submit">Find rooms ↗</button>
  </form>
  <?php display_errors($errors); ?>
  <div class="results-heading"><div><span class="eyebrow">THE COLLECTION</span><h2><?= count($rooms) ?> <?= count($rooms) === 1 ? 'room' : 'rooms' ?> <?= $in ? 'available' : 'to explore' ?></h2></div><?php if ($in && !$errors): ?><p><?= e($in) ?> — <?= e($out) ?> · <?= $guests ?> guest(s)</p><?php endif; ?></div>
  <?php if (!$rooms): ?><div class="empty-state"><h3>No rooms found</h3><p>Try different dates, guest numbers or room filters.</p><a class="btn btn-outline" href="<?= e(url('rooms.php')) ?>">Clear filters</a></div><?php endif; ?>
  <div class="room-grid large-grid">
  <?php foreach ($rooms as $room): ?>
    <article class="room-card"><a class="room-image" href="<?= e(url('room.php?id=' . $room['room_id'] . ($in ? '&check_in=' . urlencode($in) . '&check_out=' . urlencode($out) . '&guests=' . $guests : ''))) ?>"><img src="<?= e(room_image($room)) ?>" data-fallback="<?= e(room_image_fallback($room)) ?>" loading="lazy" alt="Room photograph: <?= e($room['room_name']) ?>"><span class="image-arrow">↗</span></a>
      <div class="room-card-content"><div class="room-type"><?= e($room['room_type']) ?> · UP TO <?= (int)$room['max_guests'] ?> GUESTS</div><h3><?= e($room['room_name']) ?></h3><p><?= e(substr($room['description'], 0, 112)) ?>...</p><div class="room-tags"><?php foreach (array_slice(array_map('trim', explode(',', $room['facilities'])), 0, 3) as $tag): ?><span><?= e($tag) ?></span><?php endforeach; ?></div><div class="room-meta"><span>From <strong><?= e(money((float)$room['price_per_night'])) ?></strong> / night</span><a href="<?= e(url('room.php?id=' . $room['room_id'] . ($in ? '&check_in=' . urlencode($in) . '&check_out=' . urlencode($out) . '&guests=' . $guests : ''))) ?>">View room ↗</a></div></div>
    </article>
  <?php endforeach; ?>
  </div>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
