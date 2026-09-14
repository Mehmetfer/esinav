<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC Sorular';
$activeMenu = 'src-sorular';
require __DIR__ . '/_layout_top.php';

$pdo = db();
src_ensure_tables($pdo);

// SRC sorularını getir
$rows = [];
try {
    $rows = $pdo->query("SELECT id, ders, soru, dogru, aktif FROM src_sorular ORDER BY id DESC LIMIT 100")->fetchAll();
} catch (Throwable) {
    // tablo yoksa boş liste
}

$dersAd = src_ders_adlari();
?>
  <h1 class="k-page-title">SRC Sorular</h1>

  <?php if (empty($rows)): ?>
    <section class="k-card">
      <div style="text-align:center;padding:40px;color:var(--muted)">
        <div style="font-size:3rem;margin-bottom:12px">📋</div>
        <div style="font-size:1.1rem;font-weight:600;margin-bottom:8px">Henüz SRC sorusu eklenmemiş</div>
        <div style="font-size:.9rem">Sorular admin panelinden eklenecektir.</div>
      </div>
    </section>
  <?php else: ?>
    <section class="k-card">
      <div class="k-table-wrap">
        <table class="k-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Ders</th>
              <th>Soru</th>
              <th>Durum</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= e($dersAd[$r['ders']] ?? (string)$r['ders']) ?></td>
                <td><?= e(mb_substr((string)$r['soru'], 0, 100)) ?><?= mb_strlen((string)$r['soru']) > 100 ? '…' : '' ?></td>
                <td><?= (int)$r['aktif'] === 1 ? 'Aktif' : 'Pasif' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
