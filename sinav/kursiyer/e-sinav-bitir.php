<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/esinav.php';
require_once dirname(__DIR__) . '/includes/aktivite.php';

$user = require_role('kursiyer');
$pdo = db();
$oturum = esinav_aktif_oturum($pdo, (int)$user['id']);
if (!$oturum) {
    redirect('/kursiyer/e-sinav.php');
}
$oturumId = (int)$oturum['id'];
$sonuc = esinav_bitir($pdo, $oturumId);
aktivite_log(
    (int)$user['id'],
    'esinav_bitir',
    'E-Sınav tamamlandı',
    'Oturum #' . $oturumId . ' · Doğru: ' . $sonuc['dogru'] . '/50',
    $sonuc['puan'],
    $sonuc['basarili']
);
redirect('/kursiyer/e-sinav-sonuc.php?id=' . $oturumId);
