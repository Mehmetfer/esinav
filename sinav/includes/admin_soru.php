<?php
declare(strict_types=1);

/**
 * Yönetici soru editörü yardımcıları.
 */

require_once __DIR__ . '/esinav.php';

/** @return array<string,string> */
function admin_soru_ders_secenekleri(): array
{
    return esinav_ders_adlari();
}

/** @return array<string,string> */
function admin_soru_kaynak_secenekleri(): array
{
    return [
        '' => '—',
        'doc' => 'DÖÇ (çıkmış)',
        'meb' => 'MEB / havuz',
        'manuel' => 'Manuel',
    ];
}

/**
 * Soru görseli yükle. Dönüş: relative path (doc/... veya upload/...) veya null.
 */
function admin_soru_gorsel_yukle(?array $file): ?string
{
    if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Görsel yüklenemedi (kod: ' . (int)$file['error'] . ').');
    }
    $tmp = (string)($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        throw new RuntimeException('Geçersiz yükleme.');
    }
    $size = (int)($file['size'] ?? 0);
    if ($size <= 0 || $size > 5 * 1024 * 1024) {
        throw new RuntimeException('Görsel en fazla 5 MB olabilir.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmp);
    $map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    if (!isset($map[$mime])) {
        throw new RuntimeException('Sadece JPG, PNG, WEBP veya GIF yükleyin.');
    }
    $dir = dirname(__DIR__) . '/assets/img/sorular/upload';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Upload klasörü oluşturulamadı.');
    }
    $name = 'q_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $map[$mime];
    $dest = $dir . '/' . $name;
    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('Dosya kaydedilemedi.');
    }
    return 'upload/' . $name;
}

function admin_soru_gorsel_url(?string $rel): ?string
{
    $rel = trim((string)$rel);
    if ($rel === '') {
        return null;
    }
    return '/assets/img/sorular/' . ltrim(str_replace('\\', '/', $rel), '/');
}

function admin_soru_gorsel_sil(?string $rel): void
{
    $rel = trim((string)$rel);
    if ($rel === '' || !str_starts_with($rel, 'upload/')) {
        return; // doc/ görsellerini silme
    }
    $fs = dirname(__DIR__) . '/assets/img/sorular/' . ltrim(str_replace('\\', '/', $rel), '/');
    if (is_file($fs)) {
        @unlink($fs);
    }
}

/**
 * @param array<string,mixed> $in
 * @return array<string,mixed>
 */
function admin_soru_normalize_post(array $in): array
{
    $ders = (string)($in['ders'] ?? 'trafik');
    $adlar = admin_soru_ders_secenekleri();
    if (!isset($adlar[$ders])) {
        $ders = 'trafik';
    }
    $dogru = strtoupper(substr((string)($in['dogru'] ?? 'A'), 0, 1));
    if (!in_array($dogru, ['A', 'B', 'C', 'D'], true)) {
        $dogru = 'A';
    }
    $kaynak = trim((string)($in['kaynak'] ?? ''));
    $kaynaklar = admin_soru_kaynak_secenekleri();
    if (!isset($kaynaklar[$kaynak])) {
        $kaynak = 'manuel';
    }
    if ($kaynak === '') {
        $kaynak = 'manuel';
    }

    return [
        'ders' => $ders,
        'soru' => trim((string)($in['soru'] ?? '')),
        'secenek_a' => trim((string)($in['secenek_a'] ?? '')),
        'secenek_b' => trim((string)($in['secenek_b'] ?? '')),
        'secenek_c' => trim((string)($in['secenek_c'] ?? '')),
        'secenek_d' => trim((string)($in['secenek_d'] ?? '')),
        'dogru' => $dogru,
        'aktif' => isset($in['aktif']) ? 1 : 0,
        'soru_ar' => trim((string)($in['soru_ar'] ?? '')),
        'secenek_a_ar' => trim((string)($in['secenek_a_ar'] ?? '')),
        'secenek_b_ar' => trim((string)($in['secenek_b_ar'] ?? '')),
        'secenek_c_ar' => trim((string)($in['secenek_c_ar'] ?? '')),
        'secenek_d_ar' => trim((string)($in['secenek_d_ar'] ?? '')),
        'kaynak' => $kaynak,
        'gorsel_kaldir' => !empty($in['gorsel_kaldir']),
    ];
}

/**
 * @param array<string,mixed> $data
 */
function admin_soru_validate(array $data): ?string
{
    if ($data['soru'] === '') {
        return 'Soru metni zorunludur.';
    }
    foreach (['secenek_a', 'secenek_b', 'secenek_c', 'secenek_d'] as $k) {
        if ($data[$k] === '') {
            return 'Tüm şıklar (A–D) doldurulmalıdır.';
        }
    }
    return null;
}

