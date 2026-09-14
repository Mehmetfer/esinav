<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/esinav.php';

$pageTitle = 'E-Sınav Sonuçları';
$activeMenu = 'esinav';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$id = (int)($_GET['id'] ?? 0);
$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM esinav_oturum WHERE id = ? AND kursiyer_id = ? LIMIT 1');
$stmt->execute([$id, (int)($user['id'] ?? 0)]);
$oturum = $stmt->fetch();
if (!$oturum) {
    echo '<div class="alert alert-error">Sonuç bulunamadı.</div>';
    require __DIR__ . '/_layout_bottom.php';
    exit;
}

$ist = esinav_istatistik($pdo, $id);
$puan = (int)($oturum['puan'] ?? $ist['puan']);
$basarili = (int)($oturum['basarili'] ?? 0) === 1 || !empty($ist['basarili']);
$tarih = date('d.m.Y H:i', strtotime((string)$oturum['baslangic']));

$toplam = max(1, $ist['toplam']);
$pDogru = round(($ist['dogru'] / $toplam) * 100, 1);
$pYanlis = round(($ist['yanlis'] / $toplam) * 100, 1);
$pBos = round(($ist['bos'] / $toplam) * 100, 1);
// conic-gradient slices
$g1 = $pDogru;
$g2 = $pDogru + $pYanlis;
?>
  <link rel="stylesheet" href="../assets/css/esinav-sonuc.css?v=1">

  <h1 class="k-page-title"><?= e(__('esinav')) ?> — <?= e($tarih) ?></h1>

  <section class="rs-genel">
    <div class="rs-genel-left">
      <div class="rs-head"><?= dil_ar() ? 'عام' : 'Genel' ?></div>
      <table class="rs-table">
        <tr>
          <th><?= e(__('sonuc')) ?></th>
          <td><span class="rs-badge <?= $basarili ? 'ok' : 'fail' ?>"><?= $basarili ? e(__('gecti')) : e(__('kaldi')) ?></span></td>
        </tr>
        <tr>
          <th><?= e(__('not')) ?></th>
          <td><span class="rs-pill navy"><?= $puan ?></span></td>
        </tr>
        <tr>
          <th><?= e(__('toplam_soru')) ?></th>
          <td><span class="rs-pill blue"><?= (int)$ist['toplam'] ?></span></td>
        </tr>
        <tr>
          <th><?= e(__('dogru')) ?></th>
          <td><span class="rs-pill green"><?= (int)$ist['dogru'] ?></span></td>
        </tr>
        <tr>
          <th><?= e(__('yanlis')) ?></th>
          <td><span class="rs-pill orange"><?= (int)$ist['yanlis'] ?></span></td>
        </tr>
        <tr>
          <th><?= e(__('bos')) ?></th>
          <td><span class="rs-pill gray"><?= (int)$ist['bos'] ?></span></td>
        </tr>
      </table>
      <?php $demoMode = !empty($_SESSION['demo_mode']); ?>
      <a class="rs-btn" href="<?= $demoMode ? '/mobil/demo/esinav.php' : 'e-sinav.php' ?>"><?= $demoMode ? 'E-S\u0131nav listesi' : e(__('esinava_don')) ?></a>
    </div>
    <div class="rs-genel-right">
      <div class="rs-pie" style="background:conic-gradient(#22c55e 0 <?= $g1 ?>%, #ef4444 <?= $g1 ?>% <?= $g2 ?>%, #d1d5db <?= $g2 ?>% 100%)"></div>
      <div class="rs-legend">
        <span><i class="g"></i> <?= e(__('dogru')) ?> (<?= (int)$ist['dogru'] ?>)</span>
        <span><i class="r"></i> <?= e(__('yanlis')) ?> (<?= (int)$ist['yanlis'] ?>)</span>
        <span><i class="b"></i> <?= e(__('bos')) ?> (<?= (int)$ist['bos'] ?>)</span>
      </div>
    </div>
  </section>

  <div class="rs-dersler">
    <?php foreach ($ist['dersler'] as $d): ?>
      <section class="rs-ders">
        <div class="rs-ders-head"><?= e($d['ad']) ?></div>
        <table class="rs-mini">
          <tr><th>Toplam Soru</th><td><?= (int)$d['toplam'] ?></td></tr>
          <tr><th>Doğru Sayısı</th><td><?= (int)$d['dogru'] ?></td></tr>
          <tr><th>Yanlış Sayısı</th><td><?= (int)$d['yanlis'] ?></td></tr>
          <tr><th>Boş Sayısı</th><td><?= (int)$d['bos'] ?></td></tr>
        </table>
      </section>
    <?php endforeach; ?>
  </div>

  <p class="muted" style="margin-top:12px">
    Her doğru 2 puan · Yanlış götürmez · Geçme notu 70 ·
    Dağılım: Trafik 25 · İlkyardım 12 · Motor/Araç 12 · Trafik Adabı 1
  </p>
<?php require __DIR__ . '/_layout_bottom.php'; ?>

