<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pdo = db();
src_ensure_tables($pdo);

$konular = src_konular();

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$pageTitle = $isEdit ? 'Ders Notu Düzenle' : 'Yeni Ders Notu';
$activeMenu = 'src-ders-notlari';

// Silme
if (isset($_POST['delete_id'])) {
    $pdo->prepare("DELETE FROM src_ders_notlari WHERE id = ?")->execute([(int)$_POST['delete_id']]);
    redirect('/admin/src-ders-notlari.php');
}

// Aktif/Pasif toggle
if (isset($_POST['toggle_id'])) {
    $pdo->prepare("UPDATE src_ders_notlari SET aktif = IF(aktif = 1, 0, 1) WHERE id = ?")->execute([(int)$_POST['toggle_id']]);
    redirect('/admin/src-ders-notlari.php');
}

$form = ['konu' => 'is_sagligi', 'baslik' => '', 'ozet' => '', 'ico' => 'fa-book', 'icerik' => '', 'sira' => 0, 'aktif' => 1];

if ($isEdit) {
    $st = $pdo->prepare("SELECT * FROM src_ders_notlari WHERE id = ?");
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) {
        redirect('/admin/src-ders-notlari.php');
    }
    $form = [
        'konu' => (string)$row['konu'],
        'baslik' => (string)$row['baslik'],
        'ozet' => (string)($row['ozet'] ?? ''),
        'ico' => (string)($row['ico'] ?? 'fa-book'),
        'icerik' => (string)($row['icerik'] ?? ''),
        'sira' => (int)$row['sira'],
        'aktif' => (int)$row['aktif'],
    ];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id']) && !isset($_POST['toggle_id'])) {
    $form['konu'] = (string)($_POST['konu'] ?? 'is_sagligi');
    if (!isset($konular[$form['konu']])) {
        $form['konu'] = 'is_sagligi';
    }
    $form['baslik'] = trim((string)($_POST['baslik'] ?? ''));
    $form['ozet'] = trim((string)($_POST['ozet'] ?? ''));
    $form['ico'] = trim((string)($_POST['ico'] ?? 'fa-book'));
    $form['icerik'] = (string)($_POST['icerik'] ?? '');
    $form['sira'] = (int)($_POST['sira'] ?? 0);
    $form['aktif'] = isset($_POST['aktif']) ? 1 : 0;

    if ($form['baslik'] === '') {
        $error = 'Başlık zorunludur.';
    } else {
        if ($isEdit) {
            $pdo->prepare("UPDATE src_ders_notlari SET konu=?, baslik=?, ozet=?, ico=?, icerik=?, sira=?, aktif=? WHERE id=?")
                ->execute([$form['konu'], $form['baslik'], $form['ozet'], $form['ico'], $form['icerik'], $form['sira'], $form['aktif'], $id]);
        } else {
            $pdo->prepare("INSERT INTO src_ders_notlari (konu, baslik, ozet, ico, icerik, sira, aktif) VALUES (?,?,?,?,?,?,?)")
                ->execute([$form['konu'], $form['baslik'], $form['ozet'], $form['ico'], $form['icerik'], $form['sira'], $form['aktif']]);
        }
        redirect('/admin/src-ders-notlari.php');
    }
}

require __DIR__ . '/_layout_top.php';
?>
  <h1 style="margin:0 0 14px;color:var(--navy);font-size:1.25rem"><?= $isEdit ? '✏️ Ders Notu Düzenle' : '➕ Yeni Ders Notu' ?></h1>

  <?php if ($error !== ''): ?>
    <div style="padding:10px 12px;border-radius:8px;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;margin-bottom:12px"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="post" class="card" style="padding:16px;max-width:760px">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Konu</label>
        <select name="konu" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
          <?php foreach ($konular as $k => $ad): ?>
            <option value="<?= e($k) ?>" <?= $form['konu'] === $k ? 'selected' : '' ?>><?= e($ad) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Sıra</label>
        <input type="number" name="sira" value="<?= (int)$form['sira'] ?>" style="width:120px;padding:10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px">
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Başlık *</label>
        <input name="baslik" required value="<?= e($form['baslik']) ?>" placeholder="Örn: 01 - İş Sağlığı ve İş Güvenliği" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">İkon (Font Awesome)</label>
        <input name="ico" value="<?= e($form['ico']) ?>" placeholder="fa-book" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
    </div>
    <div style="margin-top:12px">
      <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Özet (liste görünümünde)</label>
      <textarea name="ozet" rows="2" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px"><?= e($form['ozet']) ?></textarea>
    </div>
    <div style="margin-top:12px">
      <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">İçerik (HTML desteklenir)</label>
      <textarea name="icerik" rows="16" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;font-family:monospace;font-size:.82rem"><?= e($form['icerik']) ?></textarea>
    </div>
    <div style="margin-top:12px">
      <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="aktif" value="1" <?= $form['aktif'] ? 'checked' : '' ?>> Aktif</label>
    </div>
    <div style="display:flex;gap:10px;margin-top:14px">
      <button type="submit" style="padding:12px 22px;border:0;border-radius:8px;background:#1e3a8a;color:#fff;font-weight:800;cursor:pointer"><?= $isEdit ? 'Kaydet' : 'Oluştur' ?></button>
      <a href="/admin/src-ders-notlari.php" style="padding:12px 18px;border-radius:8px;background:#e2e8f0;color:#334155;font-weight:700;text-decoration:none">İptal</a>
    </div>
  </form>
<?php require __DIR__ . '/_layout_bottom.php'; ?>

