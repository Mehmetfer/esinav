<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/data/video-dersler.php';

$__metroEkitapKonular = dirname(__DIR__) . '/data/e-kitap-konular.php';
if (is_file($__metroEkitapKonular)) {
    require_once $__metroEkitapKonular;
}
unset($__metroEkitapKonular);

if (!function_exists('metro_ekitap_resmi_konular')) {
    /** @return array<string, list<array{id:string,title:string,youtube_id?:string}>> */
    function metro_ekitap_resmi_konular(): array
    {
        return [];
    }
}

/**
 * E-Kitap konuları: resmi liste varsa onu kullan, yoksa video başlıklarından üret.
 *
 * @return array<string, list<array{id:string,title:string,youtube_id:string}>>
 */
function metro_ekitap_konular(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $byDers = metro_ekitap_resmi_konular();

    // Resmi listede olmayan dersler için video kataloğundan numaralı konular
    foreach (metro_video_dersler() as $v) {
        $ders = (string)($v['ders'] ?? '');
        $title = trim((string)($v['title'] ?? ''));
        $yid = (string)($v['youtube_id'] ?? '');
        if ($ders === '' || $title === '' || isset($byDers[$ders])) {
            continue;
        }
        if (!preg_match('/^\d+/u', $title)) {
            continue;
        }
        $id = substr(sha1($ders . '|' . $yid . '|' . $title), 0, 16);
        $byDers[$ders][] = [
            'id' => $id,
            'title' => $title,
            'youtube_id' => $yid,
        ];
    }

    if (empty($byDers['direksiyon'])) {
        $dirTitles = [
            '01- Direksiyon Eğitimine Giriş',
            '02- Araç Kontrolleri',
            '03- Kalkış ve Düz Gidiş',
            '04- Vites ve Debriyaj',
            '05- Dönüşler',
            '06- Park Manevraları',
            '07- Geri Manevra',
            '08- Trafikte Güvenli Sürüş',
        ];
        foreach ($dirTitles as $t) {
            $id = substr(sha1('direksiyon|' . $t), 0, 16);
            $byDers['direksiyon'][] = [
                'id' => $id,
                'title' => $t,
                'youtube_id' => '',
            ];
        }
    }

    // youtube_id yoksa boş string garanti
    foreach ($byDers as $d => $list) {
        foreach ($list as $i => $k) {
            if (!isset($byDers[$d][$i]['youtube_id'])) {
                $byDers[$d][$i]['youtube_id'] = '';
            }
        }
    }

    $cache = $byDers;
    return $cache;
}

/** @return list<array{ders:string,title:string,youtube_id:string}> */
function metro_ekitap_ders_videolari(string $ders, int $limit = 48): array
{
    $out = [];
    foreach (metro_video_dersler() as $v) {
        if (($v['ders'] ?? '') !== $ders) {
            continue;
        }
        $out[] = $v;
        if (count($out) >= $limit) {
            break;
        }
    }
    return $out;
}

/** @return list<array{id:string,title:string,youtube_id:string}> */
function metro_ekitap_ders_konular(string $ders): array
{
    $all = metro_ekitap_konular();
    return $all[$ders] ?? [];
}

function metro_ekitap_konu_bul(string $ders, string $konuId): ?array
{
    foreach (metro_ekitap_ders_konular($ders) as $k) {
        if (($k['id'] ?? '') === $konuId) {
            return $k;
        }
    }
    return null;
}

function metro_ekitap_toplam_konu(): int
{
    $n = 0;
    foreach (metro_ekitap_konular() as $list) {
        $n += count($list);
    }
    return $n;
}

/**
 * Konu HTML içeriği (data/ekitap-icerik/{konu_id}.html).
 */
function metro_ekitap_icerik(string $konuId): string
{
    $konuId = preg_replace('/[^a-f0-9]/i', '', $konuId) ?? '';
    if ($konuId === '') {
        return '';
    }
    $path = dirname(__DIR__) . '/data/ekitap-icerik/' . $konuId . '.html';
    if (!is_file($path)) {
        return '';
    }
    $html = (string)file_get_contents($path);
    return $html;
}

