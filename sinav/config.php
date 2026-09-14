<?php
declare(strict_types=1);

$localFile = __DIR__ . '/config.local.php';
if (!is_file($localFile)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "config.local.php bulunamadi. config.local.php.example dosyasini kopyalayip duzenleyin.";
    exit;
}

$config = require $localFile;

if (!is_array($config)) {
    http_response_code(500);
    exit('Gecersiz config.local.php');
}

define('APP_ROOT', __DIR__);
define('SITE_NAME', (string)($config['site_name'] ?? 'METRO e-SINAV'));
define('SITE_URL', rtrim((string)($config['site_url'] ?? ''), '/'));
define('DB_HOST', (string)($config['db_host'] ?? 'localhost'));
define('DB_NAME', (string)($config['db_name'] ?? ''));
define('DB_USER', (string)($config['db_user'] ?? ''));
define('DB_PASS', (string)($config['db_pass'] ?? ''));
define('DB_CHARSET', (string)($config['db_charset'] ?? 'utf8mb4'));
define('SESSION_NAME', (string)($config['session_name'] ?? 'metro_esinav_sess'));

date_default_timezone_set('Europe/Istanbul');

