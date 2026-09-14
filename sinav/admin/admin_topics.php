<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Konu Yönetimi';
$activeMenu = 'topics';

$pdo = db();
$message = '';
$error = '';

$groupSlug = $_GET['group'] ?? 'ehliyet';
$catId = (int)($_GET['cat'] ?? 0);

$groups = education_groups();
$currentGroup = null;
foreach ($groups as $g) { if ($g['slug'] === $groupSlug) { $currentGroup = $g; break; } }
if (!$currentGroup) $currentGroup = $groups[0] ?? null;

$categories = $currentGroup ? education_categories((int)$currentGroup['id']) : [];
$currentCategory = null;
if ($catId > 0) { foreach ($categories as $c) { if ((int)$c['id'] === $catId) { $currentCategory = $c; break; } } }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $currentCategory) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_topic') {
        $name = trim($_POST['name'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if ($slug === '') $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
        if ($name !== '') {
            try {
                $max = (int)$pdo->query("SELECT MAX(sort_order) FROM topics WHERE category_id=" . (int)$currentCategory['id'])->fetchColumn();
                $st = $pdo->prepare("INSERT INTO topics (category_id, name, slug, content, sort_order) VALUES (?, ?, ?, ?, ?)");
                $st->execute([(int)$currentCategory['id'], $name, $slug, $content, $max + 1]);
                $message = 'Konu eklendi.';
            } catch (Throwable $e) { $error = 'Eklerken hata: ' . $e->getMessage(); }
        } else { $error = 'Konu adı zorunlu.'; }
    } elseif ($action === 'delete_topic') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) { $pdo->prepare("DELETE FROM topics WHERE id=? AND category_id=?")->execute([$id, (int)$currentCategory['id']]); $message = 'Konu silindi.'; }
    } elseif ($action === 'toggle_aktif') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) { $pdo->prepare("UPDATE topics SET aktif = IF(aktif=1,0,1) WHERE id=?")->execute([$id]); $message = 'Durum güncellendi.'; }
    }
}

$topics = $currentCategory ? education_topics((int)$currentCategory['id']) : [];
require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">Konu Yönetimi</h1>
    <?php if ($currentCategory): ?>
      <a href="topics.php?group=<?= e($groupSlug) ?>&cat=<?= (int)$currentCategory['id'] ?>&action=new" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni Konu</a>
    <?php endif; ?>
  </div>

  <?php if ($message !== ''): ?><div class="alert alert-ok"><?= e($message) ?></div><?php endif; ?>
  <?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

  <div class="card" style="margin-bottom:14px">
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px">
      <?php foreach ($groups as $g): ?>
        <a href="topics.php?group=<?= e($g['slug']) ?>" style="padding:8px 16px;border-radius:20px;text-decoration:none;font-weight:700;font-size:.85rem;<?= $g['slug'] === $groupSlug ? 'background:#1e3a8a;color:#fff' : 'background:#e2e8f0;color:#334155' ?>"><?= e($g['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <?php if ($categories): ?>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <?php foreach ($categories as $c): ?>
          <a href="topics.php?group=<?= e($groupSlug) ?>&cat=<?= (int)$c['id'] ?>" style="padding:6px 12px;border-radius:16px;text-decoration:none;font-weight:600;font-size:.8rem;<?= (int)$c['id'] === $catId ? 'background:#dbeafe;color:#1e40af' : 'background:#f1f5f9;color:#475569' ?>"><?= e($c['name']) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if (($_GET['action'] ?? '') === 'new' && $currentCategory): ?>
    <div class="card" style="margin-bottom:14px">
      <h3 style="margin:0 0 12px;font-size:1.1rem;color:var(--navy)">Yeni Konu Ekle — <?= e($currentCategory['name']) ?></h3>
      <form method="post" style="display:grid;gap:12px">
        <input type="hidden" name="action" value="add_topic">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label style="font-size:.75rem;color:var(--muted)">Konu Adı</label>
            <input name="name" required placeholder="Örn: Trafik ve Çevre" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          </div>
          <div>
            <label style="font-size:.75rem;color:var(--muted)">Slug</label>
            <input name="slug" placeholder="trafik-cevre (otomatik)" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          </div>
        </div>
        <div>
          <label style="font-size:.75rem;color:var(--muted)">İçerik (HTML)</label>
          <textarea name="content" rows="6" placeholder="Konu anlatımı..." style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px;font-family:monospace"></textarea>
        </div>
        <div style="display:flex;gap:8px">
          <button type="submit" style="padding:10px 18px;border:0;border-radius:6px;background:#1e3a8a;color:#fff;font-weight:700;cursor:pointer">Kaydet</button>
          <a href="topics.php?group=<?= e($groupSlug) ?>&cat=<?= (int)$currentCategory['id'] ?>" style="padding:10px 18px;border-radius:6px;background:#e2e8f0;color:#334155;font-weight:700;text-decoration:none">İptal</a>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <?php if ($currentCategory): ?>
    <div class="card" style="padding:0;overflow:auto">
      <table style="width:100%;border-collapse:collapse;font-size:.88rem">
        <thead>
          <tr style="background:#f8fafc;text-align:left">
            <th style="padding:10px 12px">Sıra</th>
            <th style="padding:10px 12px">Ad</th>
            <th style="padding:10px 12px">Slug</th>
            <th style="padding:10px 12px">Durum</th>
            <th style="padding:10px 12px;text-align:right">İşlemler</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($topics)): ?>
          <tr><td colspan="5" style="padding:20px;color:var(--muted)">Bu kategoride henüz konu yok.</td></tr>
        <?php else: foreach ($topics as $t): ?>
          <tr style="border-top:1px solid #eef2f7">
            <td style="padding:10px 12px;font-weight:600"><?= (int)$t['sort_order'] ?></td>
            <td style="padding:10px 12px;font-weight:600"><?= e($t['name']) ?></td>
            <td style="padding:10px 12px;color:var(--muted);font-family:monospace"><?= e($t['slug']) ?></td>
            <td style="padding:10px 12px">
              <form method="post" style="display:inline">
                <input type="hidden" name="action" value="toggle_aktif">
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button type="submit" style="padding:4px 12px;border:0;border-radius:12px;font-size:.75rem;font-weight:700;cursor:pointer;<?= $t['aktif'] ? 'background:#dcfce7;color:#166534' : 'background:#fee2e2;color:#991b1b' ?>"><?= $t['aktif'] ? 'Aktif' : 'Pasif' ?></button>
              </form>
            </td>
            <td style="padding:10px 12px;text-align:right">
              <a href="questions.php?group=<?= e($groupSlug) ?>&topic=<?= (int)$t['id'] ?>" style="padding:4px 10px;border-radius:6px;background:#dbeafe;color:#1e40af;font-weight:700;text-decoration:none;font-size:.8rem">Sorular</a>
              <form method="post" style="display:inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                <input type="hidden" name="action" value="delete_topic">
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button type="submit" style="padding:4px 10px;border:0;border-radius:6px;background:#fee2e2;color:#991b1b;font-weight:700;cursor:pointer;font-size:.8rem">Sil</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="card" style="text-align:center;padding:40px;color:var(--muted)">
      <div style="font-size:2rem;margin-bottom:8px">📂</div>
      <div>Lütfen bir kategori seçin.</div>
    </div>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
