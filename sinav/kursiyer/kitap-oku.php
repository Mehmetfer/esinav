<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'E-Kitap Oku';
$activeMenu = 'kitaplar';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$userId = (int)($user['id'] ?? 0);
$groupId = (int)($user['group_id'] ?? 0);

if ($groupId <= 0) {
    $ug = user_groups($userId);
    $groupId = !empty($ug) ? (int)$ug[0] : 0;
}

$bookId = (int)($_GET['id'] ?? 0);
$book = null;

try {
    $pdo = db();
    $st = $pdo->prepare("SELECT * FROM books WHERE id = ? AND group_id = ? AND aktif = 1 LIMIT 1");
    $st->execute([$bookId, $groupId]);
    $book = $st->fetch();
} catch (Throwable) {}

if (!$book) {
    redirect('/kursiyer/books.php');
}

// Mark as read
try {
    $pdo = db();
    $st = $pdo->prepare("INSERT INTO user_progress (user_id, group_id, content_type, content_id, progress_percent, is_completed, completed_at) VALUES (?,?,?,?,100,1,NOW()) ON DUPLICATE KEY UPDATE progress_percent=100, is_completed=1, completed_at=NOW()");
    $st->execute([$userId, $groupId, 'book', $bookId]);
} catch (Throwable) {}
?>
  <h1 class="k-page-title"><?= e($book['title']) ?></h1>

  <section class="k-card">
    <?php if (!empty($book['file_path'])): ?>
      <div style="margin-bottom:16px">
        <a href="<?= e($book['file_path']) ?>" target="_blank" class="k-start-btn" style="text-decoration:none;display:inline-block">PDF Aç</a>
      </div>
      <iframe src="<?= e($book['file_path']) ?>" style="width:100%;height:80vh;border:1px solid #e2e8f0;border-radius:8px"></iframe>
    <?php else: ?>
      <div style="text-align:center;padding:40px;color:var(--muted)">
        <div style="font-size:3rem;margin-bottom:12px">📖</div>
        <div style="font-size:1.1rem;font-weight:600">Kitap dosyası bulunamadı</div>
      </div>
    <?php endif; ?>
    
    <?php if (!empty($book['description'])): ?>
      <div style="font-size:.95rem;color:var(--muted);margin-top:12px"><?= e($book['description']) ?></div>
    <?php endif; ?>
  </section>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
