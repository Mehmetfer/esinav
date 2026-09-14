<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/dil.php';
require_once dirname(__DIR__) . '/includes/education.php';

$user = require_role('kursiyer');
$pageTitle = $pageTitle ?? 'Kursiyer Paneli';
$activeMenu = $activeMenu ?? 'anasayfa';
$kursAdi = 'METRO SÜRÜCÜ KURSU';

try {
    $row = db()->query("SELECT deger FROM ayarlar WHERE anahtar = 'kurs_adi' LIMIT 1")->fetch();
    if ($row && $row['deger']) {
        $kursAdi = $row['deger'];
    }
} catch (Throwable) {
}

// Kullanıcının eğitim grubu
$kursiyerId = (int)($user['id'] ?? 0);
$userGroups = user_education_groups($kursiyerId);
$currentGroup = $userGroups[0] ?? ['slug' => 'ehliyet', 'name' => 'Ehliyet', 'id' => 1];

// Grup secimi: ?grup=slug ile gecis (oturumda hatirlanir)
$tumGruplar = [];
try {
    $tumGruplar = education_groups();
} catch (Throwable) {
    $tumGruplar = $userGroups;
}
if (isset($_GET['grup'])) {
    $istenen = strtolower(trim((string)$_GET['grup']));
    foreach ($tumGruplar as $g) {
        if ((string)$g['slug'] === $istenen) {
            $_SESSION['grup'] = $istenen;
            break;
        }
    }
}
if (!empty($_SESSION['grup'])) {
    foreach ($tumGruplar as $g) {
        if ((string)$g['slug'] === (string)$_SESSION['grup']) {
            $currentGroup = $g;
            break;
        }
    }
}

$groupSlug = $currentGroup['slug'];
$groupName = strtoupper($currentGroup['name']);
$groupIcon = ($groupSlug === 'src') ? '<i class="fas fa-truck-moving"></i>' : '<i class="fas fa-car-side"></i>';

