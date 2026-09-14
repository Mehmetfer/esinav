<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC Deneme Sınavları';
$activeMenu = 'src-deneme';
require __DIR__ . '/_layout_top.php';

$pdo = db();
src_ensure_tables($pdo);

// Toplam soru sayısı
$toplamSoru = 0;
try {
    $toplamSoru = (int)$pdo->query("SELECT COUNT(*) FROM src_sorular WHERE aktif = 1")->fetchColumn();
} catch (Throwable) {
}
?>
  <h1 class="k-page-title">SRC Deneme Sınavları</h1>

  <section class="k-card k-exam-start">
    <div class="k-timer-box">
      <div class="k-timer-label">SÜRE</div>
      <div class="k-timer-big">00 : 45 : 00</div>
    </div>

    <?php if ($toplamSoru >= 50): ?>
      <form method="post" action="src-baslat.php">
        <button type="submit" class="k-start-btn">DENEME SINAvi BAŞLAT</button>
      </form>
      <div class="k-info-box">
        <strong>Bilgi:</strong> Deneme sınavı 50 sorudan oluşur ve süre 45 dakikadır.
        <br>Toplam <?= $toplamSoru ?> soru havuzundan rastgele seçilecektir.
      </div>
    <?php else: ?>
      <div class="k-info-box" style="background:#fef3c7;color:#92400e">
        <strong>Yetersiz soru:</strong> Deneme sınavı için en az 50 soru gerekli. Şu anda <?= $toplamSoru ?> soru mevcut.
      </div>
    <?php endif; ?>
  </section>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
