<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Trafik İşaretleri';
$activeMenu = 'trafik';

$pdo = db();

$signs = [];
try {
    $signs = $pdo->query(
        "SELECT t.*, c.name AS category_name
         FROM traffic_signs t
         LEFT JOIN categories c ON t.category_id = c.id
         ORDER BY t.sort_order, t.id"
    )->fetchAll();
} catch (Throwable) {
}

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">🚦 Trafik İşaretleri <span style="font-size:.8rem;color:var(--muted)">(<?= count($signs) ?>)</span></h1>
    <a href="/admin/traffic-sign-edit.php" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni İşaret</a>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:16px">
    <?php if (empty($signs)): ?>
      <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted)">Henüz trafik işareti yok. "+ Yeni İşaret" ile ekleyin.</div>
    <?php else: foreach ($signs as $s):
        $img = trim((string)($s['image'] ?? ''));
        $imgSrc = $img !== '' ? ('/assets/img/signboards/' . ltrim($img, '/')) : '';
    ?>
      <div class="card" style="padding:0;overflow:hidden;text-align:center">
        <div style="padding:16px;background:#f8fafc;min-height:120px;display:flex;align-items:center;justify-content:center">
          <?php if ($imgSrc !== ''): ?>
            <img src="<?= e($imgSrc) ?>" style="max-width:100%;max-height:110px;object-fit:contain" alt="">
          <?php else: ?>
            <span style="font-size:3rem">🚦</span>
          <?php endif; ?>
        </div>
        <div style="padding:10px">
          <div style="font-weight:700;color:var(--navy);font-size:.85rem">
            <?= e((string)$s['name']) ?><?php if (!empty($s['code'])): ?> <span style="color:var(--muted);font-weight:600">(<?= e((string)$s['code']) ?>)</span><?php endif; ?>
          </div>
          <div style="font-size:.72rem;color:var(--muted);margin:4px 0"><?= e((string)($s['category_name'] ?? '—')) ?></div>
          <div style="display:flex;gap:6px;justify-content:center;align-items:center;margin-top:8px">
            <span style="padding:2px 6px;background:<?= (int)$s['aktif'] === 1 ? '#dcfce7' : '#fee2e2' ?>;color:<?= (int)$s['aktif'] === 1 ? '#166534' : '#991b1b' ?>;border-radius:4px;font-size:.7rem"><?= (int)$s['aktif'] === 1 ? 'Aktif' : 'Pasif' ?></span>
            <a href="/admin/traffic-sign-edit.php?id=<?= (int)$s['id'] ?>" style="padding:2px 8px;background:#0f766e;color:#fff;border-radius:4px;text-decoration:none;font-size:.7rem">Düzenle</a>
            <form method="post" action="/admin/traffic-sign-edit.php" style="display:inline" onsubmit="return confirm('Bu işaret silinsin mi?')">
              <input type="hidden" name="delete" value="<?= (int)$s['id'] ?>">
              <button type="submit" style="padding:2px 8px;background:#dc2626;color:#fff;border:0;border-radius:4px;text-decoration:none;font-size:.7rem;cursor:pointer">Sil</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
