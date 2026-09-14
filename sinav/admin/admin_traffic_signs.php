<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Trafik İşaretleri';
$activeMenu = 'ehliyet-traffic-signs';
$activeGroup = 'ehliyet';

$pdo = db();

$signs = [];
try {
    $st = $pdo->prepare(
        "SELECT t.*, c.name as category_name 
         FROM traffic_signs t 
         LEFT JOIN categories c ON t.category_id = c.id 
         WHERE t.group_id = (SELECT id FROM education_groups WHERE slug = 'ehliyet')
         ORDER BY t.sort_order, t.id DESC LIMIT 200"
    );
    $st->execute();
    $signs = $st->fetchAll();
} catch (Throwable) {
}

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">🚦 Trafik İşaretleri</h1>
    <a href="/admin/traffic-sign-edit.php" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni İşaret</a>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px">
    <?php if (empty($signs)): ?>
      <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted)">Henüz trafik işareti yok.</div>
    <?php else: foreach ($signs as $s): ?>
      <div class="card" style="padding:0;overflow:hidden;text-align:center">
        <div style="padding:16px;background:#f8fafc;min-height:120px;display:flex;align-items:center;justify-content:center">
          <?php if (!empty($s['image'])): ?>
            <img src="<?= e($s['image']) ?>" style="max-width:100%;max-height:100px;object-fit:contain" alt="">
          <?php else: ?>
            <span style="font-size:3rem">🚦</span>
          <?php endif; ?>
        </div>
        <div style="padding:10px">
          <div style="font-weight:700;color:var(--navy);font-size:.85rem"><?= e((string)$s['name']) ?></div>
          <div style="font-size:.7rem;color:var(--muted);margin:4px 0"><?= e((string)($s['category_name'] ?? '—')) ?></div>
          <div style="display:flex;gap:6px;justify-content:center;margin-top:8px">
            <span style="padding:2px 6px;background:<?= (int)$s['aktif'] === 1 ? '#dcfce7' : '#fee2e2' ?>;color:<?= (int)$s['aktif'] === 1 ? '#166534' : '#991b1b' ?>;border-radius:4px;font-size:.7rem"><?= (int)$s['aktif'] === 1 ? 'Aktif' : 'Pasif' ?></span>
            <a href="/admin/traffic-sign-edit.php?id=<?= (int)$s['id'] ?>" style="padding:2px 8px;background:#0f766e;color:#fff;border-radius:4px;text-decoration:none;font-size:.7rem">Düzenle</a>
          </div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
