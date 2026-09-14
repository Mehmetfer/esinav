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

// Konulu (mini) sinav destegi: tip=konulu&konu=<slug>
$tip = (string)($_GET['tip'] ?? $_POST['tip'] ?? 'deneme');
$konu = (string)($_GET['konu'] ?? $_POST['konu'] ?? '');
if (!in_array($tip, ['deneme', 'konulu'], true)) {
    $tip = 'deneme';
}
if ($tip === 'konulu') {
    if ($konu === '' || !isset(src_konular()[$konu])) {
        $_SESSION['src_error'] = 'Gecersiz konu secimi.';
        redirect('/kursiyer/src-konu-sinavlari.php');
    }
} else {
    $konu = '';
}

try {
    $pdo = db();
    src_ensure_tables($pdo);
    src_seed_if_needed($pdo);
    $oturumId = src_oturum_baslat($pdo, $kursiyerId, $tip, $konu !== '' ? $konu : null);
    redirect('/kursiyer/src-soru.php?q=1');
} catch (Throwable $e) {
    $_SESSION['src_error'] = $e->getMessage();
    redirect($tip === 'konulu' ? '/kursiyer/src-konu-sinavlari.php' : '/kursiyer/src.php');
}
