<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'E-Kitaplar';
$activeMenu = (($_GET['group'] ?? 'ehliyet') === 'src') ? 'src-books' : 'ehliyet-books';
$activeGroup = $_GET['group'] ?? 'ehliyet';

$pdo = db();

$books = [];
try {
    $st = $pdo->prepare(
        "SELECT b.*, g.slug as group_slug, c.name as category_name 
         FROM books b 
         JOIN education_groups g ON b.group_id = g.id 
         LEFT JOIN categories c ON b.category_id = c.id 
         WHERE g.slug = ? 
         ORDER BY b.sort_order, b.id DESC LIMIT 200"
    );
    $st->execute([$activeGroup]);
    $books = $st->fetchAll();
} catch (Throwable) {
}

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">📘 E-Kitaplar — <?= e(strtoupper($activeGroup)) ?></h1>
    <a href="/admin/book-edit.php?group=<?= e($activeGroup) ?>" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni Kitap</a>
  </div>

  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
      <thead>
        <tr style="background:#f8fafc;text-align:left">
          <th style="padding:10px 12px">ID</th>
          <th style="padding:10px 12px">Başlık</th>
          <th style="padding:10px 12px">Kategori</th>
          <th style="padding:10px 12px">Dosya</th>
          <th style="padding:10px 12px;text-align:center">Durum</th>
          <th style="padding:10px 12px"></th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($books)): ?>
        <tr><td colspan="6" style="padding:20px;color:var(--muted)">Henüz kitap yok.</td></tr>
      <?php else: foreach ($books as $b): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px"><?= (int)$b['id'] ?></td>
          <td style="padding:10px 12px;font-weight:600"><?= e((string)$b['title']) ?></td>
          <td style="padding:10px 12px"><?= e((string)($b['category_name'] ?? '—')) ?></td>
          <td style="padding:10px 12px;font-size:.82rem"><?= e(basename((string)($b['file_path'] ?? ''))) ?: '—' ?></td>
          <td style="padding:10px 12px;text-align:center"><?= (int)$b['aktif'] === 1 ? '✅' : '⛔' ?></td>
          <td style="padding:10px 12px">
            <a href="/admin/book-edit.php?id=<?= (int)$b['id'] ?>" style="padding:4px 10px;background:#0f766e;color:#fff;border-radius:4px;text-decoration:none;font-size:.8rem">Düzenle</a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
