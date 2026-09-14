<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Kategori Yönetimi';
$activeMenu = 'categories';

$pdo = db();
$message = '';
$error = '';

$groupSlug = $_GET['group'] ?? 'ehliyet';
$groups = education_groups();
$currentGroup = null;
foreach ($groups as $g) {
    if ($g['slug'] === $groupSlug) { $currentGroup = $g; break; }
}
if (!$currentGroup) { $currentGroup = $groups[0] ?? null; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $currentGroup) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_category') {
        $name = trim($_POST['name'] ?? '');
        $parentId = (int)($_POST['parent_id'] ?? 0);
        $slug = trim($_POST['slug'] ?? '');
        if ($slug === '') $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
        if ($name !== '') {
            try {
                $st = $pdo->prepare("INSERT INTO categories (group_id, parent_id, name, slug) VALUES (?, ?, ?, ?)");
                $st->execute([(int)$currentGroup['id'], $parentId > 0 ? $parentId : null, $name, $slug]);
                $message = 'Kategori eklendi.';
            } catch (Throwable $e) { $error = 'Eklerken hata: ' . $e->getMessage(); }
        } else { $error = 'Kategori adı zorunlu.'; }
    } elseif ($action === 'delete_category') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("DELETE FROM categories WHERE id=? AND group_id=?")->execute([$id, (int)$currentGroup['id']]);
            $message = 'Kategori silindi.';
        }
    } elseif ($action === 'toggle_aktif') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("UPDATE categories SET aktif = IF(aktif=1,0,1) WHERE id=?")->execute([$id]);
            $message = 'Durum güncellendi.';
        }
    }
}

$categories = $currentGroup ? education_categories((int)$currentGroup['id']) : [];
require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">Kategori Yönetimi</h1>
    <a href="categories.php?group=<?= e($groupSlug) ?>&action=new" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni Kategori</a>
  </div>

  <?php if ($message !== ''): ?><div class="alert alert-ok"><?= e($message) ?></div><?php endif; ?>
  <?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

  <div class="card" style="margin-bottom:14px">
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <?php foreach ($groups as $g): ?>
        <a href="categories.php?group=<?= e($g['slug']) ?>" style="padding:8px 16px;border-radius:20px;text-decoration:none;font-weight:700;font-size:.85rem;<?= $g['slug'] === $groupSlug ? 'background:#1e3a8a;color:#fff' : 'background:#e2e8f0;color:#334155' ?>"><?= e($g['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if (($_GET['action'] ?? '') === 'new'): ?>
    <div class="card" style="margin-bottom:14px">
      <h3 style="margin:0 0 12px;font-size:1.1rem;color:var(--navy)">Yeni Kategori Ekle — <?= e($currentGroup['name'] ?? '') ?></h3>
      <form method="post" style="display:grid;gap:12px">
        <input type="hidden" name="action" value="add_category">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label style="font-size:.75rem;color:var(--muted)">Kategori Adı</label>
            <input name="name" required placeholder="Örn: SRC 1" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          </div>
          <div>
            <label style="font-size:.75rem;color:var(--muted)">Slug</label>
            <input name="slug" placeholder="src-1 (otomatik)" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          </div>
        </div>
        <div>
          <label style="font-size:.75rem;color:var(--muted)">Üst Kategori</label>
          <select name="parent_id" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
            <option value="0">— Yok —</option>
            <?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div style="display:flex;gap:8px">
          <button type="submit" style="padding:10px 18px;border:0;border-radius:6px;background:#1e3a8a;color:#fff;font-weight:700;cursor:pointer">Kaydet</button>
          <a href="categories.php?group=<?= e($groupSlug) ?>" style="padding:10px 18px;border-radius:6px;background:#e2e8f0;color:#334155;font-weight:700;text-decoration:none">İptal</a>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
      <thead>
        <tr style="background:#f8fafc;text-align:left">
          <th style="padding:10px 12px">ID</th>
          <th style="padding:10px 12px">Ad</th>
          <th style="padding:10px 12px">Slug</th>
          <th style="padding:10px 12px">Durum</th>
          <th style="padding:10px 12px;text-align:right">İşlemler</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($categories)): ?>
        <tr><td colspan="5" style="padding:20px;color:var(--muted)">Bu grubun henüz kategorisi yok.</td></tr>
      <?php else: foreach ($categories as $c): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px;font-weight:600"><?= (int)$c['id'] ?></td>
          <td style="padding:10px 12px;font-weight:600"><?= e($c['name']) ?></td>
          <td style="padding:10px 12px;color:var(--muted);font-family:monospace"><?= e($c['slug']) ?></td>
          <td style="padding:10px 12px">
            <form method="post" style="display:inline">
              <input type="hidden" name="action" value="toggle_aktif">
              <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
              <button type="submit" style="padding:4px 12px;border:0;border-radius:12px;font-size:.75rem;font-weight:700;cursor:pointer;<?= $c['aktif'] ? 'background:#dcfce7;color:#166534' : 'background:#fee2e2;color:#991b1b' ?>"><?= $c['aktif'] ? 'Aktif' : 'Pasif' ?></button>
            </form>
          </td>
          <td style="padding:10px 12px;text-align:right">
            <form method="post" style="display:inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
              <input type="hidden" name="action" value="delete_category">
              <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
              <button type="submit" style="padding:4px 10px;border:0;border-radius:6px;background:#fee2e2;color:#991b1b;font-weight:700;cursor:pointer;font-size:.8rem">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
