<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';
require_once dirname(__DIR__) . '/includes/questions.php';

$pageTitle = 'Soru Düzenle';
$activeMenu = 'questions';

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;

$groups = education_groups();
$group_id = (int)($_GET['group_id'] ?? 0);
if (!$group_id && $groups) {
    $group_id = (int)$groups[0]['id'];
}

$categories = $group_id ? categories_by_group($group_id) : [];
$topics = [];

$form = [
    'group_id' => $group_id,
    'category_id' => 0,
    'topic_id' => 0,
    'question_text' => '',
    'option_a' => '',
    'option_b' => '',
    'option_c' => '',
    'option_d' => '',
    'correct_answer' => 'A',
    'explanation' => '',
    'image' => '',
    'difficulty' => 'orta',
    'source' => '',
    'aktif' => 1,
];

$error = '';
$message = '';

if ($isEdit) {
    $q = question_get($id);
    if (!$q) { redirect('/admin/questions.php'); }
    $form = [
        'group_id' => (int)$q['group_id'],
        'category_id' => (int)$q['category_id'],
        'topic_id' => (int)$q['topic_id'],
        'question_text' => $q['question_text'],
        'option_a' => $q['option_a'],
        'option_b' => $q['option_b'],
        'option_c' => $q['option_c'],
        'option_d' => $q['option_d'],
        'correct_answer' => $q['correct_answer'],
        'explanation' => $q['explanation'] ?? '',
        'image' => $q['image'] ?? '',
        'difficulty' => $q['difficulty'] ?? 'orta',
        'source' => $q['source'] ?? '',
        'aktif' => (int)$q['aktif'],
    ];
    $group_id = $form['group_id'];
    $categories = categories_by_group($group_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['group_id'] = (int)($_POST['group_id'] ?? 0);
    $form['category_id'] = (int)($_POST['category_id'] ?? 0);
    $form['topic_id'] = (int)($_POST['topic_id'] ?? 0);
    $form['question_text'] = trim((string)($_POST['question_text'] ?? ''));
    $form['option_a'] = trim((string)($_POST['option_a'] ?? ''));
    $form['option_b'] = trim((string)($_POST['option_b'] ?? ''));
    $form['option_c'] = trim((string)($_POST['option_c'] ?? ''));
    $form['option_d'] = trim((string)($_POST['option_d'] ?? ''));
    $form['correct_answer'] = strtoupper(substr((string)($_POST['correct_answer'] ?? 'A'), 0, 1));
    $form['explanation'] = trim((string)($_POST['explanation'] ?? ''));
    $form['difficulty'] = (string)($_POST['difficulty'] ?? 'orta');
    $form['source'] = trim((string)($_POST['source'] ?? ''));
    $form['aktif'] = isset($_POST['aktif']) ? 1 : 0;

    if (!in_array($form['correct_answer'], ['A', 'B', 'C', 'D'], true)) {
        $form['correct_answer'] = 'A';
    }

    if ($form['question_text'] === '') {
        $error = 'Soru metni zorunludur.';
    } elseif ($form['option_a'] === '' || $form['option_b'] === '' || $form['option_c'] === '' || $form['option_d'] === '') {
        $error = 'Tüm şıklar doldurulmalıdır.';
    } else {
        try {
            $image = $form['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = (string)$finfo->file($_FILES['image']['tmp_name']);
                $map = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
                if (isset($map[$mime])) {
                    $dir = dirname(__DIR__) . '/assets/img/sorular/upload';
                    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
                        throw new RuntimeException('Upload klasörü oluşturulamadı.');
                    }
                    $name = 'q_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $map[$mime];
                    $dest = $dir . '/' . $name;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                        $image = 'upload/' . $name;
                    }
                }
            }

            $data = [
                'group_id' => $form['group_id'],
                'category_id' => $form['category_id'],
                'topic_id' => $form['topic_id'],
                'question_text' => $form['question_text'],
                'option_a' => $form['option_a'],
                'option_b' => $form['option_b'],
                'option_c' => $form['option_c'],
                'option_d' => $form['option_d'],
                'correct_answer' => $form['correct_answer'],
                'explanation' => $form['explanation'],
                'image' => $image,
                'difficulty' => $form['difficulty'],
                'source' => $form['source'],
                'aktif' => $form['aktif'],
            ];

            if ($isEdit) {
                question_update($id, $data);
                $message = 'Soru güncellendi.';
            } else {
                $id = question_create($data);
                $isEdit = true;
                $message = 'Soru oluşturuldu.';
            }
            $form['image'] = $image;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$imageUrl = null;
if ($form['image'] !== '') {
    $imageUrl = '/assets/img/sorular/' . ltrim(str_replace('\\', '/', $form['image']), '/');
}

require __DIR__ . '/_layout_top.php';
?>
  <h1 style="margin:0 0 14px;color:var(--navy);font-size:1.25rem"><?= $isEdit ? 'Soru Düzenle (#' . $id . ')' : 'Yeni Soru' ?></h1>
  <?php if ($message !== ''): ?><div class="alert alert-ok"><?= e($message) ?></div><?php endif; ?>
  <?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data" style="display:grid;gap:16px;max-width:900px">
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Eğitim Grubu</label>
        <select name="group_id" id="groupSelect" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
          <?php foreach ($groups as $g): ?>
            <option value="<?= (int)$g['id'] ?>" <?= $form['group_id'] === (int)$g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Kategori</label>
        <select name="category_id" id="categorySelect" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Seçiniz</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $form['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Konu</label>
        <select name="topic_id" id="topicSelect" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Seçiniz</option>
        </select>
      </div>
    </div>
    <div>
      <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Soru Metni</label>
      <textarea name="question_text" rows="4" required style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px"><?= e($form['question_text']) ?></textarea>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <?php foreach (['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $k => $h): ?>
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Şık <?= $h ?></label>
          <input name="option_<?= $k ?>" required value="<?= e($form['option_' . $k]) ?>" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
        </div>
      <?php endforeach; ?>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Doğru Cevap</label>
        <select name="correct_answer" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
          <?php foreach (['A', 'B', 'C', 'D'] as $h): ?>
            <option value="<?= $h ?>" <?= $form['correct_answer'] === $h ? 'selected' : '' ?>><?= $h ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Zorluk</label>
        <select name="difficulty" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="kolay" <?= $form['difficulty'] === 'kolay' ? 'selected' : '' ?>>Kolay</option>
          <option value="orta" <?= $form['difficulty'] === 'orta' ? 'selected' : '' ?>>Orta</option>
          <option value="zor" <?= $form['difficulty'] === 'zor' ? 'selected' : '' ?>>Zor</option>
        </select>
      </div>
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Kaynak</label>
        <input name="source" value="<?= e($form['source']) ?>" placeholder="Örn: MEB, Çıkmış" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
    </div>
    <div>
      <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Açıklama</label>
      <textarea name="explanation" rows="2" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px"><?= e($form['explanation']) ?></textarea>
    </div>
    <?php if ($imageUrl): ?>
      <div><img src="<?= e($imageUrl) ?>" alt="Soru görseli" style="max-width:200px;max-height:200px;border:1px solid #e2e8f0;border-radius:8px"></div>
    <?php endif; ?>
    <div>
      <label style="display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px">Görsel Yükle</label>
      <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" style="width:100%;padding:8px;border:1px dashed #cbd5e1;border-radius:6px;background:#f8fafc">
    </div>
    <div><label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="aktif" value="1" <?= $form['aktif'] ? 'checked' : '' ?>> Aktif</label></div>
    <div style="display:flex;gap:10px">
      <button type="submit" style="padding:12px 22px;border:0;border-radius:8px;background:#1e3a8a;color:#fff;font-weight:800;cursor:pointer"><?= $isEdit ? 'Kaydet' : 'Oluştur' ?></button>
      <a href="/admin/questions.php" style="padding:12px 18px;border-radius:8px;background:#e2e8f0;color:#334155;font-weight:700;text-decoration:none">İptal</a>
    </div>
  </form>
  <script>
  document.getElementById('groupSelect').addEventListener('change', function() {
    window.location.href = '/admin/question-edit.php?group_id=' + this.value;
  });
  </script>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
