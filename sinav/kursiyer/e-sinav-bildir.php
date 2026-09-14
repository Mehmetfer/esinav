<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

header('Content-Type: application/json; charset=utf-8');

start_app_session();
$user = current_user();
if ($user === null || ($user['role'] ?? '') !== 'kursiyer') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Oturum bulunamadi.']);
    exit;
}

$soruId = (int)($_POST['soru_id'] ?? 0);
$oturumId = (int)($_POST['oturum_id'] ?? 0);
$not = trim((string)($_POST['not'] ?? ''));
if ($oturumId <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Gecersiz oturum.']);
    exit;
}

try {
    $pdo = db();
    $chk = $pdo->prepare('SELECT id FROM esinav_oturum WHERE id = ? AND kursiyer_id = ? LIMIT 1');
    $chk->execute([$oturumId, (int)$user['id']]);
    if (!$chk->fetch()) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'Yetkisiz islem.']);
        exit;
    }
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS esinav_soru_bildirim ("
        . "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,"
        . "oturum_id INT NOT NULL,"
        . "soru_id INT NOT NULL DEFAULT 0,"
        . "kursiyer_id INT NOT NULL,"
        . "notu VARCHAR(1000) NOT NULL DEFAULT '',"
        . "durum VARCHAR(20) NOT NULL DEFAULT 'yeni',"
        . "created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,"
        . "KEY idx_oturum (oturum_id),"
        . "KEY idx_kursiyer (kursiyer_id)"
        . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
    $ins = $pdo->prepare(
        'INSERT INTO esinav_soru_bildirim (oturum_id, soru_id, kursiyer_id, notu) VALUES (?, ?, ?, ?)'
    );
    $ins->execute([$oturumId, $soruId, (int)$user['id'], mb_substr($not, 0, 1000)]);
    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Kaydedilemedi.']);
}
