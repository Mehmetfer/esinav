<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';
require_once dirname(__DIR__) . '/includes/questions.php';

$pageTitle = 'Soru Havuzu';
$activeMenu = 'questions';

$groups = education_groups();
$group_id = (int)($_GET['group_id'] ?? ($groups ? (int)$groups[0]['id'] : 0));
$category_id = (int)($_GET['category_id'] ?? 0);
$search = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

$categories = $group_id ? categories_by_group($group_id) : [];
$topics = $category_id ? topics_by_category($category_id) : [];

$filters = ['group_id' => $group_id, 'category_id' => $category_id, 'search' => $search];
$total = question_count($filters);
$totalPages = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;
$questions = question_list($filters, $perPage, $offset);

require __DIR__ . '/_layout_top.php';
?>
  <h1 style="margin:0 0 14px;color:var(--navy);font-size:1.25rem">Soru Havuzu</h1>

  <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
    <a href="/admin/question-edit.php?group_id=<?= $group_id ?>" style="padding:10px 18px;border-radius:8px;background:#1e3a8a;color:#fff;font-weight:700;text-decoration:none">+ Yeni Soru</a>
  </div>

  <div style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;margin-bottom:14px;align-items:end">
    <div>
      <label style="font-size:.75rem;color:var(--muted)">Ara</label>
      <input name="q" value="<?= e($search) ?>" placeholder="Soru metni..." style="width:100%;padding:9px;border:1px solid #d1d5db;border-radius:6px">
    </div>
    <div>
      <label style="font-size:.75rem;color:var(--muted)">Grup</label>
    </div>
    <div>
      <label style="font-size:.75rem;color:var(--muted)">Kategori</label>
    </div>
    <div></div>
  </div>

  <div style="overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
      <thead>
        <tr style="background:#f8fafc;text-align:left">
          <th style="padding:10px 12px">ID</th>
          <th style="padding:10px 12px">Grup</th>
          <th style="padding:10px 12px">Soru</th>
          <th style="padding:10px 12px">Doğru</th>
          <th style="padding:10px 12px">Durum</th>
          <th style="padding:10px 12px"></th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($questions)): ?>
        <tr><td colspan="6" style="padding:20px;color:var(--muted)">Soru yok.</td></tr>
      <?php else: foreach ($questions as $q): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px"><?= (int)$q['id'] ?></td>
          <td style="padding:10px 12px"><?= e($q['group_name'] ?? '') ?></td>
          <td style="padding:10px 12px;max-width:400px"><?= e(mb_substr((string)$q['question_text'], 0, 100)) ?><?= mb_strlen((string)$q['question_text']) > 100 ? '…' : '' ?></td>
          <td style="padding:10px 12px;font-weight:700"><?= e($q['correct_answer']) ?></td>
          <td style="padding:10px 12px"><?= (int)$q['aktif'] === 1 ? 'Aktif' : 'Pasif' ?></td>
          <td style="padding:10px 12px"><a href="/admin/question-edit.php?id=<?= (int)$q['id'] ?>" style="padding:6px 12px;background:#e2e8f0;color:#334155;border-radius:6px;text-decoration:none;font-weight:700;font-size:.8rem">Düzenle</a></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
  <div style="display:flex;gap:6px;margin-top:14px;justify-content:center">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?page=<?= $i ?>&group_id=<?= $group_id ?>&category_id=<?= $category_id ?>&q=<?= urlencode($search) ?>" style="padding:8px 14px;border-radius:6px;text-decoration:none;font-weight:700;<?= $i === $page ? 'background:#1e3a8a;color:#fff' : 'background:#e2e8f0;color:#334155' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
