<?php
require_once __DIR__ . '/../includes/db.php';
$jsonPath = __DIR__ . "/videos.json";
$all_videos = [];

if (file_exists($jsonPath)) {
    $jsonContent = file_get_contents($jsonPath);
    $all_videos = json_decode($jsonContent, true) ?? [];
}

$currentGroup    = isset($_GET["grup"]) ? trim((string)$_GET["grup"]) : "ehliyet";
$currentHoca     = isset($_GET["hoca"]) ? trim((string)$_GET["hoca"]) : "ebru";
$currentCategory = isset($_GET["d"]) ? trim((string)$_GET["d"]) : "all";
$searchQuery     = isset($_GET["q"]) ? trim((string)$_GET["q"]) : "";

$hoca_videos = array_filter($all_videos, function ($video) use ($currentHoca) {
    $hoca = $video["hoca"] ?? "ebru";
    return $hoca === $currentHoca;
});

$categories = [];
foreach ($hoca_videos as $video) {
    $catKey   = $video["kategori"] ?? $video["category"] ?? "diger";
    $catLabel = $video["category_label"] ?? "DiĞer";
    if (!isset($categories[$catKey])) {
        $categories[$catKey] = ["label" => $catLabel, "count" => 0];
    }
    $categories[$catKey]["count"]++;
}

$filtered_videos = array_filter($hoca_videos, function ($video) use ($currentCategory, $searchQuery) {
    if ($currentCategory !== "all" && ($video["category"] ?? "") !== $currentCategory) {
        return false;
    }
    if ($searchQuery !== "") {
        $title = $video["title"] ?? "";
        if (mb_stripos($title, $searchQuery) === false) {
            return false;
        }
    }
    return true;
});

// Reklam kodu (admin panelinden yonetilir - ayarlar.reklam_kodu)
$reklamKodu = '';
try {
    $rRow = db()->query("SELECT deger FROM ayarlar WHERE anahtar = 'reklam_kodu' LIMIT 1")->fetch();
    if ($rRow && $rRow['deger'] !== null) {
        $reklamKodu = trim((string)$rRow['deger']);
    }
} catch (Throwable $e) {
    // ayarlar/reklam_kodu yoksa bos gec
}
?>\n<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Dersler</title>
    <style>
