<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC Çıkmış Sorular';
$activeMenu = 'src-cikmis-sorular';
require __DIR__ . '/_layout_top.php';

$pdo = db();
src_ensure_tables($pdo);
$konular = src_konular();

$ders = (string)($_GET['ders'] ?? '');
if (!isset($konular[$ders])) {
    $ders = '';
}

$sorular = [];
try {
    if ($ders !== '') {
        $st = $pdo->prepare("SELECT * FROM src_sorular WHERE ders = ? AND aktif = 1 ORDER BY id");
        $st->execute([$ders]);
    } else {
        $st = $pdo->query("SELECT * FROM src_sorular WHERE aktif = 1 ORDER BY ders, id");
    }
    $sorular = $st->fetchAll();
} catch (Throwable) {
}

$secenekKeys = ['A' => 'secenek_a', 'B' => 'secenek_b', 'C' => 'secenek_c', 'D' => 'secenek_d'];
?>
  <h1 class="k-page-title">SRC Çıkmış Sorular</h1>
  <p class="k-sign-lead">
    Soru havuzundaki tüm soruları konularına göre inceleyebilirsiniz. Doğru cevap yeşil renkte gösterilir.
    Sınav pratiği için <a href="/kursiyer/src-deneme-sinavlari.php" style="color:#1d4ed8;font-weight:700">Deneme Sınavı</a>'na geçebilirsiniz.
  </p>

  <?php $toplam = count($sorular); ?>

  <div class="k-filters" style="margin-bottom:16px">
    <a class="k-chip <?= $ders === '' ? 'active' : '' ?>" href="src-cikmis-sorular.php">Tümü</a>
    <?php foreach ($konular as $k => $ad): ?>
      <a class="k-chip <?= $ders === $k ? 'active' : '' ?>" href="src-cikmis-sorular.php?ders=<?= e($k) ?>"><?= e($ad) ?></a>
    <?php endforeach; ?>
  </div>

  <p class="k-sign-count"><?= (int)$toplam ?> soru<?= $ders !== '' ? ' · ' . e($konular[$ders] ?? '') : '' ?></p>

  <?php if (empty($sorular)): ?>
    <div class="k-empty"><div class="k-empty-illu">📝</div><p>Bu konuya ait soru bulunamadı.</p></div>
  <?php else: ?>
    <div style="display:grid;gap:14px">
      <?php foreach ($sorular as $i => $s):
          $dogru = strtoupper((string)$s['dogru']);
      ?>
        <section class="k-sign-guide-card" style="margin:0">
          <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px">
            <strong style="font-size:.85rem;color:var(--navy)">Soru <?= (int)($i + 1) ?></strong>
            <span class="k-badge" style="background:#eff6ff;color:#1e3a8a;padding:2px 8px;border-radius:6px;font-size:.72rem;font-weight:700"><?= e($konular[$s['ders']] ?? (string)$s['ders']) ?></span>
          </div>
          <div style="font-size:.95rem;font-weight:600;color:#1f2937;margin-bottom:12px;line-height:1.5"><?= e((string)$s['soru']) ?></div>
          <div style="display:grid;gap:8px">
            <?php foreach ($secenekKeys as $h => $col): ?>
              <div style="padding:10px 14px;border-radius:8px;<?= $h === $dogru ? 'background:#dcfce7;border:1px solid #86efac;color:#14532d;font-weight:700' : 'background:#f8fafc;border:1px solid #eef2f7;color:#334155' ?>">
                <strong style="margin-right:8px"><?= $h ?>)</strong> <?= e((string)($s[$col] ?? '')) ?>
                <?php if ($h === $dogru): ?><span style="float:right;font-size:.75rem">✔ Doğru</span><?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div style="margin-top:20px;text-align:center">
    <a href="/kursiyer/src-deneme-sinavlari.php" style="display:inline-block;padding:12px 24px;background:#1e3a8a;color:#fff;border-radius:10px;font-weight:800;text-decoration:none">🎯 Deneme Sınavına Başla</a>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
