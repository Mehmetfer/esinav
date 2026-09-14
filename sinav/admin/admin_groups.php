<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/education.php';
require_once dirname(__DIR__) . '/includes/auth_extended.php';

$pageTitle = 'Eğitim Grupları';
$activeMenu = 'groups';

$user = require_role('admin');
$pdo = db();

$message = '';
$error = '';

// Grup ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $slug = strtolower(trim($_POST['slug'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        
        if ($slug === '' || $name === '') {
            $error = 'Slug ve ad zorunludur.';
        } elseif (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $error = 'Slug sadece küçük harf, rakam ve tire içerebilir.';
        } else {
            try {
                education_group_create($slug, $name, $description ?: null, $icon ?: null);
                $message = 'Grup eklendi.';
            } catch (Throwable $e) {
                $error = 'Bu slug zaten kullanılıyor.';
            }
        }
    }
    
    if ($_POST['action'] === 'toggle' && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $group = education_group_by_id($id);
        if ($group) {
            education_group_update($id, (string)$group['name'], $group['description'] ?: null, $group['icon'] ?: null, !((int)$group['aktif'] === 1));
            $message = 'Grup güncellendi.';
        }
    }
    
    if ($_POST['action'] === 'delete' && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        try {
            $st = $pdo->prepare("DELETE FROM education_groups WHERE id = ?");
            $st->execute([$id]);
            $message = 'Grup silindi.';
        } catch (Throwable $e) {
            $error = 'Bu grup kullanımda olduğu için silinemez.';
        }
    }
}

$groups = education_groups(false);

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">Eğitim Grupları</h1>
  </div>

  <?php if ($message !== ''): ?><div class="alert alert-ok"><?= e($message) ?></div><?php endif; ?>
  <?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

  <div class="card" style="margin-bottom:14px">
    <h3 style="margin:0 0 12px;color:var(--navy)">Yeni Grup Ekle</h3>
    <form method="post" style="display:grid;gap:10px">
      <input type="hidden" name="action" value="create">
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px">
        <div>
          <label style="font-size:.75rem;color:var(--muted)">Slug</label>
          <input name="slug" required pattern="[a-z0-9-]+" placeholder="ehliyet"
                 style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
        </div>
        <div>
          <label style="font-size:.75rem;color:var(--muted)">Ad</label>
          <input name="name" required placeholder="Ehliyet"
                 style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
        </div>
        <div>
          <label style="font-size:.75rem;color:var(--muted)">İkon</label>
          <input name="icon" placeholder="🚗"
                 style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
        </div>
        <div>
          <label style="font-size:.75rem;color:var(--muted)">Açıklama</label>
          <input name="description" placeholder="Açıklama"
                 style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
        </div>
      </div>
      <button type="submit" style="padding:10px 16px;border:0;border-radius:6px;background:#1e3a8a;color:#fff;font-weight:700;cursor:pointer;justify-self:start">Ekle</button>
    </form>
  </div>

  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
      <thead>
        <tr style="background:#f8fafc;text-align:left">
          <th style="padding:10px 12px">ID</th>
          <th style="padding:10px 12px">Slug</th>
          <th style="padding:10px 12px">Ad</th>
          <th style="padding:10px 12px">İkon</th>
          <th style="padding:10px 12px">Durum</th>
          <th style="padding:10px 12px">İşlemler</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($groups as $g): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px"><?= (int)$g['id'] ?></td>
          <td style="padding:10px 12px"><code><?= e($g['slug']) ?></code></td>
          <td style="padding:10px 12px;font-weight:600"><?= e($g['name']) ?></td>
          <td style="padding:10px 12px;font-size:1.2rem"><?= e($g['icon']) ?></td>
          <td style="padding:10px 12px">
            <span style="padding:2px 8px;border-radius:4px;font-size:.75rem;font-weight:700;<?= (int)$g['aktif'] === 1 ? 'background:#dcfce7;color:#166534' : 'background:#fee2e2;color:#991b1b' ?>">
              <?= (int)$g['aktif'] === 1 ? 'Aktif' : 'Pasif' ?>
            </span>
          </td>
          <td style="padding:10px 12px">
            <form method="post" style="display:inline">
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
              <button type="submit" style="padding:4px 10px;border:0;border-radius:4px;background:#e2e8f0;color:#334155;font-weight:600;cursor:pointer;font-size:.75rem">
                <?= (int)$g['aktif'] === 1 ? 'Pasif Yap' : 'Aktif Yap' ?>
              </button>
            </form>
            <form method="post" style="display:inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
              <button type="submit" style="padding:4px 10px;border:0;border-radius:4px;background:#fee2e2;color:#991b1b;font-weight:600;cursor:pointer;font-size:.75rem">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
