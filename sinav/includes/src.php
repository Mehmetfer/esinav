<?php
declare(strict_types=1);

const SRC_SURE_SN = 2700;
const SRC_PUAN_DOGRU = 2;
const SRC_GECME_PUAN = 70;
const SRC_SORU_SAYISI = 50;
/** Konulu (mini) sinav: soru sayisi ve sure */
const SRC_KONULU_SORU = 10;
const SRC_KONULU_SURE_SN = 600;

function src_konular(): array {
    return [
        'is_sagligi'     => '01 - İş Sağlığı ve İş Güvenliği',
        'is_organizasyon'=> '02 - İş Organizasyonu',
        'surus_hazirlik'=> '03 - Aracın Yolculuk Öncesi Sürüş Hazırlığı',
        'yolcu_tasima'   => '04 - Yolcu Taşıma Kuralları',
        'guvenli_surus'  => '05 - Güvenli Sürüş Teknikleri',
        'mevzuat'        => '06 - Yolcu Taşıma (Trafik, Ulaştırma ve Turizm) Mevzuatı',
        'trafik_cezalar' => '07 - Trafik Kuralları ve Cezalar',
        'psikoloji'      => '08 - Trafik ve Davranış Psikolojisi',
        'trafik_adabi'   => '09 - Trafik Adabı ve Görgü Kuralları',
        'iletisim'       => '10 - İletişim Teknolojileri ve Harita Okuma Bilgisi',
        'gumruk'         => '11 - Gümrük - Kaçakçılık ve Tır Mevzuatı',
        'yasal'          => '12 - Yasal Sorumluluklar ve Sigorta ve Lojistik Coğrafyası',
        'ilk_yardim'     => '13 - İlk Yardım',
        'arac_bilgisi'   => '14 - Araç Bilgisi ve Ekonomik Araç Kullanma',
        'meslek_gelisim' => '15 - Mesleki Gelişim Dersi',
        'cikmis'         => '16 - Çıkmış Sınav Soruları',
    ];
}

function src_ders_adlari(): array {
    return src_konular();
}

/**
 * SRC sinavi soru dagilimi (toplam SRC_SORU_SAYISI = 50).
 * Bir derste yeterli soru yoksa o dersten elde olan kadari cekilir.
 */
function src_dagilim(): array {
    return [
        'is_sagligi' => 4, 'is_organizasyon' => 4, 'surus_hazirlik' => 4,
        'yolcu_tasima' => 3, 'guvenli_surus' => 4, 'mevzuat' => 3,
        'trafik_cezalar' => 4, 'psikoloji' => 3, 'trafik_adabi' => 2,
        'iletisim' => 3, 'gumruk' => 3, 'yasal' => 3,
        'ilk_yardim' => 4, 'arac_bilgisi' => 4, 'meslek_gelisim' => 2,
    ];
}

/**
 * Belirli bir derste (konu slug) aktif soru sayisi.
 */
function src_konu_soru_sayisi(PDO $pdo, string $konu): int {
    try {
        $st = $pdo->prepare("SELECT COUNT(*) FROM src_sorular WHERE ders = ? AND aktif = 1");
        $st->execute([$konu]);
        return (int)$st->fetchColumn();
    } catch (Throwable) {
        return 0;
    }
}

/**
 * Tum derslerin aktif soru sayilari: ['slug' => adet].
 */
function src_konu_sayilari(PDO $pdo): array {
    $sonuc = [];
    try {
        foreach ($pdo->query("SELECT ders, COUNT(*) AS adet FROM src_sorular WHERE aktif = 1 GROUP BY ders")->fetchAll() as $r) {
            $sonuc[(string)$r['ders']] = (int)$r['adet'];
        }
    } catch (Throwable) {
    }
    return $sonuc;
}

