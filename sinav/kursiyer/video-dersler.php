<?php
declare(strict_types=1);
/* METRO e-SINAV - Video Ders Paneli (tek dosya) */
$jsonPath = __DIR__ . '/videos.json';
$videos = [];
$raw = @file_get_contents($jsonPath);
if ($raw !== false) { $d = json_decode($raw, true); if (is_array($d)) $videos = $d; }
$filter = isset($_GET['d']) ? trim((string)$_GET['d']) : 'all';
$q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$counts = []; $labels = [];
foreach ($videos as $v) {
  $c = (string)($v['category'] ?? '');
  if ($c === '') continue;
  $counts[$c] = ($counts[$c] ?? 0) + 1;
  if (!isset($labels[$c]) && isset($v['category_label'])) $labels[$c] = (string)$v['category_label'];
}
$order = ['y2026','y2025','y2024','y2023','y2022','y2021','y2020','animasyon','ingilizce','diger'];
$cats = array_keys($counts);
usort($cats, function ($a, $b) use ($order) {
  $pa = array_search($a, $order, true); $pb = array_search($b, $order, true);
  return ($pa === false ? 999 : $pa) <=> ($pb === false ? 999 : $pb);
});
$filtered = array_values(array_filter($videos, function ($v) use ($filter, $q) {
  if ($filter !== 'all' && $filter !== '' && (string)($v['category'] ?? '') !== $filter) return false;
  if ($q !== '') {
    $t = (string)($v['title'] ?? '');
    $hit = function_exists('mb_stripos') ? mb_stripos($t, $q, 0, 'UTF-8') : stripos($t, $q);
    if ($hit === false) return false;
  }
  return true;
}));
$e = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Video Dersler - METRO e-SINAV</title>
<style>
:root{--navy:#0b245b;--muted:#64748b;--bg:#f1f5f9}
*{box-sizing:border-box}
body{margin:0;font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;background:var(--bg);color:#0f172a}
.wrap{max-width:1200px;margin:0 auto;padding:20px 16px 60px}
.pt{color:var(--navy);font-size:1.5rem;margin:0 0 4px;font-weight:900}
.lead{color:var(--muted);font-size:.9rem;margin:0 0 16px}
.tb{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:14px}
.sr{display:flex;gap:8px}
.sr input{padding:9px 12px;border:1px solid #cbd5e1;border-radius:10px;min-width:220px;font-size:.9rem}
.btn{padding:9px 16px;border:0;border-radius:10px;background:var(--navy);color:#fff;font-weight:700;cursor:pointer;font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center}
.chips{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px}
.chip{padding:8px 14px;border-radius:20px;background:#e2e8f0;color:#334155;text-decoration:none;font-size:.83rem;font-weight:700}
.chip.on{background:var(--navy);color:#fff}
.cnt{color:var(--muted);font-size:.85rem;margin:6px 0 14px}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
@media(max-width:1024px){.grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:768px){.grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:480px){.grid{grid-template-columns:1fr}}
.card{background:#fff;border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;cursor:pointer;text-align:left;padding:0;display:flex;flex-direction:column;font:inherit;transition:transform .15s,box-shadow .15s}
.card:hover{transform:translateY(-3px);box-shadow:0 8px 22px rgba(15,23,42,.12)}
.th{position:relative;padding-bottom:56.25%;background:#000;display:block}
.th img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.pl{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:56px;height:56px;background:rgba(0,0,0,.7);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem}
.mt{padding:12px;display:block}
.ct{display:inline-block;font-size:.72rem;font-weight:800;color:var(--navy);background:#eff6ff;border-radius:6px;padding:2px 8px;margin-bottom:6px}
.tt{font-size:.85rem;font-weight:600;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.empty{text-align:center;padding:50px 20px;color:var(--muted);background:#fff;border-radius:14px}
.lb{position:fixed;inset:0;background:rgba(0,0,0,.85);display:flex;align-items:center;justify-content:center;z-index:9999;padding:16px}
.lb[hidden]{display:none}
.lb-box{width:min(900px,100%)}
.lb-fr{position:relative;padding-bottom:56.25%;background:#000;border-radius:12px;overflow:hidden}
.lb-fr iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
.lb-tt{color:#fff;text-align:center;font-size:.95rem;margin:12px 40px 0}
.lb-x{position:absolute;top:14px;right:16px;width:42px;height:42px;border-radius:50%;border:0;background:#fff;color:#111;font-size:1.3rem;font-weight:900;cursor:pointer}
</style>
</head>
<body>
<div class="wrap">
<h1 class="pt">VIDEO DERSLER</h1>
<p class="lead">Videolar yila gore gruplanmistir. Izlemek icin karta tiklayin.</p>
<form class="tb" method="get" action="video-dersler.php">
<?php if ($filter !== 'all' && $filter !== ''): ?>
<input type="hidden" name="d" value="<?= $e($filter) ?>">
<?php endif; ?>
<div class="sr">
<input type="text" name="q" value="<?= $e($q) ?>" placeholder="Video ara..." aria-label="Ara">
<button type="submit" class="btn">Ara</button>
<?php if ($q !== '' || ($filter !== 'all' && $filter !== '')): ?>
<a class="btn" style="background:#64748b" href="video-dersler.php">Temizle</a>
<?php endif; ?>
</div>
</form>
<div class="chips">
<a class="chip <?= ($filter === 'all' || $filter === '') ? 'on' : '' ?>" href="video-dersler.php<?= $q !== '' ? '?q='.rawurlencode($q) : '' ?>">Tumu (<?= count($videos) ?>)</a>
<?php foreach ($cats as $c): ?>
<?php $href='video-dersler.php?d='.rawurlencode($c); if($q!==''){$href.='&q='.rawurlencode($q);} ?>
<a class="chip <?= $filter === $c ? 'on' : '' ?>" href="<?= $e($href) ?>"><?= $e($labels[$c] ?? $c) ?> (<?= (int)$counts[$c] ?>)</a>
<?php endforeach; ?>
</div>
<p class="cnt"><?= count($filtered) ?> video listeleniyor</p>
<?php if (!$filtered): ?>
<div class="empty"><div style="font-size:2.5rem">Film</div><p>Bu filtreye uygun video bulunamadi.</p></div>
<?php else: ?>
<div class="grid">
<?php foreach ($filtered as $v): ?>
<?php $yid=(string)($v['id']??''); $thumb=$yid!==''?'https://img.youtube.com/vi/'.rawurlencode($yid).'/hqdefault.jpg':''; $embed=$yid!==''?'https://www.youtube.com/embed/'.rawurlencode($yid):''; $cl=(string)($v['category_label']??($labels[(string)($v['category']??'')]??($v['category']??''))); ?>
<button type="button" class="card v-card" data-embed="<?= $e($embed) ?>" data-title="<?= $e((string)($v['title']??'')) ?>">
<span class="th"><?php if($thumb!==''): ?><img src="<?= $e($thumb) ?>" alt="" loading="lazy"><?php endif; ?><span class="pl">&#9654;</span></span>
<span class="mt"><span class="ct"><?= $e($cl) ?></span><span class="tt"><?= $e((string)($v['title']??'')) ?></span></span>
</button>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
<div class="lb" id="videoLb" hidden>
<button type="button" class="lb-x" id="videoLbClose" aria-label="Kapat">x</button>
<div class="lb-box"><div class="lb-fr" id="videoLbFrame"></div><p class="lb-tt" id="videoLbTitle"></p></div>
</div>
<script>
(function(){var lb=document.getElementById('videoLb'),fr=document.getElementById('videoLbFrame'),tt=document.getElementById('videoLbTitle'),x=document.getElementById('videoLbClose');if(!lb||!fr)return;function openLb(em,t){if(!em)return;fr.innerHTML='';var f=document.createElement('iframe');f.src=em+(em.indexOf('?')===-1?'?':'&')+'autoplay=1&rel=0';f.title=t||'Video';f.allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';f.allowFullscreen=true;fr.appendChild(f);if(tt)tt.textContent=t||'';lb.hidden=false;document.body.style.overflow='hidden';}function closeLb(){lb.hidden=true;fr.innerHTML='';if(tt)tt.textContent='';document.body.style.overflow='';}document.querySelectorAll('.v-card').forEach(function(b){b.addEventListener('click',function(){openLb(b.getAttribute('data-embed')||'',b.getAttribute('data-title')||'');});});if(x)x.addEventListener('click',closeLb);lb.addEventListener('click',function(ev){if(ev.target===lb)closeLb();});document.addEventListener('keydown',function(ev){if(ev.key==='Escape'&&!lb.hidden)closeLb();});})();
</script>
</body>
</html>
