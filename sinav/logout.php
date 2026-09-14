<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/aktivite.php';

start_app_session();
$user = current_user();
if ($user && ($user['role'] ?? '') === 'kursiyer') {
    aktivite_log((int)$user['id'], 'cikis', 'Sistemden çıkış yaptı');
}
logout_user();
$ref = (string)($_SERVER['HTTP_REFERER'] ?? '');
$toApp = !empty($_GET['app']) || (strpos($ref, '/mobil/') !== false);
redirect($toApp ? '/mobil/login.php' : '/index.php');


