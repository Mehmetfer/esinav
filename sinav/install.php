<?php
declare(strict_types=1);

/**
 * Tek seferlik kurulum: tablolari olusturur, varsayilan hesaplari yazar.
 * Kurulumdan sonra bu dosyayi silin veya rename edin.
 *
 * Tarayici: http://esinav.mehmetfer.com.tr/install.php?key=metro-kurulum-2026
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$expectedKey = 'metro-kurulum-2026';
$key = (string)($_GET['key'] ?? '');
if (!hash_equals($expectedKey, $key)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Yetkisiz. Ornek: install.php?key=metro-kurulum-2026";
    exit;
}

header('Content-Type: text/html; charset=utf-8');

$superAdminTel = '5417453663';
$superAdminPass = '1453';
$adminTel = '5072048626';
$adminPass = '1453';

try {
    $pdo = db();
    $sql = file_get_contents(__DIR__ . '/sql/schema.sql');
    if ($sql === false) {
        throw new RuntimeException('schema.sql okunamadi');
    }

    // PDO multi-query: ifadeleri ayir
    $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql) ?: []));
    foreach ($statements as $statement) {
        if ($statement === '' || str_starts_with($statement, '--')) {
            continue;
        }
        // Yorum satirlarini temizle
        $clean = preg_replace('/^--.*$/m', '', $statement) ?? $statement;
        $clean = trim($clean);
        if ($clean === '') {
            continue;
        }
        $pdo->exec($clean);
    }

    $superHash = password_hash($superAdminPass, PASSWORD_DEFAULT);
    $adminHash = password_hash($adminPass, PASSWORD_DEFAULT);

    $pdo->prepare(
        'INSERT INTO yoneticiler (telefon, sifre_hash, ad_soyad, aktif)
         VALUES (?, ?, ?, 1)
         ON DUPLICATE KEY UPDATE sifre_hash = VALUES(sifre_hash), ad_soyad = VALUES(ad_soyad), aktif = 1'
    )->execute([$superAdminTel, $superHash, 'Süper Admin']);

    $pdo->prepare(
        'INSERT INTO yoneticiler (telefon, sifre_hash, ad_soyad, aktif)
         VALUES (?, ?, ?, 1)
         ON DUPLICATE KEY UPDATE sifre_hash = VALUES(sifre_hash), ad_soyad = VALUES(ad_soyad), aktif = 1'
    )->execute([$adminTel, $adminHash, 'Admin']);

    echo '<h1>Kurulum tamam</h1>';
    echo '<p>Veritabani: <strong>' . htmlspecialchars(DB_NAME) . '</strong></p>';
    echo '<ul>';
    echo '<li>Superadmin GSM: <code>' . htmlspecialchars($superAdminTel) . '</code> / sifre: <code>' . htmlspecialchars($superAdminPass) . '</code></li>';
    echo '<li>Admin GSM: <code>' . htmlspecialchars($adminTel) . '</code> / sifre: <code>' . htmlspecialchars($adminPass) . '</code></li>';
    echo '</ul>';
    echo '<p><a href="/index.php">Giris sayfasina git</a></p>';
    echo '<p style="color:#b00"><strong>Guvenlik:</strong> install.php dosyasini sunucudan silin.</p>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Kurulum hatasi</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p>config.local.php icindeki DB bilgilerini kontrol edin.</p>';
}

