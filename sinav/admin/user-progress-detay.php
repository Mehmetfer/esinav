<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'İlerleme Detayı';
$activeMenu = 'raporlar-detay';

$pdo = db();

$id = (int)($_GET['id'] ?? 0);
$kursiyer = null;
if ($id > 0) {
    $st = $pdo->prepare("SELECT id, ad, soyad, telefon, ehliyet_sinifi, aktif, created_at FROM kursiyerler WHERE id = ?");
    $st->execute([$id]);
    $kursiyer = $st->fetch();
}
if (!$kursiyer) {
    redirect('/admin/user-progress.php');
}

$esinav = [];
try {
    $st = $pdo->prepare("SELECT * FROM esinav_oturum WHERE kursiyer_id = ? ORDER BY baslangic DESC LIMIT 200");
    $st->execute([$id]);
    $esinav = $st->fetchAll();
} catch (Throwable) {}

$src = [];
try {
    $st = $pdo->prepare("SELECT * FROM src_oturum WHERE kursiyer_id = ? ORDER BY baslangic DESC LIMIT 200");
    $st->execute([$id]);
    $src = $st->fetchAll();
} catch (Throwable) {}

$konular = src_konular();

$videolar = [];
try {
    $videolar = $pdo->query("SELECT v.title, up.progress_percent, up.is_completed, up.completed_at FROM user_progress up JOIN videos v ON v.id = up.content_id WHERE up.user_id = {$id} AND up.content_type = 'video' ORDER BY up.updated_at DESC")->fetchAll();
} catch (Throwable) {}

$kitaplar = [];
try {
    $kitaplar = $pdo->query("SELECT b.title, up.progress_percent, up.is_completed, up.completed_at FROM user_progress up JOIN books b ON b.id = up.content_id WHERE up.user_id = {$id} AND up.content_type = 'book' ORDER BY up.updated_at DESC")->fetchAll();
} catch (Throwable) {}

$aktiviteler = [];
try {
    $st = $pdo->prepare("SELECT * FROM kursiyer_aktivite WHERE kursiyer_id = ? ORDER BY created_at DESC LIMIT 100");
    $st->execute([$id]);
    $aktiviteler = $st->fetchAll();
} catch (Throwable) {}

