<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_NAME,
        DB_CHARSET
    );

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // MySQL oturum saat dilimini PHP ile ayni yap (Europe/Istanbul = +03:00)
    // NOW() UTC donuyor; sure hesaplari PHP (Europe/Istanbul) ile yapiliyor.
    try {
        $tzOffset = (new DateTime('now'))->format('P');
        $pdo->exec("SET time_zone = '" . $tzOffset . "'");
    } catch (Throwable $e) {
        // yoksay
    }


    return $pdo;
}

