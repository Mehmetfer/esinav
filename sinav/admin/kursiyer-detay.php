<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/aktivite.php';
require_once dirname(__DIR__) . '/includes/esinav.php';

$id = (int)($_GET['id'] ?? 0);
$pdo = db();
aktivite_ensure_table($pdo);
esinav_ensure_tables($pdo);

$stmt = $pdo->prepare('SELECT * FROM kursiyerler WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$k = $stmt->fetch();
if (!$k) {
    http_response_code(404);
    echo 'Kursiyer bulunamadi';
    exit;
}

$pageTitle = 'Kursiyer Detay';
$activeMenu = 'kursiyer-detay';

$aktiviteler = $pdo->prepare('SELECT * FROM kursiyer_aktivite WHERE kursiyer_id = ? ORDER BY id DESC LIMIT 200');
$aktiviteler->execute([$id]);
$akt = $aktiviteler->fetchAll();

$sinavlar = $pdo->prepare(
    "SELECT * FROM esinav_oturum WHERE kursiyer_id = ? AND durum = 'bitti' ORDER BY id DESC LIMIT 50"
);
$sinavlar->execute([$id]);
$sinavRows = $sinavlar->fetchAll();

$ozet = [
    'giris' => 0,
    'ekitap' => 0,
    'video' => 0,
    'animasyon' => 0,
    'esinav' => count($sinavRows),
];
foreach ($akt as $a) {
    $t = (string)$a['tip'];
    if ($t === 'giris') {
        $ozet['giris']++;
    }
    if ($t === 'ekitap') {
        $ozet['ekitap']++;
    }
    if ($t === 'video') {
        $ozet['video']++;
    }
    if ($t === 'animasyon') {
        $ozet['animasyon']++;
    }
}

$sonGiris = null;
foreach ($akt as $a) {
    if ($a['tip'] === 'giris') {
        $sonGiris = $a['created_at'];
        break;
    }
}

require __DIR__ . '/_layout_top.php';
?>
  <div class="card" style="margin-bottom:14px">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start">
      <div>
        <h2 style="margin:0 0 6px;color:var(--navy)"><?= e(trim($k['ad'].' '.$k['soyad'])) ?></h2>
        <div style="color:var(--muted);font-size:.9rem">
          GSM: <?= e($k['telefon']) ?> · Sınıf: <?= e((string)($k['ehliyet_sinifi'] ?: '—')) ?>
        </div>
        <div style="color:var(--muted);font-size:.85rem;margin-top:4px">
          Son giriş: <?= $sonGiris ? e(date('d.m.Y H:i:s', strtotime((string)$sonGiris))) : '—' ?>
        </div>
      </div>
      <a href="/admin/kursiyerler.php" class="btn-link">← Listeye dön</a>
    </div>
  </div>

  <div class="stats">
    <div class="stat"><div class="label">Giriş sayısı</div><div class="value"><?= $ozet['giris'] ?></div></div>
    <div class="stat"><div class="label">E-Kitap açma</div><div class="value"><?= $ozet['ekitap'] ?></div></div>
    <div class="stat"><div class="label">Video</div><div class="value"><?= $ozet['video'] ?></div></div>
    <div class="stat"><div class="label">Animasyon</div><div class="value"><?= $ozet['animasyon'] ?></div></div>
    <div class="stat"><div class="label">E-Sınav</div><div class="value"><?= $ozet['esinav'] ?></div></div>
  </div>

  <div class="card" style="margin-bottom:14px">
    <h3 style="margin-top:0;color:var(--navy)">E-Sınav Sonuçları</h3>
    <div style="overflow-x:auto">
      <table style="width:100%;border-collapse:collapse;font-size:.88rem">
        <thead>
          <tr style="text-align:left;border-bottom:2px solid #e5e7eb;color:var(--muted)">
            <th style="padding:8px">Başlangıç</th>
            <th style="padding:8px">Bitiş</th>
            <th style="padding:8px">Puan</th>
            <th style="padding:8px">Doğru</th>
            <th style="padding:8px">Sonuç</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$sinavRows): ?>
          <tr><td colspan="5" style="padding:12px;color:var(--muted)">E-Sınav kaydı yok.</td></tr>
        <?php else: foreach ($sinavRows as $s): ?>
          <tr style="border-bottom:1px solid #f3f4f6">
            <td style="padding:8px"><?= e(date('d.m.Y H:i', strtotime((string)$s['baslangic']))) ?></td>
            <td style="padding:8px"><?= $s['bitis'] ? e(date('d.m.Y H:i', strtotime((string)$s['bitis']))) : '—' ?></td>
            <td style="padding:8px;font-weight:700"><?= (int)$s['puan'] ?></td>
            <td style="padding:8px"><?= (int)$s['dogru_sayisi'] ?>/50</td>
            <td style="padding:8px"><?= (int)$s['basarili'] === 1 ? 'Başarılı' : 'Başarısız' ?></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <h3 style="margin-top:0;color:var(--navy)">Tüm Aktiviteler</h3>
    <div style="overflow-x:auto">
      <table style="width:100%;border-collapse:collapse;font-size:.88rem">
        <thead>
          <tr style="text-align:left;border-bottom:2px solid #e5e7eb;color:var(--muted)">
            <th style="padding:8px">Tarih/Saat</th>
            <th style="padding:8px">Tip</th>
            <th style="padding:8px">İşlem</th>
            <th style="padding:8px">Detay</th>
            <th style="padding:8px">Puan</th>
            <th style="padding:8px">IP</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$akt): ?>
          <tr><td colspan="6" style="padding:12px;color:var(--muted)">Aktivite yok.</td></tr>
        <?php else: foreach ($akt as $a): ?>
          <tr style="border-bottom:1px solid #f3f4f6">
            <td style="padding:8px;white-space:nowrap"><?= e(date('d.m.Y H:i:s', strtotime((string)$a['created_at']))) ?></td>
            <td style="padding:8px"><?= e(aktivite_tip_etiket((string)$a['tip'])) ?></td>
            <td style="padding:8px"><?= e($a['baslik']) ?></td>
            <td style="padding:8px;color:#64748b"><?= e((string)($a['detay'] ?? '—')) ?></td>
            <td style="padding:8px"><?= $a['puan'] !== null ? (int)$a['puan'] : '—' ?></td>
            <td style="padding:8px;font-size:.8rem;color:#94a3b8"><?= e((string)($a['ip'] ?? '—')) ?></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>