function src_ensure_tables(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS src_sorular (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      ders VARCHAR(30) NOT NULL DEFAULT 'is_sagligi',
      soru TEXT NOT NULL,
      secenek_a VARCHAR(500) NOT NULL,
      secenek_b VARCHAR(500) NOT NULL,
      secenek_c VARCHAR(500) NOT NULL,
      secenek_d VARCHAR(500) NOT NULL,
      dogru CHAR(1) NOT NULL,
      aktif TINYINT(1) NOT NULL DEFAULT 1,
      gorsel VARCHAR(255) NULL,
      kaynak VARCHAR(20) NULL,
      aciklama TEXT NULL,
      KEY idx_src_soru_konu (ders)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS src_oturum (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      kursiyer_id INT UNSIGNED NOT NULL,
      tip VARCHAR(20) NOT NULL DEFAULT 'deneme',
      konu VARCHAR(30) NULL,
      baslangic DATETIME NOT NULL,
      bitis DATETIME NULL,
      sure_sn INT NOT NULL DEFAULT 2700,
      puan INT NULL,
      dogru_sayisi INT NULL,
      basarili TINYINT(1) NULL,
      durum VARCHAR(10) NOT NULL DEFAULT 'devam',
      KEY idx_src_oturum_kursiyer (kursiyer_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS src_soru (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      oturum_id INT UNSIGNED NOT NULL,
      soru_id INT UNSIGNED NOT NULL,
      sira TINYINT UNSIGNED NOT NULL,
      cevap CHAR(1) NULL,
      UNIQUE KEY uq_src_oturum_sira (oturum_id, sira),
      KEY idx_src_es_oturum (oturum_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS src_ders_notlari (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      konu VARCHAR(30) NOT NULL,
      baslik VARCHAR(200) NOT NULL,
      ozet TEXT NULL,
      ico VARCHAR(30) NULL,
      icerik LONGTEXT NULL,
      dosya VARCHAR(255) NULL,
      sira TINYINT NOT NULL DEFAULT 0,
      aktif TINYINT(1) NOT NULL DEFAULT 1,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      KEY idx_src_ders_konu (konu)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS src_videolar (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      konu VARCHAR(30) NOT NULL,
      baslik VARCHAR(200) NOT NULL,
      youtube_id VARCHAR(20) NULL,
      aciklama TEXT NULL,
      sira TINYINT NOT NULL DEFAULT 0,
      aktif TINYINT(1) NOT NULL DEFAULT 1,
      KEY idx_src_video_konu (konu)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci");
    src_ensure_oturum_kolonlari($pdo);
}

/**
 * src_oturum tablosunda sonradan eklenen kolonlari tamamlar.
 * CREATE TABLE IF NOT EXISTS mevcut tabloyu degistirmedigi icin
 * eski kurulumlarda 'tip' / 'konu' kolonlari eksik kalabiliyor.
 */
function src_ensure_oturum_kolonlari(PDO $pdo): void {
    $eksikler = [
        'tip'  => "ALTER TABLE src_oturum ADD COLUMN tip VARCHAR(20) NOT NULL DEFAULT 'deneme' AFTER kursiyer_id",
        'konu' => "ALTER TABLE src_oturum ADD COLUMN konu VARCHAR(30) NULL AFTER tip",
    ];
    foreach ($eksikler as $kolon => $sql) {
        try {
            $st = $pdo->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'src_oturum' AND COLUMN_NAME = ?"
            );
            $st->execute([$kolon]);
            if ((int)$st->fetchColumn() === 0) {
                $pdo->exec($sql);
            }
        } catch (Throwable) {
            // Sema degistirme yetkisi yoksa sessizce devam et (koruma amacli).
        }
    }
}

function src_seed_if_needed(PDO $pdo): void {
    try { $c = (int)$pdo->query("SELECT COUNT(*) FROM src_sorular")->fetchColumn(); if ($c > 0) return; } catch (Throwable) { return; }
}

function src_oturum_soru(PDO $pdo, int $oturumId, int $sira): ?array {
    $st = $pdo->prepare("SELECT es.sira, es.cevap, s.soru, s.secenek_a, s.secenek_b, s.secenek_c, s.secenek_d, s.dogru, s.gorsel, s.ders FROM src_soru es INNER JOIN src_sorular s ON s.id = es.soru_id WHERE es.oturum_id = ? AND es.sira = ?");
    $st->execute([$oturumId, $sira]);
    return $st->fetch() ?: null;
}

function src_oturum_baslat(PDO $pdo, int $kursiyerId, string $tip = 'deneme', ?string $konu = null): int {
    $pdo->beginTransaction();
    try {
        // Ayni anda tek aktif oturum kalsin (deneme veya konulu farketmez).
        $pdo->prepare("UPDATE src_oturum SET durum = 'bitti', bitis = NOW() WHERE kursiyer_id = ? AND durum = 'devam'")->execute([$kursiyerId]);
        if ($tip === 'konulu' && $konu) {
            $st = $pdo->prepare("SELECT id FROM src_sorular WHERE ders = ? AND aktif = 1 ORDER BY RAND() LIMIT " . SRC_KONULU_SORU);
            $st->execute([$konu]);
            $ids = $st->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $tip = 'deneme';
            $konu = null;
            $dagilim = src_dagilim();
            $ids = [];
            foreach ($dagilim as $k => $adet) {
                $st = $pdo->prepare("SELECT id FROM src_sorular WHERE ders = ? AND aktif = 1 ORDER BY RAND() LIMIT ?");
                $st->execute([$k, $adet]);
                foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $fid) { $ids[] = (int)$fid; }
            }
        }
        $sure = ($tip === 'konulu') ? SRC_KONULU_SURE_SN : SRC_SURE_SN;
        $pdo->prepare("INSERT INTO src_oturum (kursiyer_id, tip, konu, baslangic, sure_sn, durum) VALUES (?, ?, ?, NOW(), ?, 'devam')")->execute([$kursiyerId, $tip, $konu, $sure]);
        $oturumId = (int)$pdo->lastInsertId();
        $ins = $pdo->prepare("INSERT INTO src_soru (oturum_id, soru_id, sira) VALUES (?, ?, ?)");
        foreach ($ids as $i => $sid) { $ins->execute([$oturumId, $sid, $i + 1]); }
        $pdo->commit();
        return $oturumId;
    } catch (Throwable $e) { $pdo->rollBack(); throw $e; }
}

/**
 * Kursiyerin devam eden SRC oturumunu dondurur.
 * $tip = null verilirse tip filtresi uygulanmaz (deneme + konulu birlikte).
 * $konu verilirse sadece o konuya ait oturum aranir.
 */
function src_aktif_oturum(PDO $pdo, int $kursiyerId, ?string $tip = 'deneme', ?string $konu = null): ?array {
    $sql = "SELECT * FROM src_oturum WHERE kursiyer_id = ? AND durum = 'devam'";
    $par = [$kursiyerId];
    if ($tip !== null) { $sql .= " AND tip = ?"; $par[] = $tip; }
    if ($konu !== null) { $sql .= " AND konu = ?"; $par[] = $konu; }
    $sql .= " ORDER BY id DESC LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->execute($par);
    return $st->fetch() ?: null;
}

function src_kalan_sn(array $oturum): int {
    return max(0, (int)$oturum['sure_sn'] - (time() - strtotime((string)$oturum['baslangic'])));
}

function src_cevap_kaydet(PDO $pdo, int $oturumId, int $sira, string $cevap): void {
    $cevap = strtoupper(substr($cevap, 0, 1));
    if (!in_array($cevap, ['A','B','C','D'], true)) return;
    $pdo->prepare("UPDATE src_soru SET cevap = ? WHERE oturum_id = ? AND sira = ?")->execute([$cevap, $oturumId, $sira]);
}

function src_bitir(PDO $pdo, int $oturumId): array {
    $st = $pdo->prepare("SELECT es.cevap, s.dogru FROM src_soru es INNER JOIN src_sorular s ON s.id = es.soru_id WHERE es.oturum_id = ?");
    $st->execute([$oturumId]);
    $dogru = 0;
    foreach ($st->fetchAll() as $r) {
        if ($r['cevap'] !== null && strtoupper((string)$r['cevap']) === strtoupper((string)$r['dogru'])) $dogru++;
    }
    $puan = $dogru * SRC_PUAN_DOGRU;
    $basarili = $puan >= SRC_GECME_PUAN ? 1 : 0;
    $pdo->prepare("UPDATE src_oturum SET bitis = NOW(), puan = ?, dogru_sayisi = ?, basarili = ?, durum = 'bitti' WHERE id = ?")->execute([$puan, $dogru, $basarili, $oturumId]);
    return ['puan' => $puan, 'dogru' => $dogru, 'basarili' => $basarili === 1];
}

function src_istatistik(PDO $pdo, int $oturumId): array {
    $konular = src_konular();
    $dersler = [];
    foreach (array_keys($konular) as $k) { $dersler[$k] = ['ad' => $konular[$k], 'toplam' => 0, 'dogru' => 0, 'yanlis' => 0, 'bos' => 0]; }
    $st = $pdo->prepare("SELECT es.cevap, s.dogru, s.ders FROM src_soru es INNER JOIN src_sorular s ON s.id = es.soru_id WHERE es.oturum_id = ?");
    $st->execute([$oturumId]);
    $dogru = $yanlis = $bos = 0;
    foreach ($st->fetchAll() as $r) {
        $k = (string)($r['ders'] ?? 'trafik_cezalar');
        if (!isset($dersler[$k])) $k = 'trafik_cezalar';
        $dersler[$k]['toplam']++;
        if ($r['cevap'] === null) { $dersler[$k]['bos']++; $bos++; }
        elseif (strtoupper((string)$r['cevap']) === strtoupper((string)$r['dogru'])) { $dersler[$k]['dogru']++; $dogru++; }
        else { $dersler[$k]['yanlis']++; $yanlis++; }
    }
    return ['puan' => $dogru * SRC_PUAN_DOGRU, 'dogru' => $dogru, 'yanlis' => $yanlis, 'bos' => $bos, 'dersler' => $dersler];
}

/**
 * SRC ders notlarini DB'den okur (admin panelinden yönetilir).
 * @return list<array{id:int,konu:string,baslik:string,ozet?:string,ico?:string,icerik?:string,dosya?:string,sira:int,aktif:int}>
 */
function src_ders_notlari(PDO $pdo): array
{
    try {
        $rows = $pdo->query(
            "SELECT * FROM src_ders_notlari WHERE aktif = 1 ORDER BY sira, id"
        )->fetchAll();
    } catch (Throwable) {
        return [];
    }

    // Katilimci sayfasiyla uyumlu sekilde konulara gore grupla (baslik sirasina gore)
    $sonuc = [];
    foreach ($rows as $r) {
        $sonuc[] = [
            'id' => (int)$r['id'],
            'konu' => (string)($r['konu'] ?? ''),
            'baslik' => (string)$r['baslik'],
            'ozet' => (string)($r['ozet'] ?? ''),
            'ico' => (string)($r['ico'] ?? 'fa-book'),
            'icerik' => (string)($r['icerik'] ?? ''),
            'dosya' => (string)($r['dosya'] ?? ''),
            'sira' => (int)$r['sira'],
            'aktif' => (int)$r['aktif'],
        ];
    }
    return $sonuc;
}
