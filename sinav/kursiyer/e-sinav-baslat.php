<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/esinav.php';
require_once dirname(__DIR__) . '/includes/aktivite.php';

$user = require_role('kursiyer');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/kursiyer/e-sinav.php');
}

try {
    $oturumId = esinav_baslat(db(), (int)$user['id']);
    aktivite_log((int)$user['id'], 'esinav_basla', 'E-Sınav başlattı', 'Oturum #' . $oturumId);
    start_app_session();
    $_SESSION['esinav_oturum_id'] = $oturumId;
    redirect('/kursiyer/e-sinav-soru.php?q=1');
} catch (Throwable $e) {
    start_app_session();
    $_SESSION['esinav_error'] = $e->getMessage();
    redirect('/kursiyer/e-sinav.php');
}

