<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC';
$activeMenu = 'src';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$kursiyerId = (int)($user['id'] ?? 0);
$error = '';
$aktif = null;
$gecmis = [];

if (!empty($_SESSION['src_error'])) {
    $error = (string)$_SESSION['src_error'];
    unset($_SESSION['src_error']);
}
try {
    $pdo = db();
    src_ensure_tables($pdo);
    src_seed_if_needed($pdo);
    $aktif = src_aktif_oturum($pdo, $kursiyerId);
    $stmt = $pdo->prepare(
        "SELECT id, baslangic, bitis, puan, dogru_sayisi, basarili, durum
         FROM src_oturum WHERE kursiyer_id = ? AND durum = 'bitti'
         ORDER BY id DESC LIMIT 20"
    );
    $stmt->execute([$kursiyerId]);
    $gecmis = $stmt->fetchAll();
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
  <h1 class="k-page-title">SRC</h1>

  <?php if ($error !== ''): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
  <?php endif; ?>

  <section class="k-card k-exam-start">
    <div class="k-timer-box">
      <div class="k-timer-label">SÜRE</div>
      <div class="k-timer-big">00 : 45 : 00</div>
    </div>

    <?php if ($aktif): ?>
      <a class="k-start-btn" href="src-soru.php?q=1">DEVAM ET</a>
      <div class="k-info-box">Devam eden bir SRC sınavınız var.</div>
    <?php else: ?>
      <form method="post" action="src-baslat.php" id="baslatForm">
        <button type="submit" class="k-start-btn" id="baslaBtn">BAŞLA</button>
      </form>
      <div class="k-info-box">
        <strong>Önemli:</strong> SRC sınavı 50 sorudan oluşur ve süre 45 dakikadır.
      </div>
    <?php endif; ?>
  </section>

  <section class="k-card">
    <div class="k-card-head">
      <h2 class="k-section-title">SRC Geçmişi</h2>
    </div>
    <div class="k-table-wrap">
      <table class="k-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Tarih</th>
            <th>Not</th>
            <th>Sonuç</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$gecmis): ?>
          <tr><td colspan="5" class="empty">Henüz SRC kaydı yok</td></tr>
        <?php else: foreach ($gecmis as $i => $g): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= e(date('d.m.Y H:i', strtotime((string)$g['baslangic']))) ?></td>
            <td><?= e((string)($g['puan'] ?? '—')) ?></td>
            <td><?= ((int)($g['basarili'] ?? 0) === 1) ? 'Geçti' : 'Kaldı' ?></td>
            <td><a class="k-btn-sm" href="src-sonuc.php?id=<?= (int)$g['id'] ?>">Sonuç</a></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </section>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
