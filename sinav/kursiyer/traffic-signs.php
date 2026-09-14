<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Trafik İşaretleri';
$activeMenu = 'trafik-isaretleri';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$groupId = (int)($user['group_id'] ?? 0);

if ($groupId <= 0) {
    $ug = user_groups($user['id'] ?? 0);
    $groupId = !empty($ug) ? (int)$ug[0] : 0;
}

// Get categories for this group
$categories = [];
try {
    $pdo = db();
    $st = $pdo->prepare("SELECT * FROM categories WHERE group_id = ? AND aktif = 1 ORDER BY sort_order, name");
    $st->execute([$groupId]);
    $categories = $st->fetchAll();
} catch (Throwable) {}

$selectedCat = (int)($_GET['cat'] ?? 0);
$signs = [];
try {
    $pdo = db();
    if ($selectedCat > 0) {
        $st = $pdo->prepare("SELECT * FROM traffic_signs WHERE group_id = ? AND category_id = ? AND aktif = 1 ORDER BY sort_order, name");
        $st->execute([$groupId, $selectedCat]);
    } else {
        $st = $pdo->prepare("SELECT * FROM traffic_signs WHERE group_id = ? AND aktif = 1 ORDER BY sort_order, name");
        $st->execute([$groupId]);
    }
    $signs = $st->fetchAll();
} catch (Throwable) {}
?>
  <h1 class="k-page-title">Trafik İşaretleri</h1>

  <?php if (!empty($categories)): ?>
  <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
    <a href="traffic-signs.php" style="padding:8px 16px;border-radius:20px;text-decoration:none;font-size:.85rem;font-weight:600;<?= $selectedCat === 0 ? 'background:#1e3a8a;color:#fff' : 'background:#e2e8f0;color:#334155' ?>">Tümü</a>
    <?php foreach ($categories as $cat): ?>
      <a href="traffic-signs.php?cat=<?= (int)$cat['id'] ?>" style="padding:8px 16px;border-radius:20px;text-decoration:none;font-size:.85rem;font-weight:600;<?= $selectedCat === (int)$cat['id'] ? 'background:#1e3a8a;color:#fff' : 'background:#e2e8f0;color:#334155' ?>"><?= e($cat['name']) ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (empty($signs)): ?>
    <section class="k-card">
      <div style="text-align:center;padding:40px;color:var(--muted)">
        <div style="font-size:3rem;margin-bottom:12px">🚦</div>
        <div style="font-size:1.1rem;font-weight:600;margin-bottom:8px">Henüz trafik işareti eklenmemiş</div>
      </div>
    </section>
  <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px">
      <?php foreach ($signs as $s): ?>
        <section class="k-card" style="margin:0;text-align:center">
          <?php if (!empty($s['image'])): ?>
            <img src="<?= e($s['image']) ?>" alt="<?= e($s['name']) ?>" style="width:100%;height:180px;object-fit:contain;background:#f8fafc;border-radius:8px;margin-bottom:12px">
          <?php else: ?>
            <div style="height:180px;display:flex;align-items:center;justify-content:center;background:#f8fafc;border-radius:8px;margin-bottom:12px;font-size:4rem">🚸</div>
          <?php endif; ?>
          <div style="font-weight:700;color:var(--navy);margin-bottom:8px"><?= e($s['name']) ?></div>
          <?php if (!empty($s['description'])): ?>
            <div style="font-size:.85rem;color:var(--muted)"><?= e($s['description']) ?></div>
          <?php endif; ?>
        </section>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