:root{--navy:#0b245b;--muted:#64748b;--bg:#f1f5f9}
*{box-sizing:border-box}
body{margin:0;font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;background:var(--bg);color:#0f172a}
.container{max-width:1200px;margin:0 auto;padding:20px 16px 60px}
.page-header h1{font-size:1.5rem;margin:0 0 4px;font-weight:900;color:var(--navy)}
.hoca-selector{display:flex;gap:10px;margin:14px 0 18px;background:#e2e8f0;padding:6px;border-radius:10px;width:fit-content}
.hoca-btn{padding:10px 24px;font-weight:700;text-decoration:none;color:#475569;border-radius:8px}
.hoca-btn.active{background:var(--navy);color:#fff}
.toolbar{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:14px}
.search-form{display:flex;gap:8px}
.search-form input{padding:9px 12px;border:1px solid #cbd5e1;border-radius:10px;min-width:220px;font-size:.9rem}
.search-form button{padding:9px 16px;border:0;border-radius:10px;background:var(--navy);color:#fff;font-weight:700;cursor:pointer}
.filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
.chip{padding:8px 14px;border-radius:20px;background:#e2e8f0;color:#334155;text-decoration:none;font-size:.83rem;font-weight:700}
.chip.active{background:var(--navy);color:#fff}
.video-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
@media(max-width:1024px){.video-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:768px){.video-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:480px){.video-grid{grid-template-columns:1fr}}
.video-card{background:#fff;border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;cursor:pointer;transition:transform .15s,box-shadow .15s}
.video-card:hover{transform:translateY(-3px);box-shadow:0 8px 22px rgba(15,23,42,.12)}
.thumb-wrapper{position:relative;padding-bottom:56.25%;background:#000}
.thumb-img{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover}
.play-icon{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:56px;height:56px;background:rgba(0,0,0,.7);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem}
.card-content{padding:12px}
.badge{display:inline-block;font-size:.72rem;font-weight:800;color:var(--navy);background:#eff6ff;border-radius:6px;padding:2px 8px;margin-bottom:6px}
.video-title{font-size:.88rem;font-weight:600;line-height:1.4}
.modal{position:fixed;inset:0;background:rgba(0,0,0,.85);display:flex;align-items:center;justify-content:center;z-index:9999;padding:16px}
.modal:not(.active){display:none}
.modal-content{width:min(900px,100%);position:relative}
.modal-close{position:absolute;top:-14px;right:-14px;width:42px;height:42px;border-radius:50%;border:0;background:#fff;color:#111;font-size:1.3rem;font-weight:900;cursor:pointer;z-index:2}
.iframe-container{position:relative;padding-bottom:56.25%;background:#000;border-radius:12px;overflow:hidden}
.iframe-container iframe{position:absolute;top:0;left:0;width:100%;height:100%;border:0}
.modal-title{color:#fff;text-align:center;font-size:.95rem;margin:12px 40px 0}
</style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <h1>Video Dersler</h1>
    </div>
    <?php if ($reklamKodu !== ''): ?>
    <div class="ad-slot" style="margin:12px 0 18px"><?php echo $reklamKodu; ?></div>
    <?php endif; ?>

    <div class="hoca-selector">
        <a href="videos.php?grup=<?php echo urlencode($currentGroup); ?>&hoca=ebru" class="hoca-btn <?php echo ($currentHoca === 'ebru' ? 'active' : ''); ?>">Ebru Hoca</a>
        <a href="videos.php?grup=<?php echo urlencode($currentGroup); ?>&hoca=cenk" class="hoca-btn <?php echo ($currentHoca === 'cenk' ? 'active' : ''); ?>">Cenk Hoca</a>
    </div>

    <div class="toolbar">
        <form class="search-form" method="get" action="videos.php">
            <input type="hidden" name="grup" value="<?php echo htmlspecialchars($currentGroup); ?>">
            <input type="hidden" name="hoca" value="<?php echo htmlspecialchars($currentHoca); ?>">
            <?php if ($currentCategory !== 'all'): ?>
                <input type="hidden" name="d" value="<?php echo htmlspecialchars($currentCategory); ?>">
            <?php endif; ?>
            <input type="text" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Video ara...">
            <button type="submit">Ara</button>
        </form>
    </div>

    <div class="filters">
        <?php
        $allCount = count($hoca_videos);
        $allUrl = "videos.php?grup=" . urlencode($currentGroup) . "&hoca=" . urlencode($currentHoca) . ($searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '');
        ?>
        <a class="chip <?php echo ($currentCategory === 'all' ? 'active' : ''); ?>" href="<?php echo htmlspecialchars($allUrl); ?>">
            Tömü (<?php echo $allCount; ?>)
        </a>
        <?php foreach ($categories as $key => $cat): 
            $catUrl = "videos.php?grup=" . urlencode($currentGroup) . "&hoca=" . urlencode($currentHoca) . "&d=" . urlencode($key);
            if ($searchQuery !== '') { $catUrl .= '&q=' . urlencode($searchQuery); }
        ?>
            <a class="chip <?php echo ($currentCategory === $key ? 'active' : ''); ?>" href="<?php echo htmlspecialchars($catUrl); ?>">
                <?php echo htmlspecialchars($cat['label']); ?> (<?php echo $cat['count']; ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <div class="video-grid">
        <?php foreach ($filtered_videos as $video): 
            $vId    = htmlspecialchars($video['id'] ?? '');
            $vTitle = htmlspecialchars($video['title'] ?? '');
            $vCat   = htmlspecialchars($video['category_label'] ?? 'Genel');
            $thumb  = "https://img.youtube.com/vi/{$vId}/hqdefault.jpg";
        ?>
            <div class="video-card" data-id="<?php echo $vId; ?>" data-title="<?php echo $vTitle; ?>">
                <div class="thumb-wrapper">
                    <img src="<?php echo $thumb; ?>" alt="<?php echo $vTitle; ?>" class="thumb-img" loading="lazy">
                    <div class="play-icon">&#9654;</div>
                </div>
                <div class="card-content">
                    <span class="badge"><?php echo $vCat; ?></span>
                    <div class="video-title"><?php echo $vTitle; ?></div>
                </div>
            </div>
<?php endforeach; ?>
    </div>
</div>

<div class="modal" id="videoModal">
    <div class="modal-content">
        <button type="button" class="modal-close" id="modalClose">&times;</button>
        <div class="iframe-container">
            <iframe id="youtubeIframe" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen title="Video"></iframe>
        </div>
        <div class="modal-title" id="modalTitle"></div>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('videoModal');
    const iframe = document.getElementById('youtubeIframe');
    const modalTitle = document.getElementById('modalTitle');
    const closeBtn = document.getElementById('modalClose');

    document.querySelectorAll('.video-card').forEach(card => {
        card.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            iframe.src = "https://www.youtube.com/embed/" + id + "?autoplay=1&rel=0";
            modalTitle.textContent = title;
            modal.classList.add('active');
        });
    });

    function closeModal() {
        modal.classList.remove('active');
        iframe.src = '';
    }

    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

});
</script>

</body>
</html>