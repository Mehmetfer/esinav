<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC Konulu Sınavlar';
$activeMenu = 'src-konu-sinavlari';
require __DIR__ . '/_layout_top.php';

$pdo = db();
src_ensure_tables($pdo);

$konular = src_konular();
$seciliKonu = (int)($_GET['kID'] ?? 0);
$aktifOturum = null;

if ($seciliKonu > 0) {
    $aktifOturum = src_aktif_oturum($pdo, (int)(current_user()['id'] ?? 0), 'konulu', $seciliKonu);
}
?>
  <h1 class="k-page-title">SRC Konulu Sınavlar</h1>

  <?php if ($seciliKonu > 0 && $aktifOturum): ?>
    <div style="margin-bottom:16px">
      <a href="/kursiyer/src-konu-sinavlari.php" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#e2e8f0;color:#334155;border-radius:8px;text-decoration:none;font-weight:700;font-size:.9rem">← Konu Listesine Dön</a>
    </div>
    <section class="k-card k-exam-start">
      <div class="k-timer-box">
        <div class="k-timer-label">SÜRE</div>
        <div class="k-timer-big">00 : 45 : 00</div>
      </div>
      <a class="k-start-btn" href="src-soru.php?q=1">DEVAM ET</a>
      <div class="k-info-box">Devam eden bir konulu sınavınız var.</div>
    </section>
  <?php elseif ($seciliKonu > 0): ?>
    <div style="margin-bottom:16px">
      <a href="/kursiyer/src-konu-sinavlari.php" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#e2e8f0;color:#334155;border-radius:8px;text-decoration:none;font-weight:700;font-size:.9rem">← Konu Listesine Dön</a>
    </div>
    <section class="k-card k-exam-start">
      <div class="k-timer-box">
        <div class="k-timer-label">SÜRE</div>
        <div class="k-timer-big">00 : 45 : 00</div>
      </div>
      <form method="post" action="src-baslat.php?tip=konulu&kID=<?= $seciliKonu ?>">
        <button type="submit" class="k-start-btn">KONULU SINAvi BAŞLAT</button>
      </form>
      <div class="k-info-box">
        <strong>Bilgi:</strong> Bu konudan <?= (int)($konular[$seciliKonu]['soru_sayi'] ?? 0) ?> soru ile sınav başlatılacak.
      </div>
    </section>
  <?php else: ?>
    <section class="k-card">
      <div style="display:grid;gap:10px">
        <?php foreach ($konular as $k): ?>
          <?php if ((int)($k['soru_sayi'] ?? 0) > 0): ?>
            <a href="/kursiyer/src-konu-sinavlari.php?kID=<?= (int)$k['id'] ?>" style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;background:#f8fafc;border-radius:10px;text-decoration:none;color:var(--navy);transition:all .2s;border:1px solid #e2e8f0">
              <div>
                <div style="font-weight:700"><?= e($k['ad']) ?></div>
                <div style="font-size:.8rem;color:var(--muted);margin-top:2px"><?= (int)$k['soru_sayi'] ?> soru mevcut</div>
              </div>
              <span style="font-size:1.2rem;color:var(--navy)">→</span>
            </a>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
