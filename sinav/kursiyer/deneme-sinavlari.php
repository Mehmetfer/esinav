<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Deneme Sınavları';
$activeMenu = 'deneme-sinavlari';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$groupId = (int)($user['group_id'] ?? 0);

if ($groupId <= 0) {
    $ug = user_groups($user['id'] ?? 0);
    $groupId = !empty($ug) ? (int)$ug[0] : 0;
}

// Get categories
$categories = [];
try {
    $pdo = db();
    $st = $pdo->prepare("SELECT * FROM categories WHERE group_id = ? AND aktif = 1 ORDER BY sort_order, name");
    $st->execute([$groupId]);
    $categories = $st->fetchAll();
} catch (Throwable) {}

$selectedCat = (int)($_GET['cat'] ?? 0);
$exams = [];
try {
    $pdo = db();
    if ($selectedCat > 0) {
        $st = $pdo->prepare("SELECT * FROM exams WHERE group_id = ? AND category_id = ? AND type = 'deneme' AND aktif = 1 ORDER BY title");
        $st->execute([$groupId, $selectedCat]);
    } else {
        $st = $pdo->prepare("SELECT * FROM exams WHERE group_id = ? AND type = 'deneme' AND aktif = 1 ORDER BY title");
        $st->execute([$groupId]);
    }
    $exams = $st->fetchAll();
} catch (Throwable) {}
?>
  <h1 class="k-page-title">Deneme Sınavları</h1>

  <?php if (!empty($categories)): ?>
  <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
    <a href="deneme-sinavlari.php" style="padding:8px 16px;border-radius:20px;text-decoration:none;font-size:.85rem;font-weight:600;<?= $selectedCat === 0 ? 'background:#1e3a8a;color:#fff' : 'background:#e2e8f0;color:#334155' ?>">Tümü</a>
    <?php foreach ($categories as $cat): ?>
      <a href="deneme-sinavlari.php?cat=<?= (int)$cat['id'] ?>" style="padding:8px 16px;border-radius:20px;text-decoration:none;font-size:.85rem;font-weight:600;<?= $selectedCat === (int)$cat['id'] ? 'background:#1e3a8a;color:#fff' : 'background:#e2e8f0;color:#334155' ?>"><?= e($cat['name']) ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (empty($exams)): ?>
    <section class="k-card">
      <div style="text-align:center;padding:40px;color:var(--muted)">
        <div style="font-size:3rem;margin-bottom:12px">⏱</div>
        <div style="font-size:1.1rem;font-weight:600;margin-bottom:8px">Henüz deneme sınavı oluşturulmamış</div>
      </div>
    </section>
  <?php else: ?>
    <div style="display:grid;gap:12px">
      <?php foreach ($exams as $exam): ?>
        <section class="k-card" style="margin:0">
          <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
            <div>
              <div style="font-weight:700;color:var(--navy);font-size:1.1rem"><?= e($exam['title']) ?></div>
              <?php if (!empty($exam['description'])): ?>
                <div style="font-size:.85rem;color:var(--muted);margin-top:4px"><?= e($exam['description']) ?></div>
              <?php endif; ?>
              <div style="display:flex;gap:16px;margin-top:8px;font-size:.85rem;color:var(--muted)">
                <span>📋 <?= (int)$exam['question_count'] ?> soru</span>
                <span>⏱ <?= gmdate('H:i', (int)$exam['duration_seconds']) ?></span>
                <span>🎯 Geçme: <?= (int)$exam['passing_score'] ?>%</span>
              </div>
            </div>
            <a href="deneme-sinav.php?id=<?= (int)$exam['id'] ?>" class="k-start-btn" style="text-decoration:none;padding:10px 20px;font-size:.85rem">Başla</a>
          </div>
        </section>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