function ekitap_ensure_table(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS kursiyer_ekitap (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          kursiyer_id INT UNSIGNED NOT NULL,
          ders VARCHAR(40) NOT NULL,
          konu_id VARCHAR(32) NOT NULL,
          progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
          okundu_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          UNIQUE KEY uq_ekitap (kursiyer_id, ders, konu_id),
          KEY idx_ekitap_kursiyer (kursiyer_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci"
    );
    try {
        $pdo->exec('ALTER TABLE kursiyer_ekitap ADD COLUMN progress TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER konu_id');
    } catch (Throwable) {
        // kolon zaten var
    }
}

function ekitap_konu_okundu(int $kursiyerId, string $ders, string $konuId, int $progress = 5): void
{
    if ($kursiyerId <= 0 || $ders === '' || $konuId === '') {
        return;
    }
    $progress = max(0, min(100, $progress));
    try {
        $pdo = db();
        ekitap_ensure_table($pdo);
        $pdo->prepare(
            'INSERT INTO kursiyer_ekitap (kursiyer_id, ders, konu_id, progress)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               progress = GREATEST(progress, VALUES(progress)),
               okundu_at = CURRENT_TIMESTAMP'
        )->execute([$kursiyerId, $ders, $konuId, $progress]);
    } catch (Throwable) {
        // İlerleme kritik değil
    }
}

/**
 * @return array{okunan:array<string,int>, toplam:array<string,int>, pct:array<string,int>, recent:list<array{ders:string,konu_id:string,title:string,okundu_at:string,progress:int}>}
 */
function ekitap_progress(int $kursiyerId): array
{
    $dersler = array_keys(metro_dersler());
    $toplam = [];
    $okunan = [];
    $pct = [];
    foreach ($dersler as $d) {
        $toplam[$d] = count(metro_ekitap_ders_konular($d));
        $okunan[$d] = 0;
        $pct[$d] = 0;
    }

    $recent = [];
    if ($kursiyerId <= 0) {
        return compact('okunan', 'toplam', 'pct', 'recent');
    }

    try {
        $pdo = db();
        ekitap_ensure_table($pdo);
        $st = $pdo->prepare(
            'SELECT ders, konu_id, okundu_at, progress FROM kursiyer_ekitap
             WHERE kursiyer_id = ? ORDER BY okundu_at DESC'
        );
        $st->execute([$kursiyerId]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $titleMap = [];
        foreach (metro_ekitap_konular() as $d => $list) {
            foreach ($list as $k) {
                $titleMap[$d . '|' . $k['id']] = $k['title'];
            }
        }
        foreach ($rows as $r) {
            $d = (string)$r['ders'];
            $p = (int)($r['progress'] ?? 0);
            if ($p >= 80 && isset($okunan[$d])) {
                $okunan[$d]++;
            }
            if (count($recent) < 8) {
                $key = $d . '|' . $r['konu_id'];
                $recent[] = [
                    'ders' => $d,
                    'konu_id' => (string)$r['konu_id'],
                    'title' => $titleMap[$key] ?? (string)$r['konu_id'],
                    'okundu_at' => (string)$r['okundu_at'],
                    'progress' => max(0, min(100, $p > 0 ? $p : 2)),
                ];
            }
        }
        foreach ($dersler as $d) {
            $t = max(1, $toplam[$d]);
            $pct[$d] = (int)round(($okunan[$d] / $t) * 100);
            if ($pct[$d] > 100) {
                $pct[$d] = 100;
            }
        }
    } catch (Throwable) {
        // ignore
    }

    return compact('okunan', 'toplam', 'pct', 'recent');
}

/** @return array<string,bool> */
function ekitap_okunan_set(int $kursiyerId, string $ders): array
{
    $out = [];
    if ($kursiyerId <= 0) {
        return $out;
    }
    try {
        $pdo = db();
        ekitap_ensure_table($pdo);
        $st = $pdo->prepare(
            'SELECT konu_id, progress FROM kursiyer_ekitap WHERE kursiyer_id = ? AND ders = ?'
        );
        $st->execute([$kursiyerId, $ders]);
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if ((int)($row['progress'] ?? 0) >= 80) {
                $out[(string)$row['konu_id']] = true;
            }
        }
    } catch (Throwable) {
        // ignore
    }
    return $out;
}

