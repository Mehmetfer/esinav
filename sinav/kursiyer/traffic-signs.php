<?php
declare(strict_types=1);

$pageTitle = 'Trafik İşaret ve Levhaları';
$activeMenu = 'traffic-signs';
require __DIR__ . '/_layout_top.php';
require_once dirname(__DIR__) . '/data/isaretler.php';

$kategoriler = metro_isaret_kategoriler();
$items = metro_isaretler();
$egitim = metro_isaret_egitim();
$sekiller = metro_isaret_sekiller();

$cat = (int)($_GET['cat'] ?? 0);
$q = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 24;
$showGuide = $cat === 0 && $q === '';

$filtered = array_values(array_filter($items, static function (array $it) use ($cat, $q): bool {
    if ($cat > 0 && (int)$it['cat'] !== $cat) {
        return false;
    }
    if ($q !== '') {
        $hay = ((string)($it['title'] ?? '')) . ' ' . ((string)($it['code'] ?? ''));
        $hit = function_exists('mb_stripos')
            ? mb_stripos($hay, $q, 0, 'UTF-8')
            : stripos($hay, $q);
        if ($hit === false) {
            return false;
        }
    }
    return true;
}));

$total = count($filtered);
$pages = max(1, (int)ceil($total / $perPage));
if ($page > $pages) {
    $page = $pages;
}
$slice = array_slice($filtered, ($page - 1) * $perPage, $perPage);

$imgBase = '../assets/img/signboards/';

