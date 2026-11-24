<?php
require __DIR__ . '/includes/bootstrap.php';
$id = (int)($_POST['room_id'] ?? $_GET['room_id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM rooms WHERE room_id = ? AND room_status = 'active'");
$stmt->execute([$id]); $room = $stmt->fetch();
if (!$room) { flash('error', 'This room is no longer available.'); redirect('rooms.php'); }
$in = trim((string)($_POST['check_in'] ?? $_GET['check_in'] ?? ''));
$out = trim((string)($_POST['check_out'] ?? $_GET['check_out'] ?? ''));
$guests = (int)($_POST['guests'] ?? $_GET['guests'] ?? 2);
if (!current_user()) {
    $_SESSION['after_login'] = 'book.php?' . http_build_query(['room_id' => $id, 'check_in' => $in, 'check_out' => $out, 'guests' => $guests]);
    flash('info', 'Log in or register to request a booking.'); redirect('login.php');
}
$pageTitle = 'Complete your booking'; $errors = [];
$guestName = trim((string)($_POST['guest_name'] ?? current_user()['name']));
$guestEmail = strtolower(trim((string)($_POST['guest_email'] ?? current_user()['email'])));
$guestContact = trim((string)($_POST['guest_contact'] ?? current_user()['contact_number']));
if (strlen($guestName) < 2 || strlen($guestName) > 100) $errors[] = 'Enter a guest name between 2 and 100 characters.';
if (!filter_var($guestEmail, FILTER_VALIDATE_EMAIL) || strlen($guestEmail) > 190) $errors[] = 'Enter a valid guest email address.';
if (!preg_match('/^[+0-9() .-]{7,24}$/', $guestContact)) $errors[] = 'Enter a valid guest phone number.';
if (!valid_dates($in, $out)) $errors[] = 'Please select future check-in and check-out dates (maximum 60 nights).';
if ($guests < 1 || $guests > (int)$room['max_guests']) $errors[] = 'This room accommodates 1–' . $room['max_guests'] . ' guests.';
if (is_post()) {
    verify_csrf();
    if (!$errors) {
        try {
            $pdo = db(); $pdo->beginTransaction();
            // Lock the room row to serialize booking/confirmation decisions.
            $lock = $pdo->prepare('SELECT room_id, price_per_night, max_guests, room_status FROM rooms WHERE room_id = ? FOR UPDATE');
            $lock->execute([$id]); $lockedRoom = $lock->fetch();
            if (!$lockedRoom || $lockedRoom['room_status'] !== 'active') throw new RuntimeException('This room is no longer available.');
            if ($guests > (int)$lockedRoom['max_guests']) throw new RuntimeException('The room capacity has changed.');
            $conflict = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = ? AND booking_status = 'confirmed' AND check_in < ? AND check_out > ?");
            $conflict->execute([$id, $out, $in]);
            if ((int)$conflict->fetchColumn() > 0) throw new RuntimeException('This room has already been confirmed for the chosen dates. Choose other dates.');
            $total = nights($in, $out) * (float)$lockedRoom['price_per_night'];
            $save = $pdo->prepare("INSERT INTO bookings (user_id, room_id, guest_name, guest_email, guest_contact, check_in, check_out, guests, total_amount, booking_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $save->execute([current_user()['user_id'], $id, $guestName, $guestEmail, $guestContact, $in, $out, $guests, $total]);
            $pdo->commit();
            flash('success', 'Booking request sent! Your reservation is pending confirmation.');
            redirect('my-bookings.php');
        } catch (Throwable $ex) {
            if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
            if ($ex instanceof RuntimeException) $errors[] = $ex->getMessage();
            else { error_log($ex->getMessage()); $errors[] = 'We could not place the booking. Please retry.'; }
        }
    }
}
$calculatedTotal = (!$errors) ? nights($in, $out) * (float)$room['price_per_night'] : 0;
require __DIR__ . '/includes/header.php';
?>
<div class="page-banner smaller"><div class="container"><span class="eyebrow">ONE STEP CLOSER TO THE SEA</span><h1>Request your <em>stay.</em></h1></div></div>
<div class="container section booking-layout"><div><span class="eyebrow">YOUR RESERVATION</span><h2>Almost <em>there.</em></h2><p>Review the details below. We’ll confirm your request as soon as possible.</p><?php display_errors($errors); ?><form class="stack-form booking-form" method="post" data-night-price="<?= e((string)$room['price_per_night']) ?>"><?= csrf_field() ?><input type="hidden" name="room_id" value="<?= $id ?>"><div class="two-cols"><label>Check-in<input type="date" name="check_in" min="<?= date('Y-m-d') ?>" value="<?= e($in) ?>" required></label><label>Check-out<input type="date" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= e($out) ?>" required></label></div><div class="two-cols"><label>Guest full name<input type="text" name="guest_name" maxlength="100" value="<?= e($guestName) ?>" required></label><label>Guest contact number<input type="tel" name="guest_contact" maxlength="24" value="<?= e($guestContact) ?>" required></label></div><label>Guest email address<input type="email" name="guest_email" maxlength="190" value="<?= e($guestEmail) ?>" required></label><label>Number of guests<select name="guests"><?php for ($n=1;$n<=(int)$room['max_guests'];$n++): ?><option value="<?= $n ?>" <?= $guests===$n ? 'selected' : '' ?>><?= $n ?> <?= $n===1?'guest':'guests' ?></option><?php endfor; ?></select></label><div class="booking-notice">Your reservation is linked securely to your logged-in account. You can enter your preferred guest contact details above.</div><div class="estimate" aria-live="polite"><?= $calculatedTotal ? e(money($calculatedTotal)) . ' estimated total' : 'Select valid dates for your estimated total' ?></div><button type="submit" class="btn btn-primary btn-wide">Send booking request ↗</button><small class="muted">You will not be charged now. Requests are pending until approved by the villa administrator.</small></form></div>
<aside class="summary-card"><img src="<?= e(room_image($room)) ?>" data-fallback="<?= e(room_image_fallback($room)) ?>" loading="lazy" alt="Sample room photograph of <?= e($room['room_name']) ?>"><div class="summary-body"><span class="eyebrow">ROOM SUMMARY</span><h3><?= e($room['room_name']) ?></h3><div class="summary-line"><span>Rate per night</span><strong><?= e(money((float)$room['price_per_night'])) ?></strong></div><?php if ($calculatedTotal): ?><div class="summary-line"><span><?= nights($in, $out) ?> night(s)</span><strong><?= e(money($calculatedTotal)) ?></strong></div><?php endif; ?><p>Flexible booking requests · Admin confirmation required</p></div></aside></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
