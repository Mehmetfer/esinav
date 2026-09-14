<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/esinav.php';
require_once dirname(__DIR__) . '/includes/dil.php';

start_app_session();
$userEarly = current_user();
if (!empty($_SESSION['demo_mode'])) {
    redirect('/mobil/demo/esinav.php');
}
dil_baslat((string)($userEarly['tc'] ?? ''));

$pageTitle = __('esinav');
$activeMenu = 'esinav';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$kursiyerId = (int)($user['id'] ?? 0);
$error = '';
$aktif = null;
$gecmis = [];

start_app_session();
if (!empty($_SESSION['esinav_error'])) {
    $error = (string)$_SESSION['esinav_error'];
    unset($_SESSION['esinav_error']);
}
try {
    $pdo = db();
    esinav_ensure_tables($pdo);
    esinav_seed_if_needed($pdo);
    $aktif = esinav_aktif_oturum($pdo, $kursiyerId);
    $stmt = $pdo->prepare(
        "SELECT id, baslangic, bitis, puan, dogru_sayisi, basarili, durum
         FROM esinav_oturum
         WHERE kursiyer_id = ? AND durum = 'bitti'
         ORDER BY id DESC LIMIT 20"
    );
    $stmt->execute([$kursiyerId]);
    $gecmis = $stmt->fetchAll();
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
  <h1 class="k-page-title"><?= e(__('esinav')) ?></h1>

  <?php if ($error !== ''): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
  <?php endif; ?>

  <section class="k-card k-exam-start">
    <div class="k-timer-box">
      <div class="k-timer-label"><?= e(__('sure')) ?></div>
      <div class="k-timer-big">00 : 45 : 00</div>
    </div>

    <?php if ($aktif): ?>
      <a class="k-start-btn" href="e-sinav-soru.php?q=1"><?= e(__('devam_et')) ?></a>
      <div class="k-info-box"><?= e(__('devam_info')) ?></div>
    <?php else: ?>
      <form method="post" action="e-sinav-baslat.php" id="baslatForm">
        <button type="submit" class="k-start-btn" id="baslaBtn"><?= e(__('basla')) ?></button>
      </form>
      <div class="k-info-box">
        <?= e(__('onemli')) ?>: <?= e(__('basla_info')) ?>
      </div>
    <?php endif; ?>
  </section>

  <section class="k-card">
    <div class="k-card-head">
      <h2 class="k-section-title"><?= e(__('gecmis')) ?></h2>
    </div>
    <div class="k-table-wrap">
      <table class="k-table">
        <thead>
          <tr>
            <th>No</th>
            <th><?= dil_ar() ? 'التاريخ' : 'Tarih' ?></th>
            <th><?= e(__('not')) ?></th>
            <th><?= e(__('sonuc')) ?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$gecmis): ?>
          <tr><td colspan="5" class="empty"><?= dil_ar() ? 'لا توجد اختبارات بعد' : 'Henüz E-Sınav kaydı yok' ?></td></tr>
        <?php else: foreach ($gecmis as $i => $g): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= e(date('d.m.Y H:i', strtotime((string)$g['baslangic']))) ?></td>
            <td><?= e((string)($g['puan'] ?? '—')) ?></td>
            <td><?= ((int)($g['basarili'] ?? 0) === 1) ? e(__('gecti')) : e(__('kaldi')) ?></td>
            <td><a class="k-btn-sm" href="e-sinav-sonuc.php?id=<?= (int)$g['id'] ?>"><?= e(__('sonuc')) ?></a></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </section>
<style>
#geriSayim{display:none;position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.94);align-items:center;justify-content:center;flex-direction:column;gap:10px}
#geriSayimSayi{color:#fff;font-size:8rem;font-weight:900;line-height:1;font-variant-numeric:tabular-nums}
#geriSayimSayi.zoom{animation:esz .95s ease}
#geriSayimAlt{color:#fbbf24;font-weight:700;letter-spacing:.2em;font-size:.9rem}
@keyframes esz{0%{transform:scale(.3);opacity:0}25%{transform:scale(1.1);opacity:1}100%{transform:scale(1);opacity:1}}
@media (max-width:576px){#geriSayimSayi{font-size:5rem}}
</style>
<div id="geriSayim" role="alert"><div id="geriSayimSayi">3</div><div id="geriSayimAlt">SINAV BASLIYOR</div></div>
<script>(function(){var form=document.getElementById('baslatForm');if(!form)return;form.addEventListener('submit',function(ev){ev.preventDefault();var ov=document.getElementById('geriSayim');var sayi=document.getElementById('geriSayimSayi');var alt=document.getElementById('geriSayimAlt');var btn=document.getElementById('baslaBtn');if(btn){btn.disabled=true;}ov.style.display='flex';var a=['3','2','1'];var i=0;function z(t){sayi.textContent=t;sayi.classList.remove('zoom');void sayi.offsetWidth;sayi.classList.add('zoom');}function g(){if(i<a.length){z(a[i]);i++;setTimeout(g,1000);}else{sayi.style.fontSize='3rem';z('SINAV BASLIYOR');alt.textContent='BASARILAR';setTimeout(function(){form.submit();},1100);}}g();});})();</script>
<?php require __DIR__ . '/_layout_bottom.php'; ?>