// Kategorileri al (Eğitim bölümü için)
$categories = [];
try {
    $categories = education_categories((int)$currentGroup['id'], null, true);
} catch (Throwable) {
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> — <?= e(SITE_NAME) ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="icon" href="/assets/img/metro-logo.png">
</head>
<body class="<?= $groupSlug === 'src' ? 'theme-src' : 'theme-ehliyet' ?>">
<div class="app-shell">
  <aside class="sidebar<?= $groupSlug === 'src' ? ' sidebar-src' : ' sidebar-ehliyet' ?>" id="sidebar">
    <div class="sidebar-brand"><?= $groupIcon ?> <?= e($groupName) ?> EĞİTİMİ</div>
    <nav>
      <a class="nav-item <?= $activeMenu === 'anasayfa' ? 'active' : '' ?>" href="/kursiyer/index.php"><span class="ico"><i class="fas fa-home"></i></span> Ana Sayfa</a>

      <?php
      $srcTurler = [
          'src1'    => 'SRC 1',
          'src2'    => 'SRC 2',
          'src3'    => 'SRC 3',
          'src4'    => 'SRC 4',
          'odyudy'  => 'ODY-ÜDY',
          'kurye'   => 'SRC Kurye',
      ];
      $srcMenuler = [
          ['ico' => 'fa-book-open',  'ad' => 'Ders Notları', 'href' => 'src-ders-notlari.php', 'key' => 'src-ders-notlari'],
          ['ico' => 'fa-file-alt',   'ad' => 'Konulu Sınavlar', 'href' => 'src-konu-sinavlari.php', 'key' => 'src-konu-sinavlari'],
          ['ico' => 'fa-thumbtack',  'ad' => 'Çıkmış Sınav Soruları', 'href' => 'src-cikmis-sorular.php', 'key' => 'src-cikmis-sorular'],
          ['ico' => 'fa-pencil-alt', 'ad' => 'Deneme Sınavları', 'href' => 'src-deneme-sinavlari.php', 'key' => 'src-deneme-sinavlari'],
          ['ico' => 'fa-video',      'ad' => 'Uygulama Sınav Videoları', 'href' => 'src-videolar.php', 'key' => 'src-videolar'],
      ];
      $srcAcik = ($groupSlug === 'src') ? ' open' : '';
      $ehliyetAlt = [
          ['ico' => 'fa-book-open',     'ad' => 'E-Kitap', 'href' => 'books.php', 'key' => 'ekitap'],
          ['ico' => 'fa-film',          'ad' => 'Animasyonlar', 'href' => 'animations.php', 'key' => 'animations'],
          ['ico' => 'fa-user-tie',      'ad' => 'Video Dersler', 'href' => 'videos.php', 'key' => 'videos', 'hocalar' => [
              ['ad' => 'Ebru Hoca', 'href' => 'videos.php?hoca=ebru&grup=ehliyet', 'key' => 'videos-ebru'],
              ['ad' => 'Cenk Hoca', 'href' => 'videos.php?hoca=cenk&grup=ehliyet', 'key' => 'videos-cenk'],
          ]],
          ['ico' => 'fa-traffic-light', 'ad' => 'Trafik İşaretleri', 'href' => 'traffic-signs.php', 'key' => 'traffic-signs'],
          ['ico' => 'fa-file-alt',      'ad' => 'Konu Sınavları', 'href' => 'konu-sinavlari.php', 'key' => 'konu-sinavlari'],
          ['ico' => 'fa-pencil-alt',    'ad' => 'Deneme Sınavları', 'href' => 'deneme-sinavlari.php', 'key' => 'deneme-sinavlari'],
          ['ico' => 'fa-clock',         'ad' => 'E-Sınav', 'href' => 'e-sinav.php', 'key' => 'e-sinav'],
      ];
      $ehliyetAcik = ($groupSlug === 'ehliyet') ? ' open' : '';
      ?>

      <details class="nav-group"<?= $ehliyetAcik ?>>
        <summary class="nav-item <?= $groupSlug === 'ehliyet' ? 'active' : '' ?>" onclick="grupSec('ehliyet')">
          <span class="ico"><i class="fas fa-car-side"></i></span> Ehliyet Eğitimi <span class="nav-caret">▾</span>
        </summary>
        <?php foreach ($ehliyetAlt as $m): ?>
        <?php if (!empty($m['hocalar'])): ?>
        <details class="nav-group nav-sub-group"<?= in_array($activeMenu, array_column($m['hocalar'], 'key'), true) || $activeMenu === $m['key'] ? ' open' : '' ?>>
          <summary class="nav-sub-item <?= $activeMenu === $m['key'] ? 'active' : '' ?>">
            <span class="ico"><i class="fas <?= $m['ico'] ?>"></i></span> <?= e($m['ad']) ?> <span class="nav-caret">▾</span>
          </summary>
          <?php foreach ($m['hocalar'] as $h): ?>
          <a class="nav-sub-item nav-sub-sub-item <?= $activeMenu === $h['key'] ? 'active' : '' ?>" href="/kursiyer/<?= e($h['href']) ?>">
            <span class="ico"><i class="fas fa-user-tie"></i></span> <?= e($h['ad']) ?>
          </a>
          <?php endforeach; ?>
        </details>
        <?php else: ?>
        <a class="nav-sub-item <?= $activeMenu === $m['key'] ? 'active' : '' ?>" href="/kursiyer/<?= e($m['href']) ?>?grup=ehliyet">
          <span class="ico"><i class="fas <?= $m['ico'] ?>"></i></span> <?= e($m['ad']) ?>
        </a>
        <?php endif; ?>
        <?php endforeach; ?>
      </details>

      <details class="nav-group nav-src"<?= $srcAcik ?>>
        <summary class="nav-item <?= $groupSlug === 'src' ? 'active' : '' ?>" onclick="grupSec('src')">
          <span class="ico"><i class="fas fa-truck-moving"></i></span> SRC Eğitimi <span class="nav-caret">▾</span>
        </summary>
        <?php foreach ($srcMenuler as $m): ?>
        <details class="nav-group">
          <summary class="nav-item">
            <span class="ico"><i class="fas <?= $m['ico'] ?>"></i></span> <?= e($m['ad']) ?> <span class="nav-caret">▾</span>
          </summary>
          <?php foreach ($srcTurler as $slug => $ad): ?>
          <a class="nav-sub-item <?= $activeMenu === $m['key'] . '-' . $slug ? 'active' : '' ?>"
             href="/kursiyer/<?= e($m['href']) ?>?tur=<?= e($slug) ?>&grup=src"><?= e($ad) ?></a>
          <?php endforeach; ?>
        </details>
        <?php endforeach; ?>
      </details>

      <a class="nav-item <?= $activeMenu === 'sonuclar' ? 'active' : '' ?>" href="/kursiyer/sonuclar.php"><span class="ico"><i class="fas fa-chart-bar"></i></span> Sınav Sonuçları</a>
      <a class="nav-item <?= $activeMenu === 'ilerlemem' ? 'active' : '' ?>" href="/kursiyer/ilerlemem.php"><span class="ico"><i class="fas fa-user"></i></span> Profilim</a>
      <a class="nav-item" href="/logout.php"><span class="ico"><i class="fas fa-sign-out-alt"></i></span> Çıkış Yap</a>
    </nav>
  </aside>

  <script>
  function grupSec(g) {
    try { fetch('/kursiyer/index.php?grup=' + g, {cache: 'no-store'}); } catch (e) {}
    var b = document.body;
    var s = document.getElementById('sidebar');
    if (g === 'src') {
      b.classList.remove('theme-ehliyet'); b.classList.add('theme-src');
      s.classList.remove('sidebar-ehliyet'); s.classList.add('sidebar-src');
    } else {
      b.classList.remove('theme-src'); b.classList.add('theme-ehliyet');
      s.classList.remove('sidebar-src'); s.classList.add('sidebar-ehliyet');
    }
  }
  </script>

  <div class="main">
    <header class="topbar">
      <button type="button" class="menu-toggle" id="menuToggle" aria-label="Menü">☰</button>
      <?= dil_toggle_html() ?>
      <div class="user-chip">
        <span><?= e($user['name'] ?? '') ?></span>
        <span class="avatar"><?= e(mb_strtoupper(mb_substr((string)($user['name'] ?? 'Y'), 0, 1))) ?></span>
        <a class="btn-link" href="/logout.php">Çıkış</a>
      </div>
    </header>
    <main class="content">
