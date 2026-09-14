<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'İşaret Ekle / Düzenle';
$activeMenu = 'trafik';

$pdo = db();

// Trafik levha kategorileri (ehliyet grubu, slug: isaret-*)
$cats = [];
try {
    $cats = $pdo->query("SELECT * FROM categories WHERE group_id = 1 AND slug LIKE 'isaret-%' ORDER BY sort_order, id")->fetchAll();
} catch (Throwable) {
}

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;

// Silme islemi
if (isset($_POST['delete'])) {
    $delId = (int)$_POST['delete'];
    try {
        $pdo->prepare("DELETE FROM traffic_signs WHERE id = ?")->execute([$delId]);
    } catch (Throwable) {
    }
    redirect('/admin/traffic-signs.php');
}

$form = [
    'name' => '', 'code' => '', 'category_id' => 0, 'image' => '',
    'description' => '', 'sort_order' => 0, 'aktif' => 1,
];

if ($isEdit) {
    $st = $pdo->prepare("SELECT * FROM traffic_signs WHERE id = ?");
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) {
        redirect('/admin/traffic-signs.php');
    }
    $form = [
        'name' => (string)$row['name'],
        'code' => (string)($row['code'] ?? ''),
        'category_id' => (int)($row['category_id'] ?? 0),
        'image' => (string)($row['image'] ?? ''),
        'description' => (string)($row['description'] ?? ''),
        'sort_order' => (int)$row['sort_order'],
        'aktif' => (int)$row['aktif'],
    ];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete'])) {
    $form['name'] = trim((string)($_POST['name'] ?? ''));
    $form['code'] = strtoupper(trim((string)($_POST['code'] ?? '')));
    $form['category_id'] = (int)($_POST['category_id'] ?? 0);
    $form['image'] = trim((string)($_POST['image'] ?? ''));
    $form['description'] = trim((string)($_POST['description'] ?? ''));
    $form['sort_order'] = (int)($_POST['sort_order'] ?? 0);
    $form['aktif'] = isset($_POST['aktif']) ? 1 : 0;

    // Gorsel yukleme (opsiyonel)
    if (isset($_FILES['image_file']) && is_array($_FILES['image_file'])
        && ($_FILES['image_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $upDir = dirname(__DIR__) . '/assets/img/signboards/uploads';
        if (!is_dir($upDir)) {
            @mkdir($upDir, 0775, true);
        }
        $ext = strtolower((string)pathinfo((string)$_FILES['image_file']['name'], PATHINFO_EXTENSION));
        $ext = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true) ? $ext : 'png';
        $fname = 'isaret_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (move_uploaded_file((string)$_FILES['image_file']['tmp_name'], $upDir . '/' . $fname)) {
            $form['image'] = 'uploads/' . $fname;
        }
    }

    if ($form['name'] === '') {
        $error = 'İşaret adı zorunludur.';
    } else {
        if ($isEdit) {
            $pdo->prepare(
                "UPDATE traffic_signs SET name=?, code=?, category_id=?, image=?, description=?, sort_order=?, aktif=? WHERE id=?"
            )->execute([$form['name'], $form['code'], $form['category_id'] ?: null, $form['image'], $form['description'], $form['sort_order'], $form['aktif'], $id]);
        } else {
            $pdo->prepare(
                "INSERT INTO traffic_signs (group_id, category_id, name, code, description, image, sort_order, aktif) VALUES (1, ?, ?, ?, ?, ?, ?, ?)"
            )->execute([$form['category_id'] ?: null, $form['name'], $form['code'], $form['description'], $form['image'], $form['sort_order'], $form['aktif']]);
        }
        redirect('/admin/traffic-signs.php');
    }
}

// Mevcut gorseller (datalist icin)
$existingImages = [];
foreach (glob(dirname(__DIR__) . '/assets/img/signboards/tt-photos/*.{png,jpg,jpeg,svg,webp}', GLOB_BRACE) ?: [] as $f) {
    $existingImages[] = 'tt-photos/' . basename($f);
}
foreach (glob(dirname(__DIR__) . '/assets/img/signboards/tt/*.{svg,png}', GLOB_BRACE) ?: [] as $f) {
    $existingImages[] = 'tt/' . basename($f);
}
sort($existingImages);

require __DIR__ . '/_layout_top.php';
?>
  <h1 style="margin:0 0 14px;color:var(--navy);font-size:1.25rem"><?= $isEdit ? '✏️ İşaret Düzenle' : '➕ Yeni Trafik İşareti' ?></h1>

  <?php if ($error !== ''): ?>
    <div style="padding:10px 12px;border-radius:8px;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;margin-bottom:12px"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="card" style="padding:16px;max-width:560px">
    <div style="display:grid;gap:12px">
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">İşaret Adı *</label>
        <input name="name" required value="<?= e($form['name']) ?>" placeholder="Örn: Yol ver" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Kod</label>
        <input name="code" value="<?= e($form['code']) ?>" placeholder="Örn: TT-1" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Kategori</label>
        <select name="category_id" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="0">— Seçin —</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $form['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Görsel Yolu (signboards'a göre)</label>
        <input name="image" list="img-list" value="<?= e($form['image']) ?>" placeholder="Örn: tt-photos/yol-ver-levhasi-tt-1-1024x683.png" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
        <datalist id="img-list">
          <?php foreach ($existingImages as $im): ?>
            <option value="<?= e($im) ?>"></option>
          <?php endforeach; ?>
        </datalist>
      </div>
      <?php if ($form['image'] !== ''): ?>
        <div>
          <img src="/assets/img/signboards/<?= e(ltrim($form['image'], '/')) ?>" style="max-width:180px;max-height:180px;border:1px solid #e2e8f0;border-radius:8px" alt="">
        </div>
      <?php endif; ?>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Görsel Yükle (opsiyonel)</label>
        <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" style="width:100%;padding:8px;border:1px dashed #cbd5e1;border-radius:6px;background:#f8fafc">
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Açıklama</label>
        <textarea name="description" rows="2" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px"><?= e($form['description']) ?></textarea>
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Sıra</label>
        <input type="number" name="sort_order" value="<?= (int)$form['sort_order'] ?>" style="width:120px;padding:10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div>
        <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="aktif" value="1" <?= $form['aktif'] ? 'checked' : '' ?>> Aktif</label>
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" style="padding:12px 22px;border:0;border-radius:8px;background:#1e3a8a;color:#fff;font-weight:800;cursor:pointer"><?= $isEdit ? 'Kaydet' : 'Oluştur' ?></button>
        <a href="/admin/traffic-signs.php" style="padding:12px 18px;border-radius:8px;background:#e2e8f0;color:#334155;font-weight:700;text-decoration:none">İptal</a>
      </div>
    </div>
  </form>
<?php require __DIR__ . '/_layout_bottom.php'; ?>

