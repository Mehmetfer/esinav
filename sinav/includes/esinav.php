<?php
declare(strict_types=1);

/**
 * E-Sinav sabitleri ve motor.
 * Dagilim: trafik 25 + ilkyardim 12 + adab 1 + arac 12 = 50
 * Dogru = 2 puan, yanlis götürmez, 70+ basarili, sure 45 dk.
 */

const ESINAV_SURE_SN = 2700;
const ESINAV_PUAN_DOGRU = 2;
const ESINAV_GECME_PUAN = 70;
const ESINAV_SORU_SAYISI = 50;

/** @return array<string,int> */
function esinav_dagilim(): array
{
    return [
        'trafik' => 25,
        'ilkyardim' => 12,
        'adab' => 1,
        'arac' => 12,
    ];
}

/** @return array<string,string> */
function esinav_ders_adlari(): array
{
    return [
        'trafik' => 'Trafik ve Çevre Bilgisi',
        'ilkyardim' => 'İlk Yardım Bilgisi',
        'arac' => 'Motor ve Araç Tekniği',
        'adab' => 'Trafik Adabı',
    ];
}

function esinav_ensure_tables(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS sorular (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          ders VARCHAR(20) NOT NULL,
          soru TEXT NOT NULL,
          secenek_a VARCHAR(500) NOT NULL,
          secenek_b VARCHAR(500) NOT NULL,
          secenek_c VARCHAR(500) NOT NULL,
          secenek_d VARCHAR(500) NOT NULL,
          dogru CHAR(1) NOT NULL,
          aktif TINYINT(1) NOT NULL DEFAULT 1,
          soru_ar TEXT NULL,
          secenek_a_ar VARCHAR(500) NULL,
          secenek_b_ar VARCHAR(500) NULL,
          secenek_c_ar VARCHAR(500) NULL,
          secenek_d_ar VARCHAR(500) NULL,
          gorsel VARCHAR(255) NULL,
          kaynak VARCHAR(20) NULL,
          KEY idx_soru_ders (ders)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci"
    );
    esinav_ensure_ar_columns($pdo);
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS esinav_oturum (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          kursiyer_id INT UNSIGNED NOT NULL,
          baslangic DATETIME NOT NULL,
          bitis DATETIME NULL,
          sure_sn INT NOT NULL DEFAULT 2700,
          puan INT NULL,
          dogru_sayisi INT NULL,
          basarili TINYINT(1) NULL,
          durum VARCHAR(10) NOT NULL DEFAULT 'devam',
          KEY idx_oturum_kursiyer (kursiyer_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci"
    );
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS esinav_soru (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          oturum_id INT UNSIGNED NOT NULL,
          soru_id INT UNSIGNED NOT NULL,
          sira TINYINT UNSIGNED NOT NULL,
          cevap CHAR(1) NULL,
          UNIQUE KEY uq_oturum_sira (oturum_id, sira),
          KEY idx_es_oturum (oturum_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci"
    );
}

/** Mevcut kurulumlara Arapça / görsel kolonları ekle. */
function esinav_ensure_ar_columns(PDO $pdo): void
{
    try {
        $pdo->query('SELECT 1 FROM sorular LIMIT 1');
    } catch (Throwable) {
        return;
    }
    $need = [
        'soru_ar' => 'TEXT NULL',
        'secenek_a_ar' => 'VARCHAR(500) NULL',
        'secenek_b_ar' => 'VARCHAR(500) NULL',
        'secenek_c_ar' => 'VARCHAR(500) NULL',
        'secenek_d_ar' => 'VARCHAR(500) NULL',
        'gorsel' => 'VARCHAR(255) NULL',
        'kaynak' => 'VARCHAR(20) NULL',
    ];
    foreach ($need as $col => $def) {
        try {
            $st = $pdo->query("SHOW COLUMNS FROM sorular LIKE " . $pdo->quote($col));
            if ($st && $st->fetch()) {
                continue;
            }
            $pdo->exec("ALTER TABLE sorular ADD COLUMN {$col} {$def}");
        } catch (Throwable) {
            // yetki / kilit vb. — SELECT tarafı fallback kullanır
        }
    }
}

/**
 * Oturumdaki sıradaki soruyu getir (yeni kolonlar yoksa güvenli fallback).
 *
 * @return array<string,mixed>|null
 */
function esinav_oturum_soru(PDO $pdo, int $oturumId, int $sira): ?array
{
    esinav_ensure_ar_columns($pdo);
    $sqlFull = 'SELECT es.sira, es.cevap, s.soru, s.secenek_a, s.secenek_b, s.secenek_c, s.secenek_d,
            s.soru_ar, s.secenek_a_ar, s.secenek_b_ar, s.secenek_c_ar, s.secenek_d_ar, s.gorsel, s.kaynak
     FROM esinav_soru es
     INNER JOIN sorular s ON s.id = es.soru_id
     WHERE es.oturum_id = ? AND es.sira = ? LIMIT 1';
    $sqlBasic = 'SELECT es.sira, es.cevap, s.soru, s.secenek_a, s.secenek_b, s.secenek_c, s.secenek_d
     FROM esinav_soru es
     INNER JOIN sorular s ON s.id = es.soru_id
     WHERE es.oturum_id = ? AND es.sira = ? LIMIT 1';
    try {
        $stmt = $pdo->prepare($sqlFull);
        $stmt->execute([$oturumId, $sira]);
        $row = $stmt->fetch();
        return $row ?: null;
    } catch (Throwable) {
        $stmt = $pdo->prepare($sqlBasic);
        $stmt->execute([$oturumId, $sira]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}

/**
 * Dil tercihine göre soru metinlerini seç (AR yoksa TR).
 *
 * @param array<string,mixed> $row
 * @return array{soru:string,a:string,b:string,c:string,d:string,gorsel:?string}
 */
function esinav_soru_gosterim(array $row, ?string $lang = null): array
{
    if ($lang === null) {
        if (function_exists('dil')) {
            $lang = dil();
        } else {
            $lang = 'tr';
        }
    }
    $arOk = $lang === 'ar'
        && trim((string)($row['soru_ar'] ?? '')) !== '';

    $gorsel = trim((string)($row['gorsel'] ?? ''));
    if ($gorsel === '') {
        $gorsel = null;
    }

    if ($arOk) {
        return [
            'soru' => (string)$row['soru_ar'],
            'a' => (string)($row['secenek_a_ar'] ?? $row['secenek_a'] ?? ''),
            'b' => (string)($row['secenek_b_ar'] ?? $row['secenek_b'] ?? ''),
            'c' => (string)($row['secenek_c_ar'] ?? $row['secenek_c'] ?? ''),
            'd' => (string)($row['secenek_d_ar'] ?? $row['secenek_d'] ?? ''),
            'gorsel' => $gorsel,
        ];
    }
    return [
        'soru' => (string)($row['soru'] ?? ''),
        'a' => (string)($row['secenek_a'] ?? ''),
        'b' => (string)($row['secenek_b'] ?? ''),
        'c' => (string)($row['secenek_c'] ?? ''),
        'd' => (string)($row['secenek_d'] ?? ''),
        'gorsel' => $gorsel,
    ];
}

function esinav_seed_if_needed(PDO $pdo): void
{
    esinav_ensure_ar_columns($pdo);
    $count = (int)$pdo->query('SELECT COUNT(*) FROM sorular')->fetchColumn();
    $bank = require dirname(__DIR__) . '/data/soru-havuzu.php';

    $insCols = 'INSERT INTO sorular (ders, soru, secenek_a, secenek_b, secenek_c, secenek_d, dogru, aktif,
                soru_ar, secenek_a_ar, secenek_b_ar, secenek_c_ar, secenek_d_ar, gorsel, kaynak)
             VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?)';

    $rowParams = static function (array $q): array {
        return [
            $q['ders'], $q['soru'], $q['a'], $q['b'], $q['c'], $q['d'], strtoupper($q['dogru']),
            $q['soru_ar'] ?? null, $q['a_ar'] ?? null, $q['b_ar'] ?? null, $q['c_ar'] ?? null, $q['d_ar'] ?? null,
            $q['gorsel'] ?? null, $q['kaynak'] ?? null,
        ];
    };

    if ($count === 0) {
        $ins = $pdo->prepare($insCols);
        foreach ($bank as $q) {
            $ins->execute($rowParams($q));
        }
        return;
    }

    // Mevcut kurulumlarda yeni soruları ekle + Arapça / görsel güncelle
    $check = $pdo->prepare('SELECT id, soru_ar, gorsel FROM sorular WHERE soru = ? LIMIT 1');
    $ins = $pdo->prepare($insCols);
    $updMeta = $pdo->prepare(
        'UPDATE sorular SET soru_ar = COALESCE(NULLIF(soru_ar, \'\'), ?),
            secenek_a_ar = COALESCE(NULLIF(secenek_a_ar, \'\'), ?),
            secenek_b_ar = COALESCE(NULLIF(secenek_b_ar, \'\'), ?),
            secenek_c_ar = COALESCE(NULLIF(secenek_c_ar, \'\'), ?),
            secenek_d_ar = COALESCE(NULLIF(secenek_d_ar, \'\'), ?),
            gorsel = COALESCE(NULLIF(gorsel, \'\'), ?),
            kaynak = COALESCE(NULLIF(kaynak, \'\'), ?)
         WHERE id = ?'
    );
    foreach ($bank as $q) {
        $check->execute([$q['soru']]);
        $exist = $check->fetch();
        if ($exist) {
            $updMeta->execute([
                $q['soru_ar'] ?? null,
                $q['a_ar'] ?? null,
                $q['b_ar'] ?? null,
                $q['c_ar'] ?? null,
                $q['d_ar'] ?? null,
                $q['gorsel'] ?? null,
                $q['kaynak'] ?? null,
                (int)$exist['id'],
            ]);
            continue;
        }
        $ins->execute($rowParams($q));
    }

    $updBySoru = $pdo->prepare(
        'UPDATE sorular SET soru_ar = ?, secenek_a_ar = ?, secenek_b_ar = ?, secenek_c_ar = ?, secenek_d_ar = ?,
            gorsel = COALESCE(NULLIF(gorsel, \'\'), ?), kaynak = COALESCE(NULLIF(kaynak, \'\'), ?)
         WHERE soru = ? AND (soru_ar IS NULL OR soru_ar = \'\')'
    );
    foreach ($bank as $q) {
        if (empty($q['soru_ar'])) {
            continue;
        }
        $updBySoru->execute([
            $q['soru_ar'], $q['a_ar'] ?? null, $q['b_ar'] ?? null, $q['c_ar'] ?? null, $q['d_ar'] ?? null,
            $q['gorsel'] ?? null, $q['kaynak'] ?? null, $q['soru'],
        ]);
    }
}

/**
 * @return list<int> secilen soru id listesi (50)
 */
function esinav_sec_sorular(PDO $pdo): array
{
    $secilen = [];
    foreach (esinav_dagilim() as $ders => $adet) {
        $stmt = $pdo->prepare(
            'SELECT id FROM sorular WHERE aktif = 1 AND ders = ? ORDER BY RAND() LIMIT ' . (int)$adet
        );
        $stmt->execute([$ders]);
        $ids = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        if (count($ids) < $adet) {
            throw new RuntimeException(
                esinav_ders_adlari()[$ders] . " havuzunda yeterli soru yok (gerekli: {$adet}, var: " . count($ids) . ').'
            );
        }
        $secilen = array_merge($secilen, $ids);
    }
    shuffle($secilen);
    return $secilen;
}

function esinav_baslat(PDO $pdo, int $kursiyerId): int
{
    esinav_ensure_tables($pdo);
    esinav_seed_if_needed($pdo);

    // Ayni anda acik sinav varsa onu bitir / iptal et
    $pdo->prepare(
        "UPDATE esinav_oturum SET durum = 'bitti', bitis = NOW(), puan = COALESCE(puan, 0), basarili = 0
         WHERE kursiyer_id = ? AND durum = 'devam'"
    )->execute([$kursiyerId]);

    $ids = esinav_sec_sorular($pdo);
    $pdo->beginTransaction();
    try {
        $pdo->prepare(
            'INSERT INTO esinav_oturum (kursiyer_id, baslangic, sure_sn, durum) VALUES (?, NOW(), ?, ?)'
        )->execute([$kursiyerId, ESINAV_SURE_SN, 'devam']);
        $oturumId = (int)$pdo->lastInsertId();

        $ins = $pdo->prepare('INSERT INTO esinav_soru (oturum_id, soru_id, sira) VALUES (?, ?, ?)');
        foreach ($ids as $i => $soruId) {
            $ins->execute([$oturumId, $soruId, $i + 1]);
        }
        $pdo->commit();
        return $oturumId;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/** @return array<string,mixed>|null */
function esinav_aktif_oturum(PDO $pdo, int $kursiyerId): ?array
{
    $stmt = $pdo->prepare(
        "SELECT * FROM esinav_oturum WHERE kursiyer_id = ? AND durum = 'devam' ORDER BY id DESC LIMIT 1"
    );
    $stmt->execute([$kursiyerId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function esinav_kalan_sn(array $oturum): int
{
    $start = strtotime((string)$oturum['baslangic']);
    $limit = (int)$oturum['sure_sn'];
    $kalan = $limit - (time() - $start);
    return max(0, $kalan);
}

function esinav_cevap_kaydet(PDO $pdo, int $oturumId, int $sira, string $cevap): void
{
    $cevap = strtoupper(substr($cevap, 0, 1));
    if (!in_array($cevap, ['A', 'B', 'C', 'D'], true)) {
        return;
    }
    $pdo->prepare(
        'UPDATE esinav_soru SET cevap = ? WHERE oturum_id = ? AND sira = ?'
    )->execute([$cevap, $oturumId, $sira]);
}

/** @return array{puan:int,dogru:int,basarili:bool} */
function esinav_bitir(PDO $pdo, int $oturumId): array
{
    $stmt = $pdo->prepare(
        'SELECT es.cevap, s.dogru
         FROM esinav_soru es
         INNER JOIN sorular s ON s.id = es.soru_id
         WHERE es.oturum_id = ?'
    );
    $stmt->execute([$oturumId]);
    $rows = $stmt->fetchAll();
    $dogru = 0;
    foreach ($rows as $r) {
        if ($r['cevap'] !== null && strtoupper((string)$r['cevap']) === strtoupper((string)$r['dogru'])) {
            $dogru++;
        }
    }
    $puan = $dogru * ESINAV_PUAN_DOGRU;
    $basarili = $puan >= ESINAV_GECME_PUAN ? 1 : 0;
    $pdo->prepare(
        "UPDATE esinav_oturum
         SET bitis = NOW(), puan = ?, dogru_sayisi = ?, basarili = ?, durum = 'bitti'
         WHERE id = ?"
    )->execute([$puan, $dogru, $basarili, $oturumId]);

    return ['puan' => $puan, 'dogru' => $dogru, 'basarili' => $basarili === 1];
}

/**
 * Oturum istatistikleri — genel + ders bazli.
 *
 * @return array{
 *   toplam:int,dogru:int,yanlis:int,bos:int,puan:int,basarili:bool,
 *   dersler:array<string,array{ad:string,toplam:int,dogru:int,yanlis:int,bos:int}>
 * }
 */
function esinav_istatistik(PDO $pdo, int $oturumId): array
{
    $adlar = esinav_ders_adlari();
    $dersler = [];
    foreach (array_keys($adlar) as $kod) {
        $dersler[$kod] = ['ad' => $adlar[$kod], 'toplam' => 0, 'dogru' => 0, 'yanlis' => 0, 'bos' => 0];
    }

    $stmt = $pdo->prepare(
        'SELECT es.cevap, s.dogru, s.ders
         FROM esinav_soru es
         INNER JOIN sorular s ON s.id = es.soru_id
         WHERE es.oturum_id = ?'
    );
    $stmt->execute([$oturumId]);
    $rows = $stmt->fetchAll();

    $dogru = 0;
    $yanlis = 0;
    $bos = 0;
    foreach ($rows as $r) {
        $ders = (string)$r['ders'];
        if (!isset($dersler[$ders])) {
            $dersler[$ders] = ['ad' => $ders, 'toplam' => 0, 'dogru' => 0, 'yanlis' => 0, 'bos' => 0];
        }
        $dersler[$ders]['toplam']++;
        $cevap = $r['cevap'] !== null && $r['cevap'] !== '' ? strtoupper((string)$r['cevap']) : '';
        if ($cevap === '') {
            $bos++;
            $dersler[$ders]['bos']++;
        } elseif ($cevap === strtoupper((string)$r['dogru'])) {
            $dogru++;
            $dersler[$ders]['dogru']++;
        } else {
            $yanlis++;
            $dersler[$ders]['yanlis']++;
        }
    }

    $toplam = count($rows);
    $puan = $dogru * ESINAV_PUAN_DOGRU;

    return [
        'toplam' => $toplam,
        'dogru' => $dogru,
        'yanlis' => $yanlis,
        'bos' => $bos,
        'puan' => $puan,
        'basarili' => $puan >= ESINAV_GECME_PUAN,
        'dersler' => $dersler,
    ];
}