$chipHref = static function (int $catId, string $q): string {
    $params = [];
    if ($catId > 0) {
        $params['cat'] = $catId;
    }
    if ($q !== '') {
        $params['q'] = $q;
    }
    return $params === [] ? 'traffic-signs.php' : 'traffic-signs.php?' . http_build_query($params);
};
?>
  <h1 class="k-page-title">TRAFİK İŞARET VE LEVHALARI</h1>
  <p class="k-sign-lead">
    Sürücü adayları için trafik levhaları gruplarına göre düzenlenmiş katalog.
    Aşağıdan grubu seçin veya arama yapın; işarete tıklayınca büyütülür.
  </p>

  <?php if ($showGuide): ?>
  <section class="k-sign-guide">
    <?php foreach ($egitim as $blok): ?>
      <article class="k-sign-guide-card">
        <h2><?= e($blok['title']) ?></h2>
        <p><?= e($blok['body']) ?></p>
        <?php if (!empty($blok['items'])): ?>
          <ul>
            <?php foreach ($blok['items'] as $li): ?>
              <li><?= e($li) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>

    <div class="k-sign-shapes">
      <h2>Levha şekilleri</h2>
      <div class="k-sign-shape-grid">
        <?php foreach ($sekiller as $s): ?>
          <div class="k-sign-shape">
            <strong><?= e($s['title']) ?></strong>
            <span><?= e($s['text']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <article class="k-sign-guide-card">
      <h2>Trafik Tanzim İşaretleri (TT)</h2>
      <p>
        Trafiğin düzenini ve uyulması gereken kuralları belirten levhalardır.
        Aşağıdaki listeden <strong>Trafik Tanzim İşaretleri (TT)</strong> filtresini seçerek
        TT-1 Yol Ver, TT-2 Dur ve diğer tüm TT levhalarını görselleriyle inceleyebilirsiniz.
      </p>
      <p style="margin-bottom:0">
        <a class="k-chip active" href="<?= e($chipHref(2, '')) ?>">TT grubunu aç →</a>
      </p>
    </article>
  </section>
  <?php endif; ?>

  <form class="k-sign-toolbar" method="get" action="traffic-signs.php">
    <?php if ($cat > 0): ?>
      <input type="hidden" name="cat" value="<?= (int)$cat ?>">
    <?php endif; ?>
    <div class="k-sign-search">
      <input type="text" name="q" value="<?= e($q) ?>" placeholder="Örn: yol ver, TT-2, hız…" aria-label="Ara">
      <button type="submit" class="k-btn-sm">Ara</button>
    </div>
  </form>

  <div class="k-filters">
    <a class="k-chip <?= $cat === 0 ? 'active' : '' ?>" href="<?= e($chipHref(0, $q)) ?>">Tümü</a>
    <?php foreach ($kategoriler as $id => $label): ?>
      <a class="k-chip <?= $cat === (int)$id ? 'active' : '' ?>" href="<?= e($chipHref((int)$id, $q)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if ($cat === 2 && $q === ''): ?>
    <p class="k-sign-cat-intro">
      <strong>TT grubu:</strong> Yasaklama, kısıtlama ve mecburiyetleri bildiren yuvarlak / mavi mecburi yön levhalarıdır.
    </p>
  <?php endif; ?>

  <?php if ($total === 0): ?>
    <div class="k-empty">
      <div class="k-empty-illu">🅿</div>
      <p>Bu filtreye uygun işaret bulunamadı.</p>
    </div>
  <?php else: ?>
    <p class="k-sign-count"><?= (int)$total ?> işaret<?= $cat > 0 ? ' · ' . e($kategoriler[$cat] ?? '') : '' ?></p>
    <div class="k-sign-grid">
      <?php foreach ($slice as $it):
        $src = $imgBase . str_replace('\\', '/', (string)$it['file']);
        $catLabel = $kategoriler[(int)$it['cat']] ?? '';
        $title = (string)$it['title'];
        $code = (string)($it['code'] ?? '');
      ?>
        <button type="button"
                class="k-sign-card"
                data-src="<?= e($src) ?>"
                data-title="<?= e($title) ?>">
          <span class="k-sign-img-wrap">
            <img src="<?= e($src) ?>" alt="<?= e($title) ?>" loading="lazy"
                 onerror="this.style.opacity=.25">
          </span>
          <span class="k-sign-meta"><?= e($code !== '' ? $code . ' · ' . $catLabel : $catLabel) ?></span>
          <span class="k-sign-title"><?= e($title) ?></span>
        </button>
      <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
      <nav class="k-pager k-sign-pager" aria-label="Sayfa">
        <?php
        $qs = static function (int $p) use ($q, $cat): string {
            $params = ['page' => $p];
            if ($q !== '') {
                $params['q'] = $q;
            }
            if ($cat > 0) {
                $params['cat'] = $cat;
            }
            return 'traffic-signs.php?' . http_build_query($params);
        };
        ?>
        <a class="k-chip<?= $page <= 1 ? ' disabled' : '' ?>" href="<?= $page <= 1 ? '#' : e($qs($page - 1)) ?>">‹</a>
        <span class="k-chip active"><?= (int)$page ?> / <?= (int)$pages ?></span>
        <a class="k-chip<?= $page >= $pages ? ' disabled' : '' ?>" href="<?= $page >= $pages ? '#' : e($qs($page + 1)) ?>">›</a>
      </nav>
    <?php endif; ?>
  <?php endif; ?>

  <div class="k-lightbox" id="signLightbox" hidden>
    <button type="button" class="k-lightbox-close" id="signLbClose" aria-label="Kapat">×</button>
    <img id="signLbImg" src="" alt="">
    <p id="signLbTitle" class="k-lightbox-title"></p>
  </div>
<?php
$extraScript = <<<'JS'
(function () {
  var lb = document.getElementById('signLightbox');
  var img = document.getElementById('signLbImg');
  var title = document.getElementById('signLbTitle');
  var closeBtn = document.getElementById('signLbClose');
  if (!lb || !img || !title) return;

  function openLb(src, t) {
    img.src = src;
    img.alt = t || '';
    title.textContent = t || '';
    lb.hidden = false;
    document.body.style.overflow = 'hidden';
  }
  function closeLb() {
    lb.hidden = true;
    img.src = '';
    document.body.style.overflow = '';
  }

  document.querySelectorAll('.k-sign-card').forEach(function (btn) {
    btn.addEventListener('click', function () {
      openLb(btn.getAttribute('data-src') || '', btn.getAttribute('data-title') || '');
    });
  });
  if (closeBtn) closeBtn.addEventListener('click', closeLb);
  lb.addEventListener('click', function (e) {
    if (e.target === lb) closeLb();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !lb.hidden) closeLb();
  });
})();
JS;
require __DIR__ . '/_layout_bottom.php';
