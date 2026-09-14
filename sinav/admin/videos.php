<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';

$pageTitle  = 'Video Dersler';
$activeMenu = 'videolar';

// Kursiyer tarafindaki video katalogu: /kursiyer/videos.json
$jsonPath  = dirname(__DIR__) . '/kursiyer/videos.json';
$allVideos = [];
if (is_file($jsonPath)) {
    $allVideos = json_decode((string)file_get_contents($jsonPath), true) ?: [];
}

$hocalar = ['ebru' => 'Ebru Hoca', 'cenk' => 'Cenk Hoca'];

$hocaParam       = isset($_GET['hoca']) ? trim((string)$_GET['hoca']) : 'ebru';
$currentHoca     = array_key_exists($hocaParam, $hocalar) ? $hocaParam : 'ebru';
$currentCategory = trim((string)($_GET['d'] ?? 'all'));
$searchQuery     = trim((string)($_GET['q'] ?? ''));
$page            = max(1, (int)($_GET['p'] ?? 1));
$perPage         = 60;

$hocaVideos = array_values(array_filter($allVideos, static function ($v) use ($currentHoca): bool {
    return (($v['hoca'] ?? 'ebru') === $currentHoca);
}));

$categories = [];
foreach ($hocaVideos as $v) {
    $catKey   = (string)($v['kategori'] ?? $v['category'] ?? 'diger');
    $catLabel = (string)($v['category_label'] ?? 'Diğer');
    if (!isset($categories[$catKey])) {
        $categories[$catKey] = ['label' => $catLabel, 'count' => 0];
    }
    $categories[$catKey]['count']++;
}

$filteredVideos = array_values(array_filter($hocaVideos, static function ($v) use ($currentCategory, $searchQuery): bool {
    if ($currentCategory !== 'all' && (string)($v['kategori'] ?? $v['category'] ?? 'diger') !== $currentCategory) {
        return false;
    }
    if ($searchQuery !== '' && mb_stripos((string)($v['title'] ?? ''), $searchQuery) === false) {
        return false;
    }
    return true;
}));

$totalVideos = count($filteredVideos);
$totalPages  = max(1, (int)ceil($totalVideos / $perPage));
$page        = min($page, $totalPages);
$pageVideos  = array_slice($filteredVideos, ($page - 1) * $perPage, $perPage);

$hocaCounts = ['ebru' => 0, 'cenk' => 0];
foreach ($allVideos as $v) {
    $h = (string)($v['hoca'] ?? 'ebru');
    if (isset($hocaCounts[$h])) {
        $hocaCounts[$h]++;
    }
}

