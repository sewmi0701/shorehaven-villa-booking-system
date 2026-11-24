<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$in = trim((string)($_GET['check_in'] ?? ''));
$out = trim((string)($_GET['check_out'] ?? ''));
$guests = (int)($_GET['guests'] ?? 1);
if (!valid_dates($in, $out) || $guests < 1 || $guests > 20) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Enter valid dates and guest count.']);
    exit;
}
$rooms = available_rooms($in, $out, $guests);
echo json_encode(['ok' => true, 'room_ids' => array_map(static fn($room) => (int)$room['room_id'], $rooms), 'available_count' => count($rooms), 'nights' => nights($in, $out)]);
