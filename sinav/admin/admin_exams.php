<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Sınavlar';
$activeMenu = (($_GET['group'] ?? 'ehliyet') === 'src') ? 'src-exams' : 'ehliyet-exams';
$activeGroup = $_GET['group'] ?? 'ehliyet';

$pdo = db();

// Sınavları listele
$exams = [];
try {
    $st = $pdo->prepare(
        "SELECT e.*, g.name as group_name, c.name as category_name 
         FROM exams e 
         JOIN education_groups g ON e.group_id = g.id 
         LEFT JOIN categories c ON e.category_id = c.id 
         WHERE g.slug = ? 
         ORDER BY e.id DESC LIMIT 200"
    );
    $st->execute([$activeGroup]);
    $exams = $st->fetchAll();
} catch (Throwable) {
}

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">📝 Sınavlar — <?= e(strtoupper($activeGroup)) ?></h1>
    <a href="/admin/exam-edit.php?group=<?= e($activeGroup) ?>" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni Sınav</a>
  </div>

  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
      <thead>
        <tr style="background:#f8fafc;text-align:left">
          <th style="padding:10px 12px">ID</th>
          <th style="padding:10px 12px">Başlık</th>
          <th style="padding:10px 12px">Kategori</th>
          <th style="padding:10px 12px">Tip</th>
          <th style="padding:10px 12px;text-align:center">Soru</th>
          <th style="padding:10px 12px;text-align:center">Süre</th>
          <th style="padding:10px 12px;text-align:center">Geçme</th>
          <th style="padding:10px 12px;text-align:center">Durum</th>
          <th style="padding:10px 12px"></th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($exams)): ?>
        <tr><td colspan="9" style="padding:20px;color:var(--muted)">Henüz sınav yok.</td></tr>
      <?php else: foreach ($exams as $e): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px"><?= (int)$e['id'] ?></td>
          <td style="padding:10px 12px;font-weight:600"><?= e((string)$e['title']) ?></td>
          <td style="padding:10px 12px"><?= e((string)($e['category_name'] ?? '—')) ?></td>
          <td style="padding:10px 12px"><span style="padding:2px 8px;background:#e0e7ff;color:#3730a3;border-radius:4px;font-size:.75rem;font-weight:700"><?= e((string)$e['type']) ?></span></td>
          <td style="padding:10px 12px;text-align:center"><?= (int)$e['question_count'] ?></td>
          <td style="padding:10px 12px;text-align:center"><?= (int)$e['duration_seconds'] / 60 ?> dk</td>
          <td style="padding:10px 12px;text-align:center">%<?= (int)$e['passing_score'] ?></td>
          <td style="padding:10px 12px;text-align:center"><?= (int)$e['aktif'] === 1 ? '✅' : '⛔' ?></td>
          <td style="padding:10px 12px">
            <a href="/admin/exam-edit.php?id=<?= (int)$e['id'] ?>" style="padding:4px 10px;background:#0f766e;color:#fff;border-radius:4px;text-decoration:none;font-size:.8rem">Düzenle</a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
