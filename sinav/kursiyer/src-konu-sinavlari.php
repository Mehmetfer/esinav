<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC Konulu Sınavlar';
$activeMenu = 'src-konu-sinavlari';
require __DIR__ . '/_layout_top.php';

$pdo = db();
src_ensure_tables($pdo);
src_seed_if_needed($pdo);

$konular = src_konular();
$sayilar = src_konu_sayilari($pdo);
$kursiyerId = (int)(current_user()['id'] ?? 0);

$secili = (string)($_GET['konu'] ?? '');
if ($secili !== '' && !isset($konular[$secili])) {
    $secili = '';
}

$seciliSayi = $secili !== '' ? (int)($sayilar[$secili] ?? 0) : 0;
$aktifOturum = $secili !== '' ? src_aktif_oturum($pdo, $kursiyerId, 'konulu', $secili) : null;
$digerOturum = src_aktif_oturum($pdo, $kursiyerId, null);
$toplamSoru = array_sum($sayilar);
?>
  <h1 class="k-page-title">SRC Konulu Sınavlar</h1>
  <p class="k-sign-lead">
    Ders seçerek o konuya ait <?= (int)SRC_KONULU_SORU ?> soruluk mini sınav başlatabilirsiniz.
    Süre <?= (int)(SRC_KONULU_SURE_SN / 60) ?> dakikadır, her doğru cevap <?= (int)SRC_PUAN_DOGRU ?> puandır.
    Tüm derslerde toplam <strong><?= (int)$toplamSoru ?></strong> soru bulunuyor.
  </p>

  <?php if ($secili !== '' && $aktifOturum): ?>
    <div style="margin-bottom:16px">
      <a href="/kursiyer/src-konu-sinavlari.php" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#e2e8f0;color:#334155;border-radius:8px;text-decoration:none;font-weight:700;font-size:.9rem">← Konu Listesine Dön</a>
    </div>
    <section class="k-card k-exam-start">
      <div class="k-timer-box">
        <div class="k-timer-label">SÜRE</div>
        <div class="k-timer-big" id="srcTimer" data-kalan="<?= (int)src_kalan_sn($aktifOturum) ?>">00 : 00 : 00</div>
      </div>
      <a class="k-start-btn" href="src-soru.php?q=1">DEVAM ET</a>
      <div class="k-info-box">
        <strong><?= e($konular[$secili]) ?></strong> konusunda devam eden sınavınız var.
      </div>
    </section>

  <?php elseif ($secili !== ''): ?>
    <div style="margin-bottom:16px">
      <a href="/kursiyer/src-konu-sinavlari.php" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#e2e8f0;color:#334155;border-radius:8px;text-decoration:none;font-weight:700;font-size:.9rem">← Konu Listesine Dön</a>
    </div>
    <section class="k-card k-exam-start">
      <div class="k-timer-box">
        <div class="k-timer-label">SÜRE</div>
        <div class="k-timer-big"><?= sprintf('%02d : %02d : 00', intdiv((int)SRC_KONULU_SURE_SN, 60), (int)SRC_KONULU_SURE_SN % 60) ?></div>
      </div>

      <?php if ($seciliSayi < 1): ?>
        <div class="alert alert-error">Bu konuda henüz soru bulunmuyor. Lütfen başka bir konu seçin.</div>
      <?php else: ?>
        <?php if ($digerOturum): ?>
          <div class="alert alert-warning" style="margin-bottom:12px">
            Devam eden başka bir sınavınız var. Yeni sınav başlattığınızda o sınav sonlandırılır.
          </div>
        <?php endif; ?>
        <form method="post" action="src-baslat.php?tip=konulu&amp;konu=<?= e($secili) ?>">
          <button type="submit" class="k-start-btn">KONULU SINAVI BAŞLAT</button>
        </form>
        <div class="k-info-box">
          <strong>Bilgi:</strong> <em><?= e($konular[$secili]) ?></em> konusundan
          <?= (int)min($seciliSayi, (int)SRC_KONULU_SORU) ?> soru ile sınav başlatılacak.
        </div>
      <?php endif; ?>
    </section>

  <?php else: ?>
    <section class="k-card">
      <div style="display:grid;gap:10px">
        <?php foreach ($konular as $slug => $ad): ?>
          <?php $adet = (int)($sayilar[$slug] ?? 0); ?>
          <a href="/kursiyer/src-konu-sinavlari.php?konu=<?= e($slug) ?>"
             style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;background:<?= $adet > 0 ? '#f8fafc' : '#fbfbfc' ?>;border-radius:10px;text-decoration:none;color:var(--navy);border:1px solid #e2e8f0<?= $adet > 0 ? '' : ';opacity:.6' ?>">
            <div>
              <div style="font-weight:700"><?= e($ad) ?></div>
              <div style="font-size:.8rem;color:var(--muted);margin-top:2px">
                <?= $adet > 0 ? $adet . ' soru mevcut' : 'Soru eklenmedi' ?>
              </div>
            </div>
            <span style="font-size:1.2rem;color:var(--navy)">→</span>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($secili !== '' && $aktifOturum): ?>
  <script>
  (function () {
    var el = document.getElementById('srcTimer');
    if (!el) return;
    var kalan = parseInt(el.getAttribute('data-kalan') || '0', 10);
    function goster() {
      if (kalan <= 0) { window.location.href = 'src-bitir.php'; return; }
      var h = Math.floor(kalan / 3600);
      var m = Math.floor((kalan % 3600) / 60);
      var s = kalan % 60;
      el.textContent = String(h).padStart(2, '0') + ' : ' + String(m).padStart(2, '0') + ' : ' + String(s).padStart(2, '0');
      kalan--;
    }
    goster();
    setInterval(goster, 1000);
  })();
  </script>
  <?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
