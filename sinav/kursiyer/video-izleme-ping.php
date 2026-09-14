<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/video_izleme.php';

header('Content-Type: application/json; charset=utf-8');

$user = current_user();
if ($user === null || ($user['role'] ?? '') !== 'kursiyer') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'auth']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false]);
    exit;
}

$youtubeId = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)($_POST['youtube_id'] ?? '')) ?? '';
$baslik = trim((string)($_POST['baslik'] ?? ''));
$sure = (int)($_POST['sure_sn'] ?? 0);
$yeni = !empty($_POST['yeni_oturum']);

if (strlen($youtubeId) < 6 || $sure < 1) {
    echo json_encode(['ok' => false, 'error' => 'param']);
    exit;
}

try {
    video_izleme_ekle(db(), (int)$user['id'], $youtubeId, $baslik, $sure, $yeni);
    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'db']);
}
