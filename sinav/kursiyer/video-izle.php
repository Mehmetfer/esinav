<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Video İzle';
$activeMenu = 'videolar';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$userId = (int)($user['id'] ?? 0);
$groupId = (int)($user['group_id'] ?? 0);

if ($groupId <= 0) {
    $ug = user_groups($userId);
    $groupId = !empty($ug) ? (int)$ug[0] : 0;
}

$videoId = (int)($_GET['id'] ?? 0);
$video = null;

try {
    $pdo = db();
    $st = $pdo->prepare("SELECT * FROM videos WHERE id = ? AND group_id = ? AND aktif = 1 LIMIT 1");
    $st->execute([$videoId, $groupId]);
    $video = $st->fetch();
} catch (Throwable) {}

if (!$video) {
    redirect('/kursiyer/videos.php');
}

// Mark as watched
try {
    $pdo = db();
    $st = $pdo->prepare("INSERT INTO user_progress (user_id, group_id, content_type, content_id, progress_percent, is_completed, completed_at) VALUES (?,?,?,?,100,1,NOW()) ON DUPLICATE KEY UPDATE progress_percent=100, is_completed=1, completed_at=NOW()");
    $st->execute([$userId, $groupId, 'video', $videoId]);
} catch (Throwable) {}
?>
  <h1 class="k-page-title"><?= e($video['title']) ?></h1>

  <section class="k-card">
    <?php if (!empty($video['youtube_id'])): ?>
      <div style="position:relative;padding-bottom:56.25%;background:#000;border-radius:8px;overflow:hidden;margin-bottom:16px">
        <iframe src="https://www.youtube.com/embed/<?= e($video['youtube_id']) ?>" style="position:absolute;top:0;left:0;width:100%;height:100%" frameborder="0" allowfullscreen></iframe>
      </div>
    <?php elseif (!empty($video['video_file'])): ?>
      <video controls style="width:100%;border-radius:8px;margin-bottom:16px">
        <source src="<?= e($video['video_file']) ?>" type="video/mp4">
        Tarayıcınız video etiketini desteklemiyor.
      </video>
    <?php endif; ?>
    
    <?php if (!empty($video['description'])): ?>
      <div style="font-size:.95rem;color:var(--muted);margin-top:12px"><?= e($video['description']) ?></div>
    <?php endif; ?>
  </section>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
