<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'İlerleme Takibi';
$activeMenu = 'raporlar';

$pdo = db();

$arama = trim((string)($_GET['q'] ?? ''));
$ehliyet = (string)($_GET['ehliyet'] ?? '');

$where = ['1=1'];
$params = [];
if ($arama !== '') {
    $like = '%' . $arama . '%';
    $digits = preg_replace('/\D+/', '', $arama) ?? '';
    $where[] = '(ad LIKE ? OR soyad LIKE ? OR telefon LIKE ? OR telefon = ?)';
    array_push($params, $like, $like, $like, $digits);
}
if ($ehliyet !== '') {
    $where[] = 'ehliyet_sinifi = ?';
    $params[] = $ehliyet;
}
$sqlWhere = implode(' AND ', $where);

$kursiyerler = [];
try {
    $st = $pdo->prepare("SELECT id, ad, soyad, telefon, ehliyet_sinifi, aktif FROM kursiyerler WHERE {$sqlWhere} ORDER BY ad, soyad, id");
    $st->execute($params);
    $kursiyerler = $st->fetchAll();
} catch (Throwable) {
}

// Grup bazli toplam icerik (video/kitap)
$grupVideo = [];
$grupKitap = [];
try {
    foreach ($pdo->query("SELECT group_id, COUNT(*) c FROM videos WHERE aktif = 1 GROUP BY group_id")->fetchAll() as $r) {
        $grupVideo[(int)$r['group_id']] = (int)$r['c'];
    }
    foreach ($pdo->query("SELECT group_id, COUNT(*) c FROM books WHERE aktif = 1 GROUP BY group_id")->fetchAll() as $r) {
        $grupKitap[(int)$r['group_id']] = (int)$r['c'];
    }
} catch (Throwable) {
}

