<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC Video Dersler';
$activeMenu = 'src-video';
require __DIR__ . '/_layout_top.php';

// Video derslerini data/video-dersler.php'den al
$videoDosyasi = dirname(__DIR__) . '/data/video-dersler.php';
$videolar = [];
if (is_file($videoDosyasi)) {
    include $videoDosyasi;
}
?>
  <h1 class="k-page-title">SRC Video Dersler</h1>

  <?php if (empty($videolar)): ?>
    <section class="k-card">
      <div style="text-align:center;padding:40px;color:var(--muted)">
        <div style="font-size:3rem;margin-bottom:12px">🎬</div>
        <div style="font-size:1.1rem;font-weight:600;margin-bottom:8px">Henüz video ders eklenmemiş</div>
        <div style="font-size:.9rem">Video dersler admin panelinden eklenecektir.</div>
      </div>
    </section>
  <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px">
      <?php foreach ($videolar as $v): ?>
        <section class="k-card" style="margin:0">
          <div style="position:relative;padding-bottom:56.25%;background:#000;border-radius:8px;overflow:hidden;margin-bottom:12px">
            <iframe src="https://www.youtube.com/embed/<?= e($v['youtube_id'] ?? '') ?>" style="position:absolute;top:0;left:0;width:100%;height:100%" frameborder="0" allowfullscreen></iframe>
          </div>
          <div style="font-weight:700;color:var(--navy)"><?= e($v['baslik'] ?? '') ?></div>
          <?php if (!empty($v['ders'])): ?>
            <div style="font-size:.8rem;color:var(--muted);margin-top:4px"><?= e($v['ders']) ?></div>
          <?php endif; ?>
        </section>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
