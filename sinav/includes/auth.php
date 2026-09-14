<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';
require_once __DIR__ . '/db.php';

function start_app_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name(SESSION_NAME);
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        header('Location: ' . $path);
        exit;
    }
    $base = SITE_URL !== '' ? SITE_URL : '';
    header('Location: ' . $base . $path);
    exit;
}

function current_user(): ?array
{
    start_app_session();
    if (empty($_SESSION['user']) || !is_array($_SESSION['user'])) {
        return null;
    }
    return $_SESSION['user'];
}

function require_role(string $role): array
{
    $user = current_user();
    if ($user === null || ($user['role'] ?? '') !== $role) {
        redirect('/index.php');
    }
    return $user;
}

function logout_user(): void
{
    start_app_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool)$p['secure'], (bool)$p['httponly']);
    }
    session_destroy();
}

/** Telefon (GSM) numarasini sadece rakama cevirir. */
function normalize_gsm(string $gsm): string
{
    return preg_replace('/\D+/', '', $gsm) ?? '';
}

/** Gecerli telefon: en az 10, en fazla 15 rakam. */
function is_valid_gsm(string $gsm): bool
{
    $gsm = normalize_gsm($gsm);
    return preg_match('/^[0-9]{10,15}$/', $gsm) === 1;
}


/**
 * Giris mantigi:
 *  - Yonetici (superadmin/admin) numarasi girildi ise SIFRE zorunludur.
 *  - Kursiyer numarasi girildi ise sifre sorulmaz, direkt giris yapilir.
 * @return array{ok:bool,error?:string,user?:array}
 */
function attempt_login(string $gsm, string $password): array
{
    $gsm = normalize_gsm($gsm);
    if (!is_valid_gsm($gsm)) {
        return ['ok' => false, 'error' => 'Gecerli bir telefon numarasi giriniz.'];
    }

    $pdo = db();

    // 1) Once yonetici kontrol: sifre zorunlu
    $stmt = $pdo->prepare('SELECT id, telefon, sifre_hash, ad_soyad, aktif FROM yoneticiler WHERE telefon = ? LIMIT 1');
    $stmt->execute([$gsm]);
    $admin = $stmt->fetch();
    if ($admin) {
        if ((int)$admin['aktif'] !== 1) {
            return ['ok' => false, 'error' => 'Hesabiniz aktif degil.'];
        }
        if (!password_verify($password, (string)$admin['sifre_hash'])) {
            return ['ok' => false, 'error' => 'Yonetici sifresi hatali.'];
        }
        return [
            'ok' => true,
            'user' => [
                'id' => (int)$admin['id'],
                'gsm' => $admin['telefon'],
                'name' => $admin['ad_soyad'],
                'role' => 'admin',
            ],
        ];
    }

    // 2) Kursiyer kontrol: sifre gerekmez, direkt giris
    $stmt = $pdo->prepare('SELECT id, telefon, ad, soyad, ehliyet_sinifi, aktif FROM kursiyerler WHERE telefon = ? LIMIT 1');
    $stmt->execute([$gsm]);
    $student = $stmt->fetch();
    if ($student && (int)$student['aktif'] === 1) {
        return [
            'ok' => true,
            'user' => [
                'id' => (int)$student['id'],
                'gsm' => $student['telefon'],
                'name' => trim($student['ad'] . ' ' . $student['soyad']),
                'ehliyet_sinifi' => (string)($student['ehliyet_sinifi'] ?? 'B'),
                'role' => 'kursiyer',
            ],
        ];
    }

    // 3) Yeni kullanici: sisteme kayitli degilse otomatik kursiyer olarak olustur ve giris yaptir.
    $ph = password_hash('$2y$10$otomatik-kayit-gecersiz', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO kursiyerler (telefon, sifre_hash, ad, soyad, aktif)
         VALUES (?, ?, ?, ?, 1)'
    );
    $stmt->execute([$gsm, $ph, 'Yeni', 'Kullanici']);
    $stmt = $pdo->prepare('SELECT id FROM kursiyerler WHERE telefon = ? LIMIT 1');
    $stmt->execute([$gsm]);
    $newRow = $stmt->fetch();
    $newId = (int)($newRow['id'] ?? 0);
    return [
        'ok' => true,
        'user' => [
            'id' => $newId,
            'gsm' => $gsm,
            'name' => 'Yeni Kullanici',
            'ehliyet_sinifi' => 'B',
            'role' => 'kursiyer',
        ],
    ];
}

function login_user(array $user): void
{
    start_app_session();
    session_regenerate_id(true);
    $_SESSION['user'] = $user;

    require_once __DIR__ . '/dil.php';
    dil_login_ayarla((string)($user['gsm'] ?? ''));

    if (($user['role'] ?? '') === 'kursiyer') {
        require_once __DIR__ . '/aktivite.php';
        aktivite_log(
            (int)$user['id'],
            'giris',
            'Sisteme giriş yaptı',
            'GSM: ' . (string)($user['gsm'] ?? '')
        );
    }
}

