<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/video_izleme.php';
require_once dirname(__DIR__) . '/data/video-dersler.php';

$pageTitle = 'Video İzleme Raporu';
$activeMenu = 'video-izleme';

$pdo = db();
video_izleme_ensure($pdo);

$q = trim((string)($_GET['q'] ?? ''));
$params = [];
$where = '1=1';
if ($q !== '') {
    $where .= ' AND (k.ad LIKE ? OR k.soyad LIKE ? OR k.tc_kimlik LIKE ? OR k.telefon LIKE ? OR v.baslik LIKE ? OR v.youtube_id LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like, $like, $like);
}

$sql = "SELECT v.*, k.ad, k.soyad, k.tc_kimlik, k.telefon
        FROM video_izleme v
        INNER JOIN kursiyerler k ON k.id = v.kursiyer_id
        WHERE {$where}
        ORDER BY v.son_izleme DESC
        LIMIT 400";
$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();

$ozet = $pdo->query(
    'SELECT COUNT(DISTINCT kursiyer_id) AS kisi,
            COUNT(*) AS kayit,
            COALESCE(SUM(sure_sn),0) AS toplam_sn
     FROM video_izleme'
)->fetch() ?: ['kisi' => 0, 'kayit' => 0, 'toplam_sn' => 0];

require __DIR__ . '/_layout_top.php';
?>
  <div class="stats" style="margin-bottom:16px">
    <div class="stat">
      <div class="label">İzleyen kursiyer</div>
      <div class="value"><?= (int)$ozet['kisi'] ?></div>
    </div>
    <div class="stat">
      <div class="label">Video kaydı</div>
      <div class="value"><?= (int)$ozet['kayit'] ?></div>
    </div>
    <div class="stat">
      <div class="label">Toplam süre</div>
      <div class="value" style="font-size:1.35rem"><?= e(video_izleme_format((int)$ozet['toplam_sn'])) ?></div>
    </div>
  </div>

  <div class="card" style="margin-bottom:14px;padding:14px">
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">
      <input name="q" value="<?= e($q) ?>" placeholder="Ad / GSM / video ara…"
             style="flex:1;min-width:200px;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
      <button type="submit" class="btn">Ara</button>
    </form>
  </div>

  <div class="card" style="overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
      <thead>
        <tr style="text-align:left;border-bottom:2px solid #e5e7eb;color:var(--muted)">
          <th style="padding:10px">Son izleme</th>
          <th style="padding:10px">Kursiyer</th>
          <th style="padding:10px">Video</th>
          <th style="padding:10px">Süre</th>
          <th style="padding:10px">Oturum</th>
          <th style="padding:10px"></th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="6" style="padding:20px;color:var(--muted)">Henüz izleme kaydı yok.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px;white-space:nowrap"><?= e((string)$r['son_izleme']) ?></td>
          <td style="padding:10px">
            <strong><?= e(trim(($r['ad'] ?? '') . ' ' . ($r['soyad'] ?? ''))) ?></strong><br>
            <span style="color:var(--muted);font-size:.8rem">
              <?= e((string)($r['telefon'] ?: $r['tc_kimlik'])) ?>
            </span>
          </td>
          <td style="padding:10px;max-width:360px"><?= e((string)$r['baslik']) ?></td>
          <td style="padding:10px;font-weight:800"><?= e(video_izleme_format((int)$r['sure_sn'])) ?></td>
          <td style="padding:10px"><?= (int)$r['oturum_sayisi'] ?></td>
          <td style="padding:10px">
            <a href="https://www.youtube.com/watch?v=<?= e(rawurlencode((string)$r['youtube_id'])) ?>" target="_blank" rel="noopener">YouTube</a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
