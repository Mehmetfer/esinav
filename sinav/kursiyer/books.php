<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'E-Kitaplar';
$activeMenu = 'books';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$userGroups = user_education_groups((int)$user['id']);
$currentGroup = $userGroups[0] ?? ['slug' => 'ehliyet', 'name' => 'Ehliyet', 'id' => 1];

$pdo = db();

$books = [];
try {
    $st = $pdo->prepare(
        "SELECT b.*, c.name as category_name 
         FROM books b 
         LEFT JOIN categories c ON b.category_id = c.id 
         WHERE b.group_id = ? AND b.aktif = 1 
         ORDER BY b.sort_order, b.id DESC LIMIT 200"
    );
    $st->execute([$currentGroup['id']]);
    $books = $st->fetchAll();
} catch (Throwable) {
}
?>
  <h1 class="k-page-title">📘 E-Kitaplar</h1>

  <?php if (empty($books)): ?>
    <section class="k-card">
      <div style="text-align:center;padding:40px;color:var(--muted)">
        <div style="font-size:3rem;margin-bottom:12px">📚</div>
        <div style="font-size:1.1rem;font-weight:600">Henüz kitap eklenmemiş</div>
      </div>
    </section>
  <?php else: ?>
    <div class="k-table-wrap">
      <table class="k-table">
        <thead>
          <tr>
            <th>Kitap</th>
            <th>Kategori</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $b): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:12px">
                <div style="width:40px;height:50px;background:#1e3a8a;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem">📘</div>
                <div>
                  <div style="font-weight:700;color:var(--navy)"><?= e((string)$b['title']) ?></div>
                  <?php if (!empty($b['description'])): ?>
                    <div style="font-size:.8rem;color:var(--muted)"><?= e(mb_substr((string)$b['description'], 0, 80)) ?>…</div>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td style="font-size:.85rem;color:var(--muted)"><?= e((string)($b['category_name'] ?? '—')) ?></td>
            <td>
              <?php if (!empty($b['file_path'])): ?>
                <a href="<?= e($b['file_path']) ?>" target="_blank" class="k-btn-sm">Oku</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
