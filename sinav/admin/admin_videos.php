<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Videolar';
$activeMenu = (($_GET['group'] ?? 'ehliyet') === 'src') ? 'src-videos' : 'ehliyet-videos';
$activeGroup = $_GET['group'] ?? 'ehliyet';

$pdo = db();

$videos = [];
try {
    $st = $pdo->prepare(
        "SELECT v.*, g.slug as group_slug, c.name as category_name 
         FROM videos v 
         JOIN education_groups g ON v.group_id = g.id 
         LEFT JOIN categories c ON v.category_id = c.id 
         WHERE g.slug = ? 
         ORDER BY v.sort_order, v.id DESC LIMIT 200"
    );
    $st->execute([$activeGroup]);
    $videos = $st->fetchAll();
} catch (Throwable) {
}

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">▶️ Videolar — <?= e(strtoupper($activeGroup)) ?></h1>
    <a href="/admin/video-edit.php?group=<?= e($activeGroup) ?>" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni Video</a>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
    <?php if (empty($videos)): ?>
      <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted)">Henüz video yok.</div>
    <?php else: foreach ($videos as $v): ?>
      <div class="card" style="padding:0;overflow:hidden">
        <div style="position:relative;padding-bottom:56.25%;background:#000">
          <?php if (!empty($v['youtube_id'])): ?>
            <img src="https://img.youtube.com/vi/<?= e($v['youtube_id']) ?>/hqdefault.jpg" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover" alt="">
          <?php elseif (!empty($v['thumbnail'])): ?>
            <img src="<?= e($v['thumbnail']) ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover" alt="">
          <?php else: ?>
            <div style="position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:3rem;color:#666">▶️</div>
          <?php endif; ?>
        </div>
        <div style="padding:12px">
          <div style="font-weight:700;color:var(--navy);margin-bottom:4px;font-size:.9rem"><?= e((string)$v['title']) ?></div>
          <div style="font-size:.75rem;color:var(--muted);margin-bottom:8px"><?= e((string)($v['category_name'] ?? '—')) ?></div>
          <div style="display:flex;gap:6px;align-items:center">
            <span style="padding:2px 6px;background:<?= (int)$v['aktif'] === 1 ? '#dcfce7' : '#fee2e2' ?>;color:<?= (int)$v['aktif'] === 1 ? '#166534' : '#991b1b' ?>;border-radius:4px;font-size:.7rem;font-weight:700"><?= (int)$v['aktif'] === 1 ? 'Aktif' : 'Pasif' ?></span>
            <a href="/admin/video-edit.php?id=<?= (int)$v['id'] ?>" style="margin-left:auto;padding:4px 10px;background:#0f766e;color:#fff;border-radius:4px;text-decoration:none;font-size:.75rem">Düzenle</a>
          </div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