function fmtDate(?string $d): string
{
    return $d ? date('d.m.Y H:i', strtotime($d)) : '—';
}

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">
      👤 <?= e(trim((string)$kursiyer['ad'] . ' ' . (string)$kursiyer['soyad'])) ?>
      <span style="font-size:.8rem;color:var(--muted);font-family:monospace"><?= e((string)($kursiyer['telefon'] ?: '—')) ?></span>
    </h1>
    <a href="/admin/user-progress.php" style="padding:8px 14px;background:#e2e8f0;color:#334155;border-radius:8px;text-decoration:none;font-weight:700;font-size:.85rem">← Listeye Dön</a>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:18px">
    <div class="card" style="margin:0;text-align:center"><div style="font-size:.75rem;color:var(--muted)">Ehliyet</div><div style="font-size:1.2rem;font-weight:800;color:var(--navy)"><?= e((string)($kursiyer['ehliyet_sinifi'] ?: 'B')) ?></div></div>
    <div class="card" style="margin:0;text-align:center"><div style="font-size:.75rem;color:var(--muted)">E-Sınav</div><div style="font-size:1.2rem;font-weight:800;color:var(--navy)"><?= count($esinav) ?></div></div>
    <div class="card" style="margin:0;text-align:center"><div style="font-size:.75rem;color:var(--muted)">SRC</div><div style="font-size:1.2rem;font-weight:800;color:var(--navy)"><?= count($src) ?></div></div>
    <div class="card" style="margin:0;text-align:center"><div style="font-size:.75rem;color:var(--muted)">Video</div><div style="font-size:1.2rem;font-weight:800;color:var(--navy)"><?= count($videolar) ?></div></div>
    <div class="card" style="margin:0;text-align:center"><div style="font-size:.75rem;color:var(--muted)">Kitap</div><div style="font-size:1.2rem;font-weight:800;color:var(--navy)"><?= count($kitaplar) ?></div></div>
    <div class="card" style="margin:0;text-align:center"><div style="font-size:.75rem;color:var(--muted)">Durum</div><div style="font-size:1rem;font-weight:800;color:<?= (int)$kursiyer['aktif'] === 1 ? '#166534' : '#991b1b' ?>"><?= (int)$kursiyer['aktif'] === 1 ? 'Aktif' : 'Pasif' ?></div></div>
  </div>

  <h2 style="font-size:1rem;color:var(--navy);margin:18px 0 10px">📝 E-Sınav Oturumları</h2>
  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.85rem">
      <thead><tr style="background:#f8fafc;text-align:left"><th style="padding:10px 12px">Tarih</th><th style="padding:10px 12px">Süre</th><th style="padding:10px 12px">Puan</th><th style="padding:10px 12px">Doğru</th><th style="padding:10px 12px">Durum</th></tr></thead>
      <tbody>
      <?php if (!$esinav): ?><tr><td colspan="5" style="padding:16px;color:var(--muted)">Kayıt yok.</td></tr>
      <?php else: foreach ($esinav as $r): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px"><?= e(fmtDate((string)$r['baslangic'])) ?></td>
          <td style="padding:10px 12px"><?= gmdate('H:i:s', (int)$r['sure_sn']) ?></td>
          <td style="padding:10px 12px;font-weight:800"><?= (int)($r['puan'] ?? 0) ?></td>
          <td style="padding:10px 12px"><?= (int)($r['dogru_sayisi'] ?? 0) ?></td>
          <td style="padding:10px 12px"><span style="padding:2px 8px;border-radius:4px;font-size:.75rem;font-weight:700;<?= (int)($r['basarili'] ?? 0) === 1 ? 'background:#dcfce7;color:#166534' : 'background:#fee2e2;color:#991b1b' ?>"><?= (int)($r['basarili'] ?? 0) === 1 ? 'Başarılı' : 'Başarısız' ?></span></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <h2 style="font-size:1rem;color:var(--navy);margin:18px 0 10px">🚛 SRC Oturumları</h2>
  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.85rem">
      <thead><tr style="background:#f8fafc;text-align:left"><th style="padding:10px 12px">Tarih</th><th style="padding:10px 12px">Tip</th><th style="padding:10px 12px">Konu</th><th style="padding:10px 12px">Puan</th><th style="padding:10px 12px">Doğru</th><th style="padding:10px 12px">Durum</th></tr></thead>
      <tbody>
      <?php if (!$src): ?><tr><td colspan="6" style="padding:16px;color:var(--muted)">Kayıt yok.</td></tr>
      <?php else: foreach ($src as $r): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px"><?= e(fmtDate((string)$r['baslangic'])) ?></td>
          <td style="padding:10px 12px"><?= e((string)$r['tip']) ?></td>
          <td style="padding:10px 12px"><?= e($konular[(string)($r['konu'] ?? '')] ?? ((string)($r['konu'] ?? '') !== '' ? (string)$r['konu'] : '—')) ?></td>
          <td style="padding:10px 12px;font-weight:800"><?= (int)($r['puan'] ?? 0) ?></td>
          <td style="padding:10px 12px"><?= (int)($r['dogru_sayisi'] ?? 0) ?></td>
          <td style="padding:10px 12px"><span style="padding:2px 8px;border-radius:4px;font-size:.75rem;font-weight:700;<?= (int)($r['basarili'] ?? 0) === 1 ? 'background:#dcfce7;color:#166534' : 'background:#fee2e2;color:#991b1b' ?>"><?= (int)($r['basarili'] ?? 0) === 1 ? 'Başarılı' : 'Başarısız' ?></span></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <h2 style="font-size:1rem;color:var(--navy);margin:18px 0 10px">🔊 Video / Kitap İlerlemesi</h2>
  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.85rem">
      <thead><tr style="background:#f8fafc;text-align:left"><th style="padding:10px 12px">Tür</th><th style="padding:10px 12px">Başlık</th><th style="padding:10px 12px">İlerleme</th><th style="padding:10px 12px">Tamamlandı</th></tr></thead>
      <tbody>
      <?php
      $icerikRows = array_merge(
          array_map(function($x){ return ['tur' => '🎬 Video', 'title' => $x['title'], 'pct' => (int)$x['progress_percent'], 'done' => (int)$x['is_completed'], 'at' => $x['completed_at']]; }, $videolar),
          array_map(function($x){ return ['tur' => '📚 Kitap', 'title' => $x['title'], 'pct' => (int)$x['progress_percent'], 'done' => (int)$x['is_completed'], 'at' => $x['completed_at']]; }, $kitaplar),
      );
      if (!$icerikRows): ?>
        <tr><td colspan="4" style="padding:16px;color:var(--muted)">Kayıt yok.</td></tr>
      <?php else: foreach ($icerikRows as $r): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px"><?= $r['tur'] ?></td>
          <td style="padding:10px 12px"><?= e((string)$r['title']) ?></td>
          <td style="padding:10px 12px;min-width:120px">
            <div style="height:6px;background:#e2e8f0;border-radius:3px"><div style="height:100%;width:<?= (int)$r['pct'] ?>%;background:#1e3a8a;border-radius:3px"></div></div>
          </td>
          <td style="padding:10px 12px"><?= $r['done'] ? '✔ ' . e(fmtDate((string)$r['at'])) : '—' ?></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <h2 style="font-size:1rem;color:var(--navy);margin:18px 0 10px">🕓 Aktivite Geçmişi</h2>
  <div class="card" style="padding:0;overflow:auto;max-height:420px">
    <table style="width:100%;border-collapse:collapse;font-size:.83rem">
      <tbody>
      <?php if (!$aktiviteler): ?><tr><td style="padding:16px;color:var(--muted)">Kayıt yok.</td></tr>
      <?php else: foreach ($aktiviteler as $r): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:8px 12px;white-space:nowrap;color:var(--muted)"><?= e(fmtDate((string)$r['created_at'])) ?></td>
          <td style="padding:8px 12px;font-weight:600"><?= e((string)$r['baslik']) ?></td>
          <td style="padding:8px 12px;color:var(--muted)"><?= e((string)($r['detay'] ?? '')) ?></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
