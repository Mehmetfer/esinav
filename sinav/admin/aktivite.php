<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/aktivite.php';
require_once dirname(__DIR__) . '/includes/esinav.php';

$pageTitle = 'Kursiyer Aktiviteleri';
$activeMenu = 'aktivite';

$pdo = db();
aktivite_ensure_table($pdo);
esinav_ensure_tables($pdo);

$filtreTip = trim((string)($_GET['tip'] ?? ''));
$q = trim((string)($_GET['q'] ?? ''));

$sql = 'SELECT a.*, k.ad, k.soyad, k.telefon
        FROM kursiyer_aktivite a
        INNER JOIN kursiyerler k ON k.id = a.kursiyer_id
        WHERE 1=1';
$params = [];
if ($filtreTip !== '') {
    $sql .= ' AND a.tip = ?';
    $params[] = $filtreTip;
}
if ($q !== '') {
    $sql .= ' AND (k.ad LIKE ? OR k.soyad LIKE ? OR k.telefon LIKE ? OR a.baslik LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like);
}
$sql .= ' ORDER BY a.id DESC LIMIT 300';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Son E-Sinav ozet
$esinavlar = $pdo->query(
    "SELECT o.id, o.kursiyer_id, o.baslangic, o.bitis, o.puan, o.dogru_sayisi, o.basarili,
            k.ad, k.soyad, k.telefon
     FROM esinav_oturum o
     INNER JOIN kursiyerler k ON k.id = o.kursiyer_id
     WHERE o.durum = 'bitti'
     ORDER BY o.id DESC LIMIT 50"
)->fetchAll();

require __DIR__ . '/_layout_top.php';
?>
  <div class="card" style="margin-bottom:16px">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center">
      <h2 style="margin:0;color:var(--navy)">Kursiyer Aktivite Raporu</h2>
      <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">
        <input name="q" value="<?= e($q) ?>" placeholder="Ad / Telefon ara"
               style="padding:8px 10px;border:1px solid #d1d5db;border-radius:6px">
        <select name="tip" style="padding:8px 10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Tüm tipler</option>
          <?php foreach (['giris','cikis','ekitap','animasyon','video','konu_sinav','deneme_sinav','esinav_basla','esinav_bitir','anasayfa','sayfa'] as $t): ?>
            <option value="<?= $t ?>" <?= $filtreTip === $t ? 'selected' : '' ?>><?= e(aktivite_tip_etiket($t)) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" style="padding:8px 14px;border:0;border-radius:6px;background:var(--meb-red);color:#fff;font-weight:700;cursor:pointer">Filtrele</button>
      </form>
    </div>
  </div>

  <div class="card" style="margin-bottom:16px">
    <h3 style="margin-top:0;color:var(--navy)">Son E-Sınav Sonuçları</h3>
    <div style="overflow-x:auto">
      <table style="width:100%;border-collapse:collapse;font-size:.88rem">
        <thead>
          <tr style="text-align:left;border-bottom:2px solid #e5e7eb;color:var(--muted)">
            <th style="padding:8px">Tarih/Saat</th>
            <th style="padding:8px">Kursiyer</th>
            <th style="padding:8px">Telefon</th>
            <th style="padding:8px">Puan</th>
            <th style="padding:8px">Doğru</th>
            <th style="padding:8px">Sonuç</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$esinavlar): ?>
          <tr><td colspan="6" style="padding:14px;color:var(--muted)">Henüz tamamlanan E-Sınav yok.</td></tr>
        <?php else: foreach ($esinavlar as $e): ?>
          <tr style="border-bottom:1px solid #f3f4f6">
            <td style="padding:8px"><?= e(date('d.m.Y H:i', strtotime((string)$e['baslangic']))) ?></td>
            <td style="padding:8px">
              <a href="/admin/kursiyer-detay.php?id=<?= (int)$e['kursiyer_id'] ?>"><?= e(trim($e['ad'].' '.$e['soyad'])) ?></a>
            </td>
            <td style="padding:8px"><?= e($e['telefon'] ?? '—') ?></td>
            <td style="padding:8px;font-weight:700"><?= (int)$e['puan'] ?></td>
            <td style="padding:8px"><?= (int)$e['dogru_sayisi'] ?>/50</td>
            <td style="padding:8px"><?= (int)$e['basarili'] === 1 ? 'Başarılı' : 'Başarısız' ?></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <h3 style="margin-top:0;color:var(--navy)">Aktivite Günlüğü</h3>
    <div style="overflow-x:auto">
      <table style="width:100%;border-collapse:collapse;font-size:.88rem">
        <thead>
          <tr style="text-align:left;border-bottom:2px solid #e5e7eb;color:var(--muted)">
            <th style="padding:8px">Tarih/Saat</th>
            <th style="padding:8px">Kursiyer</th>
            <th style="padding:8px">Telefon</th>
            <th style="padding:8px">Tip</th>
            <th style="padding:8px">İşlem</th>
            <th style="padding:8px">Detay</th>
            <th style="padding:8px">Puan</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="7" style="padding:14px;color:var(--muted)">Kayıt yok.</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr style="border-bottom:1px solid #f3f4f6">
            <td style="padding:8px;white-space:nowrap"><?= e(date('d.m.Y H:i:s', strtotime((string)$r['created_at']))) ?></td>
            <td style="padding:8px">
              <a href="/admin/kursiyer-detay.php?id=<?= (int)$r['kursiyer_id'] ?>"><?= e(trim($r['ad'].' '.$r['soyad'])) ?></a>
            </td>
            <td style="padding:8px"><?= e($r['telefon'] ?? '—') ?></td>
            <td style="padding:8px"><?= e(aktivite_tip_etiket((string)$r['tip'])) ?></td>
            <td style="padding:8px"><?= e($r['baslik']) ?></td>
            <td style="padding:8px;color:#64748b"><?= e((string)($r['detay'] ?? '—')) ?></td>
            <td style="padding:8px"><?= $r['puan'] !== null ? (int)$r['puan'] : '—' ?></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>

