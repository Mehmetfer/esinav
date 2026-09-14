<?php
declare(strict_types=1);

/**
 * Video izleme süreleri (kursiyer → yönetici raporu).
 */

function video_izleme_ensure(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS video_izleme (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          kursiyer_id INT UNSIGNED NOT NULL,
          youtube_id VARCHAR(20) NOT NULL,
          baslik VARCHAR(255) NOT NULL DEFAULT '',
          sure_sn INT UNSIGNED NOT NULL DEFAULT 0,
          oturum_sayisi INT UNSIGNED NOT NULL DEFAULT 0,
          son_izleme DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          UNIQUE KEY uq_kursiyer_video (kursiyer_id, youtube_id),
          KEY idx_video_son (son_izleme),
          KEY idx_video_sure (sure_sn)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci"
    );
}

function video_izleme_ekle(PDO $pdo, int $kursiyerId, string $youtubeId, string $baslik, int $sureSn, bool $yeniOturum = false): void
{
    if ($kursiyerId <= 0 || $youtubeId === '' || $sureSn < 0) {
        return;
    }
    $sureSn = min(3600, $sureSn); // tek ping max 1 saat
    video_izleme_ensure($pdo);
    if (function_exists('mb_substr')) {
        $baslik = mb_substr(trim($baslik), 0, 250, 'UTF-8');
    } else {
        $baslik = substr(trim($baslik), 0, 250);
    }
    $incOturum = $yeniOturum ? 1 : 0;
    $pdo->prepare(
        'INSERT INTO video_izleme (kursiyer_id, youtube_id, baslik, sure_sn, oturum_sayisi, son_izleme)
         VALUES (?, ?, ?, ?, ?, NOW())
         ON DUPLICATE KEY UPDATE
           baslik = IF(VALUES(baslik) <> \'\', VALUES(baslik), baslik),
           sure_sn = sure_sn + VALUES(sure_sn),
           oturum_sayisi = oturum_sayisi + VALUES(oturum_sayisi),
           son_izleme = NOW()'
    )->execute([$kursiyerId, $youtubeId, $baslik, $sureSn, $incOturum]);
}

function video_izleme_format(int $sn): string
{
    $sn = max(0, $sn);
    $h = intdiv($sn, 3600);
    $m = intdiv($sn % 3600, 60);
    $s = $sn % 60;
    if ($h > 0) {
        return sprintf('%d:%02d:%02d', $h, $m, $s);
    }
    return sprintf('%d:%02d', $m, $s);
}