$pageUrl = static function (array $over = []) use ($currentHoca, $currentCategory, $searchQuery, $page): string {
    $params = array_filter([
        'hoca' => $currentHoca,
        'd'    => ($currentCategory !== 'all') ? $currentCategory : '',
        'q'    => $searchQuery,
        'p'    => ($page > 1) ? (string)$page : '',
    ], static fn($v) => $v !== '' && $v !== null);
    $params = array_merge($params, array_filter($over, static fn($v) => $v !== '' && $v !== null));
    return '/admin/videos.php' . ($params ? '?' . http_build_query($params) : '');
};

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">🎬 Video Dersler <span style="font-size:.85rem;font-weight:400;color:var(--muted)">(kursiyer paneli ile aynı katalog)</span></h1>
    <span style="padding:6px 12px;background:#eff6ff;color:var(--navy);border-radius:8px;font-weight:700;font-size:.85rem">Toplam <?= count($allVideos) ?> video</span>
  </div>

  <div class="stats" style="margin-bottom:16px">
    <div class="stat">
      <div class="label">Ebru Hoca</div>
      <div class="value"><?= (int)$hocaCounts['ebru'] ?></div>
    </div>
    <div class="stat">
      <div class="label">Cenk Hoca</div>
      <div class="value"><?= (int)$hocaCounts['cenk'] ?></div>
    </div>
    <div class="stat">
      <div class="label">Listelenen</div>
      <div class="value"><?= $totalVideos ?></div>
    </div>
  </div>

  <div class="card" style="padding:14px;margin-bottom:14px">
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px">
      <?php foreach ($hocalar as $hKey => $hLabel): ?>
        <a href="<?= e($pageUrl(['hoca' => $hKey, 'd' => '', 'p' => ''])) ?>"
           style="padding:9px 22px;font-weight:700;text-decoration:none;border-radius:8px;<?= $currentHoca === $hKey ? 'background:var(--navy);color:#fff' : 'background:#e2e8f0;color:#475569' ?>">
          <?= e($hLabel) ?> (<?= (int)$hocaCounts[$hKey] ?>)
        </a>
      <?php endforeach; ?>
    </div>
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <input type="hidden" name="hoca" value="<?= e($currentHoca) ?>">
      <input name="q" value="<?= e($searchQuery) ?>" placeholder="Video başlığına göre ara..." style="flex:1;min-width:220px;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px">
      <button type="submit" style="padding:9px 18px;border:0;border-radius:8px;background:var(--navy);color:#fff;font-weight:700;cursor:pointer">Ara</button>
      <?php if ($searchQuery !== '' || $currentCategory !== 'all'): ?>
        <a href="<?= e($pageUrl(['q' => '', 'd' => '', 'p' => ''])) ?>" style="padding:9px 14px;background:#fee2e2;color:#991b1b;border-radius:8px;text-decoration:none;font-weight:700;font-size:.8rem">Filtreleri temizle</a>
      <?php endif; ?>
    </form>
  </div>

  <?php if ($categories): ?>
  <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
    <a href="<?= e($pageUrl(['d' => '', 'p' => ''])) ?>" style="padding:6px 14px;border-radius:16px;text-decoration:none;font-size:.8rem;font-weight:700;<?= $currentCategory === 'all' ? 'background:var(--navy);color:#fff' : 'background:#e2e8f0;color:#334155' ?>">
      Tümü (<?= count($hocaVideos) ?>)
    </a>
    <?php foreach ($categories as $catKey => $cat): ?>
      <a href="<?= e($pageUrl(['d' => $catKey, 'p' => ''])) ?>" style="padding:6px 14px;border-radius:16px;text-decoration:none;font-size:.8rem;font-weight:700;<?= $currentCategory === $catKey ? 'background:var(--navy);color:#fff' : 'background:#e2e8f0;color:#334155' ?>">
        <?= e($cat['label']) ?> (<?= (int)$cat['count'] ?>)
      </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;margin-bottom:18px">
    <?php if (empty($pageVideos)): ?>
      <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted)">Kriterlere uyan video bulunamadı.</div>
    <?php else: foreach ($pageVideos as $v):
        $vId    = (string)($v['id'] ?? '');
        if ($vId === '') { continue; }
        $vTitle = (string)($v['title'] ?? '');
        $vCat   = (string)($v['category_label'] ?? 'Genel');
        $thumb  = 'https://img.youtube.com/vi/' . rawurlencode($vId) . '/hqdefault.jpg';
    ?>
      <div class="card" style="padding:0;overflow:hidden;display:flex;flex-direction:column">
        <a href="https://www.youtube.com/watch?v=<?= e($vId) ?>" target="_blank" rel="noopener" style="display:block;position:relative;padding-bottom:56.25%;background:#000;text-decoration:none">
          <img src="<?= e($thumb) ?>" alt="" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
        </a>
        <div style="padding:12px;display:flex;flex-direction:column;gap:8px;flex:1">
          <div style="font-weight:700;color:var(--navy);font-size:.85rem;line-height:1.4;flex:1"><?= e($vTitle) ?></div>
          <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
            <span style="padding:2px 8px;background:#eff6ff;color:var(--navy);border-radius:4px;font-size:.7rem;font-weight:800"><?= e($vCat) ?></span>
            <span style="padding:2px 8px;background:#f1f5f9;color:#475569;border-radius:4px;font-size:.7rem;font-weight:700"><?= e($hocalar[$currentHoca]) ?></span>
            <a href="https://www.youtube.com/watch?v=<?= e($vId) ?>" target="_blank" rel="noopener" style="margin-left:auto;padding:4px 10px;background:#0f766e;color:#fff;border-radius:4px;text-decoration:none;font-size:.72rem;font-weight:700">▶ İzle</a>
          </div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>

  <?php if ($totalPages > 1): ?>
  <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;justify-content:center;padding:10px 0 30px">
    <?php if ($page > 1): ?>
      <a href="<?= e($pageUrl(['p' => (string)($page - 1)])) ?>" style="padding:8px 14px;background:#e2e8f0;color:#334155;border-radius:8px;text-decoration:none;font-weight:700">‹ Önceki</a>
    <?php endif; ?>
    <span style="padding:8px 12px;color:var(--muted);font-size:.85rem">Sayfa <?= $page ?> / <?= $totalPages ?></span>
    <?php if ($page < $totalPages): ?>
      <a href="<?= e($pageUrl(['p' => (string)($page + 1)])) ?>" style="padding:8px 14px;background:#e2e8f0;color:#334155;border-radius:8px;text-decoration:none;font-weight:700">Sonraki ›</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>


