<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'İlerlemem';
$activeMenu = 'ilerlemem';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$userId = (int)($user['id'] ?? 0);
$groupId = (int)($user['group_id'] ?? 0);

if ($groupId <= 0) {
    $ug = user_groups($userId);
    $groupId = !empty($ug) ? (int)$ug[0] : 0;
}

// Get progress data
$ilerleme = [];
$toplamVideo = $izlenenVideo = 0;
$toplamKitap = $okunanKitap = 0;
$toplamSinav = $tamamlananSinav = 0;

try {
    $pdo = db();
    
    // Video stats
    $st = $pdo->prepare("SELECT COUNT(*) FROM videos WHERE group_id = ? AND aktif = 1");
    $st->execute([$groupId]);
    $toplamVideo = (int)$st->fetchColumn();
    
    $st = $pdo->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND group_id = ? AND content_type = 'video' AND is_completed = 1");
    $st->execute([$userId, $groupId]);
    $izlenenVideo = (int)$st->fetchColumn();
    
    // Kitap stats
    $st = $pdo->prepare("SELECT COUNT(*) FROM books WHERE group_id = ? AND aktif = 1");
    $st->execute([$groupId]);
    $toplamKitap = (int)$st->fetchColumn();
    
    $st = $pdo->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND group_id = ? AND content_type = 'book' AND is_completed = 1");
    $st->execute([$userId, $groupId]);
    $okunanKitap = (int)$st->fetchColumn();
    
    // Sinav stats
    $st = $pdo->prepare("SELECT COUNT(*) FROM exam_attempts WHERE user_id = ? AND group_id = ? AND status = 'bitti'");
    $st->execute([$userId, $groupId]);
    $toplamSinav = (int)$st->fetchColumn();
    
    $st = $pdo->prepare("SELECT COUNT(*) FROM exam_attempts WHERE user_id = ? AND group_id = ? AND status = 'bitti' AND is_passed = 1");
    $st->execute([$userId, $groupId]);
    $tamamlananSinav = (int)$st->fetchColumn();
    
} catch (Throwable) {}
?>
  <h1 class="k-page-title">İlerlemem</h1>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px">
    <section class="k-card" style="margin:0;text-align:center">
      <div style="font-size:2.5rem;margin-bottom:8px">🎬</div>
      <div style="font-size:1.8rem;font-weight:800;color:var(--navy)"><?= $izlenenVideo ?> / <?= $toplamVideo ?></div>
      <div style="font-size:.85rem;color:var(--muted)">Video İzlendi</div>
      <div style="margin-top:12px;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden">
        <div style="height:100%;width:<?= $toplamVideo > 0 ? round(($izlenenVideo / $toplamVideo) * 100) : 0 ?>%;background:#1e3a8a;border-radius:4px"></div>
      </div>
    </section>
    
    <section class="k-card" style="margin:0;text-align:center">
      <div style="font-size:2.5rem;margin-bottom:8px">📚</div>
      <div style="font-size:1.8rem;font-weight:800;color:var(--navy)"><?= $okunanKitap ?> / <?= $toplamKitap ?></div>
      <div style="font-size:.85rem;color:var(--muted)">Kitap Okundu</div>
      <div style="margin-top:12px;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden">
        <div style="height:100%;width:<?= $toplamKitap > 0 ? round(($okunanKitap / $toplamKitap) * 100) : 0 ?>%;background:#1e3a8a;border-radius:4px"></div>
      </div>
    </section>
    
    <section class="k-card" style="margin:0;text-align:center">
      <div style="font-size:2.5rem;margin-bottom:8px">📝</div>
      <div style="font-size:1.8rem;font-weight:800;color:var(--navy)"><?= $tamamlananSinav ?> / <?= $toplamSinav ?></div>
      <div style="font-size:.85rem;color:var(--muted)">Sınav Tamamlandı</div>
      <div style="margin-top:12px;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden">
        <div style="height:100%;width:<?= $toplamSinav > 0 ? round(($tamamlananSinav / $toplamSinav) * 100) : 0 ?>%;background:#16a34a;border-radius:4px"></div>
      </div>
    </section>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
