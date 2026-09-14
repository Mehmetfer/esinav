<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Kategori';
$activeMenu = 'kategori';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$groupId = (int)($user['group_id'] ?? 0);

if ($groupId <= 0) {
    $ug = user_groups($user['id'] ?? 0);
    $groupId = !empty($ug) ? (int)$ug[0] : 0;
}

$catId = (int)($_GET['id'] ?? 0);
$category = null;
$topics = [];

try {
    $pdo = db();
    $st = $pdo->prepare("SELECT * FROM categories WHERE id = ? AND group_id = ? AND aktif = 1 LIMIT 1");
    $st->execute([$catId, $groupId]);
    $category = $st->fetch();
    
    if ($category) {
        $st = $pdo->prepare("SELECT * FROM topics WHERE category_id = ? AND aktif = 1 ORDER BY sort_order, name");
        $st->execute([$catId]);
        $topics = $st->fetchAll();
    }
} catch (Throwable) {}

if (!$category) {
    redirect('/kursiyer/index.php');
}

$pageTitle = $category['name'];
?>
  <h1 class="k-page-title"><?= e($category['name']) ?></h1>

  <?php if (!empty($category['description'])): ?>
    <div style="font-size:.95rem;color:var(--muted);margin-bottom:16px"><?= e($category['description']) ?></div>
  <?php endif; ?>

  <?php if (empty($topics)): ?>
    <section class="k-card">
      <div style="text-align:center;padding:40px;color:var(--muted)">
        <div style="font-size:3rem;margin-bottom:12px">📂</div>
        <div style="font-size:1.1rem;font-weight:600;margin-bottom:8px">Henüz konu eklenmemiş</div>
      </div>
    </section>
  <?php else: ?>
    <div style="display:grid;gap:12px">
      <?php foreach ($topics as $topic): ?>
        <section class="k-card" style="margin:0">
          <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
            <div>
              <div style="font-weight:700;color:var(--navy);font-size:1.1rem"><?= e($topic['name']) ?></div>
              <?php if (!empty($topic['content'])): ?>
                <div style="font-size:.85rem;color:var(--muted);margin-top:4px"><?= e(mb_substr($topic['content'], 0, 150)) ?>…</div>
              <?php endif; ?>
            </div>
            <a href="konu.php?id=<?= (int)$topic['id'] ?>" class="k-btn-sm" style="text-decoration:none">İncele</a>
          </div>
        </section>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
