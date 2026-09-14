<?php
declare(strict_types=1);

/**
 * Auth Genişletme - Eğitim Grubu Desteği
 * 
 * Bu fonksiyonlar mevcut auth.php'ye ek olarak kullanılır.
 * attempt_login() fonksiyonu education_groups tablosundan
 * kullanıcının eğitim grubunu belirler.
 */

require_once dirname(__DIR__) . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Kullanıcının eğitim gruplarını getir
 */
function user_education_groups(int $user_id, string $role = 'kursiyer'): array {
    if ($role !== 'kursiyer') return [];
    
    $pdo = db();
    try {
        $st = $pdo->prepare("
            SELECT eg.id, eg.slug, eg.name, eg.icon 
            FROM user_groups ug
            JOIN education_groups eg ON eg.id = ug.group_id AND eg.aktif = 1
            WHERE ug.user_id = ?
            ORDER BY eg.sort_order, eg.id
        ");
        $st->execute([$user_id]);
        return $st->fetchAll();
    } catch (Throwable) {
        return [];
    }
}

/**
 * Kullanıcının bir gruba ait olup olmadığını kontrol et
 */
function user_in_group(int $user_id, string $group_slug): bool {
    $groups = user_education_groups($user_id);
    foreach ($groups as $g) {
        if ($g['slug'] === $group_slug) return true;
    }
    return false;
}

/**
 * Kullanıcının birincil eğitim grubu
 * Eğer birden fazla grubuna aitse, ilk grup döner
 */
function user_primary_group(int $user_id, string $role = 'kursiyer'): ?array {
    $groups = user_education_groups($user_id, $role);
    return $groups[0] ?? null;
}

/**
 * Kullanıcıyı eğitim grubuna ata
 */
function assign_user_to_group(int $user_id, int $group_id): bool {
    $pdo = db();
    try {
        $st = $pdo->prepare("INSERT IGNORE INTO user_groups (user_id, group_id) VALUES (?, ?)");
        return $st->execute([$user_id, $group_id]);
    } catch (Throwable) {
        return false;
    }
}

/**
 * Kullanıcıyı eğitim grubundan çıkar
 */
function remove_user_from_group(int $user_id, int $group_id): bool {
    $pdo = db();
    try {
        $st = $pdo->prepare("DELETE FROM user_groups WHERE user_id = ? AND group_id = ?");
        return $st->execute([$user_id, $group_id]);
    } catch (Throwable) {
        return false;
    }
}

/**
 * Eğitim grubundaki kullanıcıları getir
 */
function get_group_users(int $group_id, bool $aktif_only = true): array {
    $pdo = db();
    try {
        $sql = "
            SELECT k.id, k.ad, k.soyad, k.telefon, k.gsm, k.ehliyet_sinifi, k.aktif,
                   k.sinav_durumu, k.basaril_durumu
            FROM user_groups ug
            JOIN kursiyerler k ON k.id = ug.user_id
            WHERE ug.group_id = ?
        ";
        if ($aktif_only) $sql .= " AND k.aktif = 1";
        $sql .= " ORDER BY k.ad, k.soyad";
        $st = $pdo->prepare($sql);
        $st->execute([$group_id]);
        return $st->fetchAll();
    } catch (Throwable) {
        return [];
    }
}

/**
 * Eğitim grubunun istatistiklerini getir
 */
function group_statistics(int $group_id): array {
    $pdo = db();
    $stats = [
        'toplam_kullanici' => 0,
        'aktif_kullanici' => 0,
        'toplam_sinav' => 0,
        'basarili_sinav' => 0,
        'toplam_soru' => 0,
    ];
    
    try {
        // Kullanıcı sayısı
        $st = $pdo->prepare("SELECT COUNT(*) FROM user_groups WHERE group_id = ?");
        $st->execute([$group_id]);
        $stats['toplam_kullanici'] = (int)$st->fetchColumn();
        
        // Soru sayısı
        $st = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE group_id = ? AND aktif = 1");
        $st->execute([$group_id]);
        $stats['toplam_soru'] = (int)$st->fetchColumn();
        
        // Sınav sayısı
        $st = $pdo->prepare("SELECT COUNT(*) FROM exam_attempts ea 
                            JOIN user_groups ug ON ug.user_id = ea.user_id 
                            WHERE ug.group_id = ? AND ea.status = 'bitti'");
        $st->execute([$group_id]);
        $stats['toplam_sinav'] = (int)$st->fetchColumn();
        
        // Başarılı sınav
        $st = $pdo->prepare("SELECT COUNT(*) FROM exam_attempts ea 
                            JOIN user_groups ug ON ug.user_id = ea.user_id 
                            WHERE ug.group_id = ? AND ea.status = 'bitti' AND ea.is_passed = 1");
        $st->execute([$group_id]);
        $stats['basarili_sinav'] = (int)$st->fetchColumn();
        
    } catch (Throwable) {
    }
    
    return $stats;
}