$ilerleme = [];
foreach ($kursiyerler as $k) {
    $kid = (int)$k['id'];

    // E-Sinav
    $es = ['toplam' => 0, 'basarili' => 0, 'son' => null];
    try {
        $r = $pdo->prepare("SELECT COUNT(*) t, SUM(basarili) b, MAX(puan) p FROM esinav_oturum WHERE kursiyer_id = ? AND durum = 'bitti'");
        $r->execute([$kid]);
        $x = $r->fetch();
        $es = ['toplam' => (int)($x['t'] ?? 0), 'basarili' => (int)($x['b'] ?? 0), 'son' => $x['p'] !== null ? (int)$x['p'] : null];
    } catch (Throwable) {}

    // SRC
    $src = ['toplam' => 0, 'basarili' => 0, 'son' => null];
    try {
        $r = $pdo->prepare("SELECT COUNT(*) t, SUM(basarili) b, MAX(puan) p FROM src_oturum WHERE kursiyer_id = ? AND durum = 'bitti'");
        $r->execute([$kid]);
        $x = $r->fetch();
        $src = ['toplam' => (int)($x['t'] ?? 0), 'basarili' => (int)($x['b'] ?? 0), 'son' => $x['p'] !== null ? (int)$x['p'] : null];
    } catch (Throwable) {}

    // Grup
    $grupId = 0;
    $totalVideo = 0;
    $totalKitap = 0;
    try {
        $g = $pdo->prepare("SELECT group_id FROM user_groups WHERE user_id = ? ORDER BY id");
        $g->execute([$kid]);
        foreach ($g->fetchAll(PDO::FETCH_COLUMN) as $gid) {
            $gid = (int)$gid;
            if ($grupId === 0) $grupId = $gid;
            $totalVideo += $grupVideo[$gid] ?? 0;
            $totalKitap += $grupKitap[$gid] ?? 0;
        }
    } catch (Throwable) {}

    // Video/Kitap izlenme
    $izlenen = 0; $okunan = 0;
    try {
        $izlenen = (int)$pdo->query("SELECT COUNT(*) FROM user_progress WHERE user_id = {$kid} AND content_type = 'video' AND is_completed = 1")->fetchColumn();
        $okunan = (int)$pdo->query("SELECT COUNT(*) FROM user_progress WHERE user_id = {$kid} AND content_type = 'book' AND is_completed = 1")->fetchColumn();
    } catch (Throwable) {}

    // Son giris
    $sonGiris = null;
    try {
        $r = $pdo->prepare("SELECT MAX(created_at) FROM kursiyer_aktivite WHERE kursiyer_id = ? AND tip = 'giris'");
        $r->execute([$kid]);
        $sonGiris = $r->fetchColumn() ?: null;
    } catch (Throwable) {}

    $ilerleme[] = [
        'kursiyer' => $k,
        'esinav' => $es,
        'src' => $src,
        'video' => ['izlenen' => $izlenen, 'toplam' => $totalVideo],
        'kitap' => ['okunan' => $okunan, 'toplam' => $totalKitap],
        'son_giris' => $sonGiris,
    ];
}

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">📊 Kursiyer İlerleme Takibi</h1>
    <span style="font-size:.85rem;color:var(--muted)"><?= count($ilerleme) ?> kursiyer</span>
  </div>

  <div class="card" style="margin-bottom:14px">
    <form method="get" style="display:grid;grid-template-columns:2fr 1fr auto;gap:10px;align-items:end">
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Ara (GSM / Ad / Soyad)</label>
        <input name="q" value="<?= e($arama) ?>" placeholder="Örn: 5551234567 veya isim"
               style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Ehliyet Sınıfı</label>
        <select name="ehliyet" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Tümü</option>
          <?php foreach (['B', 'A', 'A1', 'A2', 'C', 'D', 'F'] as $s): ?>
            <option value="<?= $s ?>" <?= $ehliyet === $s ? 'selected' : '' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" style="padding:10px 16px;border:0;border-radius:6px;background:#1e3a8a;color:#fff;font-weight:700;cursor:pointer">Filtrele</button>
    </form>
  </div>

  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.85rem">
      <thead>
        <tr style="background:#f8fafc;text-align:left">
          <th style="padding:10px 12px">Ad Soyad</th>
          <th style="padding:10px 12px">Telefon</th>
          <th style="padding:10px 12px">Ehliyet</th>
          <th style="padding:10px 12px;text-align:center">E-Sınav</th>
          <th style="padding:10px 12px;text-align:center">SRC</th>
          <th style="padding:10px 12px;text-align:center">Video</th>
          <th style="padding:10px 12px;text-align:center">Kitap</th>
          <th style="padding:10px 12px">Son Giriş</th>
          <th style="padding:10px 12px"></th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($ilerleme)): ?>
        <tr><td colspan="9" style="padding:20px;color:var(--muted)">Kayıt yok.</td></tr>
      <?php else: foreach ($ilerleme as $i):
          $k = $i['kursiyer'];
          $es = $i['esinav'];
          $src = $i['src'];
          $video = $i['video'];
          $kitap = $i['kitap'];
          $videoPct = $video['toplam'] > 0 ? round($video['izlenen'] / $video['toplam'] * 100) : 0;
          $kitapPct = $kitap['toplam'] > 0 ? round($kitap['okunan'] / $kitap['toplam'] * 100) : 0;
          $sonGiris = $i['son_giris'] ? date('d.m.Y H:i', strtotime((string)$i['son_giris'])) : '—';
      ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px;font-weight:600">
            <?= e(trim((string)$k['ad'] . ' ' . (string)$k['soyad'])) ?>
            <?php if ((int)$k['aktif'] !== 1): ?><span style="margin-left:6px;color:#991b1b;font-size:.7rem">(pasif)</span><?php endif; ?>
          </td>
          <td style="padding:10px 12px;font-family:monospace"><?= e((string)($k['telefon'] ?: '—')) ?></td>
          <td style="padding:10px 12px"><span style="padding:2px 8px;background:#dbeafe;color:#1e40af;border-radius:4px;font-weight:700;font-size:.78rem"><?= e((string)($k['ehliyet_sinifi'] ?: 'B')) ?></span></td>
          <td style="padding:10px 12px;text-align:center">
            <div style="font-weight:700"><?= (int)$es['toplam'] ?> deneme</div>
            <div style="font-size:.75rem;color:<?= $es['son'] !== null && $es['son'] >= 70 ? '#166534' : '#64748b' ?>"><?= $es['son'] !== null ? 'Son: ' . (int)$es['son'] : '—' ?></div>
          </td>
          <td style="padding:10px 12px;text-align:center">
            <div style="font-weight:700"><?= (int)$src['toplam'] ?> deneme</div>
            <div style="font-size:.75rem;color:<?= $src['son'] !== null && $src['son'] >= 70 ? '#166534' : '#64748b' ?>"><?= $src['son'] !== null ? 'Son: ' . (int)$src['son'] : '—' ?></div>
          </td>
          <td style="padding:10px 12px;text-align:center;min-width:90px">
            <div style="font-size:.8rem;font-weight:600"><?= (int)$video['izlenen'] ?>/<?= (int)$video['toplam'] ?></div>
            <div style="height:5px;background:#e2e8f0;border-radius:3px;margin-top:4px"><div style="height:100%;width:<?= $videoPct ?>%;background:#1e3a8a;border-radius:3px"></div></div>
          </td>
          <td style="padding:10px 12px;text-align:center;min-width:90px">
            <div style="font-size:.8rem;font-weight:600"><?= (int)$kitap['okunan'] ?>/<?= (int)$kitap['toplam'] ?></div>
            <div style="height:5px;background:#e2e8f0;border-radius:3px;margin-top:4px"><div style="height:100%;width:<?= $kitapPct ?>%;background:#16a34a;border-radius:3px"></div></div>
          </td>
          <td style="padding:10px 12px;font-size:.8rem;color:var(--muted)"><?= e($sonGiris) ?></td>
          <td style="padding:10px 12px"><a href="/admin/user-progress-detay.php?id=<?= (int)$k['id'] ?>" style="color:#1d4ed8;font-weight:700;text-decoration:none">Detay →</a></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>

