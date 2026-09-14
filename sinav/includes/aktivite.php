<?php
declare(strict_types=1);

/**
 * Kursiyer aktivite gunlugu — yonetici raporlari icin.
 */

function aktivite_ensure_table(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS kursiyer_aktivite (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          kursiyer_id INT UNSIGNED NOT NULL,
          tip VARCHAR(40) NOT NULL,
          baslik VARCHAR(255) NOT NULL,
          detay TEXT NULL,
          puan INT NULL,
          basarili TINYINT(1) NULL,
          ip VARCHAR(45) NULL,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          KEY idx_akt_kursiyer (kursiyer_id),
          KEY idx_akt_tip (tip),
          KEY idx_akt_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci"
    );
}

/**
 * @param array<string,mixed>|null $extra
 */
function aktivite_log(int $kursiyerId, string $tip, string $baslik, ?string $detay = null, ?int $puan = null, ?bool $basarili = null): void
{
    if ($kursiyerId <= 0) {
        return;
    }
    try {
        $pdo = db();
        aktivite_ensure_table($pdo);
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $pdo->prepare(
            'INSERT INTO kursiyer_aktivite (kursiyer_id, tip, baslik, detay, puan, basarili, ip)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $kursiyerId,
            $tip,
            $baslik,
            $detay,
            $puan,
            $basarili === null ? null : ($basarili ? 1 : 0),
            $ip,
        ]);
    } catch (Throwable) {
        // Aktivite logu kritik degil; sinavi bozmasin
    }
}

function aktivite_tip_etiket(string $tip): string
{
    $map = [
        'giris' => 'Giriş',
        'cikis' => 'Çıkış',
        'ekitap' => 'E-Kitap',
        'animasyon' => 'Animasyon',
        'video' => 'Video Ders',
        'konu_sinav' => 'Konu Sınavı',
        'deneme_sinav' => 'Deneme Sınavı',
        'esinav_basla' => 'E-Sınav Başladı',
        'esinav_bitir' => 'E-Sınav Sonucu',
        'dilek' => 'Dilek / Öneri',
        'sayfa' => 'Sayfa',
        'anasayfa' => 'Ana Sayfa',
    ];
    return $map[$tip] ?? $tip;
}

