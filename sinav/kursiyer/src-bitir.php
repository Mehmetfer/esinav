<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

start_app_session();
$user = current_user();
if (!$user) {
    redirect('/login.php');
}

$kursiyerId = (int)($user['id'] ?? 0);

try {
    $pdo = db();
    src_ensure_tables($pdo);
    $oturum = src_aktif_oturum($pdo, $kursiyerId, null);
    $tip = (string)($oturum['tip'] ?? 'deneme');
    if ($oturum) {
        src_bitir($pdo, (int)$oturum['id']);
    }
    // Konulu (mini) sinavdan sonra konu listesine, denemeden sonra SRC ana sayfasina don.
    redirect($tip === 'konulu' ? '/kursiyer/src-konu-sinavlari.php' : '/kursiyer/src.php');
} catch (Throwable $e) {
    $_SESSION['src_error'] = $e->getMessage();
    redirect('/kursiyer/src.php');
}
