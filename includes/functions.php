<?php
declare(strict_types=1);
function url(string $path = ''): string {
    return BASE_PATH . '/' . ltrim($path, '/');
}
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function money(float $amount): string {
    return 'LKR ' . number_format($amount, 2);
}
function redirect(string $path): void {
    header('Location: ' . url($path), true, 303);
    exit;
}
function flash(string $type, string $message): void {
    $_SESSION['flashes'][] = ['type' => $type, 'message' => $message];
}
function take_flashes(): array {
    $messages = $_SESSION['flashes'] ?? [];
    unset($_SESSION['flashes']);
    return $messages;
}
function csrf_token(): string {
    if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}
function verify_csrf(): void {
    if (!isset($_POST['csrf_token']) || !is_string($_POST['csrf_token']) || !hash_equals(csrf_token(), $_POST['csrf_token'])) {
        http_response_code(419);
        exit('Security verification failed. Go back, refresh the page and retry.');
    }
}
function is_post(): bool { return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'; }
function current_user(): ?array {
    static $cached = false;
    if ($cached !== false) return $cached;
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if (!$userId) return $cached = null;
    $stmt = db()->prepare('SELECT user_id, name, email, contact_number, role FROM users WHERE user_id = ? LIMIT 1');
    $stmt->execute([$userId]);
    return $cached = ($stmt->fetch() ?: null);
}
function require_login(): array {
    $user = current_user();
    if (!$user) { flash('info', 'Please log in to continue.'); redirect('login.php'); }
    return $user;
}
function require_admin(): array {
    $user = require_login();
    if ($user['role'] !== 'admin') { http_response_code(403); exit('Admin access only.'); }
    return $user;
}
function valid_dates(string $in, string $out): bool {
    $checkIn = DateTimeImmutable::createFromFormat('!Y-m-d', $in);
    $checkOut = DateTimeImmutable::createFromFormat('!Y-m-d', $out);
    return $checkIn && $checkOut && $checkIn->format('Y-m-d') === $in && $checkOut->format('Y-m-d') === $out
        && $checkIn >= new DateTimeImmutable('today') && $checkOut > $checkIn
        && $checkOut <= $checkIn->modify('+60 days');
}
function nights(string $in, string $out): int {
    return (new DateTimeImmutable($in))->diff(new DateTimeImmutable($out))->days;
}
function available_rooms(string $in = '', string $out = '', int $guests = 1, string $type = '', float $maxPrice = 0): array {
    $conditions = ["r.room_status = 'active'", 'r.max_guests >= :guests'];
    $params = ['guests' => $guests];
    if ($type !== '') { $conditions[] = 'r.room_type = :type'; $params['type'] = $type; }
    if ($maxPrice > 0) { $conditions[] = 'r.price_per_night <= :max_price'; $params['max_price'] = $maxPrice; }
    if (valid_dates($in, $out)) {
        $conditions[] = "NOT EXISTS (SELECT 1 FROM bookings b WHERE b.room_id = r.room_id AND b.booking_status = 'confirmed' AND b.check_in < :check_out AND b.check_out > :check_in)";
        $params['check_in'] = $in;
        $params['check_out'] = $out;
    }
    $stmt = db()->prepare('SELECT r.* FROM rooms r WHERE ' . implode(' AND ', $conditions) . ' ORDER BY r.price_per_night, r.room_id');
    $stmt->execute($params);
    return $stmt->fetchAll();
}
/**
 * Display genuine stock photography (Unsplash) for the fictional demo rooms.
 * If the optional local image installer was run, prefer local files so the
 * images load without internet. The local SVG art remains the error fallback.
 * These are illustrative stock photos, NOT verified Shorehaven Villa photos.
 */
function room_photo_key(array $room): string {
    $name = trim((string)($room['room_name'] ?? ''));
    $style = (string)($room['image_name'] ?? 'ocean');
    // Seed room 5 and the user-created sixth room both default to "ocean"
    // in MySQL. Give them their own approved photographs without changing DB.
    // If an admin later selects a different image style, respect that choice.
    if ($style === 'ocean' && strcasecmp($name, 'Coastal Comfort Room') === 0) return 'standard';
    if ($style === 'ocean' && strcasecmp($name, 'Ocean View Deluxe') === 0) return 'deluxe';
    return in_array($style, ['ocean', 'palm', 'garden', 'sunset', 'villa'], true) ? $style : 'ocean';
}
function room_image_fallback(array $room): string {
    $key = room_photo_key($room);
    if ($key === 'standard' || $key === 'deluxe') $key = 'ocean';
    return url('assets/images/' . $key . '.svg');
}
function room_image(array $room): string {
    $key = room_photo_key($room);
    if ($key === 'villa') return url('assets/images/villa.svg');
    $filename = in_array($key, ['palm', 'garden', 'standard', 'deluxe'], true) ? $key . '-approved.jpg' : $key . '.jpg';
    $localFile = dirname(__DIR__) . '/assets/images/photos/' . $filename;
    if (is_file($localFile)) return url('assets/images/photos/' . $filename);
    $stockPhotos = [
        'ocean' => 'https://images.unsplash.com/photo-1718942899965-4fc10607d805?auto=format&fit=crop&w=1400&q=82',
        'palm' => 'https://images.unsplash.com/photo-1762117360890-5eacdbb07b04?auto=format&fit=crop&w=1400&q=82',
        'garden' => 'https://images.unsplash.com/photo-1763914767111-b4451913ceba?auto=format&fit=crop&w=1400&q=82',
        'sunset' => 'https://images.unsplash.com/photo-1721369483526-62f48a00b949?auto=format&fit=crop&w=1400&q=82',
        'standard' => 'https://images.unsplash.com/photo-1777543391649-9c635a4dfcf6?auto=format&fit=crop&w=1400&q=82',
        'deluxe' => 'https://images.unsplash.com/photo-1690314754158-7287f2022d9d?auto=format&fit=crop&w=1400&q=82',
    ];
    return $stockPhotos[$key] ?? $stockPhotos['ocean'];
}
function booking_status_label(string $status): string {
    return ucfirst($status);
}
function display_errors(array $errors): void {
    if ($errors) {
        echo '<div class="alert alert-error" role="alert"><strong>Please correct:</strong><ul>';
        foreach ($errors as $error) echo '<li>' . e($error) . '</li>';
        echo '</ul></div>';
    }
}
