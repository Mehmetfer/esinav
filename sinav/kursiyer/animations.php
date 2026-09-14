<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Animasyonlar';
$activeMenu = 'animations';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$userGroups = user_education_groups((int)$user['id']);
$currentGroup = $userGroups[0] ?? ['slug' => 'ehliyet', 'name' => 'Ehliyet', 'id' => 1];

$pdo = db();

$animations = [];
try {
    $st = $pdo->prepare(
        "SELECT a.*, c.name as category_name 
         FROM animations a 
         LEFT JOIN categories c ON a.category_id = c.id 
         WHERE a.group_id = ? AND a.aktif = 1 
         ORDER BY a.sort_order, a.id DESC LIMIT 200"
    );
    $st->execute([$currentGroup['id']]);
    $animations = $st->fetchAll();
} catch (Throwable) {
}
?>
  <h1 class="k-page-title">🎞️ Animasyonlar</h1>

  <?php if (empty($animations)): ?>
    <section class="k-card">
      <div style="text-align:center;padding:40px;color:var(--muted)">
        <div style="font-size:3rem;margin-bottom:12px">🎞️</div>
        <div style="font-size:1.1rem;font-weight:600">Henüz animasyon eklenmemiş</div>
      </div>
    </section>
  <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <?php foreach ($animations as $a): ?>
        <a href="<?= e($a['file_path'] ?? '#') ?>" class="k-card" style="text-decoration:none;color:inherit;overflow:hidden" <?= !empty($a['file_path']) ? 'target="_blank"' : '' ?>>
          <div style="position:relative;padding-bottom:56.25%;background:#f1f5f9">
            <?php if (!empty($a['thumbnail'])): ?>
              <img src="<?= e($a['thumbnail']) ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover" alt="">
            <?php else: ?>
              <div style="position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center">
                <span style="font-size:3rem">🎞️</span>
              </div>
            <?php endif; ?>
          </div>
          <div style="padding:12px">
            <div style="font-weight:700;color:var(--navy);font-size:.9rem"><?= e((string)$a['title']) ?></div>
            <?php if (!empty($a['category_name'])): ?>
              <div style="font-size:.75rem;color:var(--muted)"><?= e($a['category_name']) ?></div>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
