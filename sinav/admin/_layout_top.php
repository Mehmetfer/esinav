<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';

$user = require_role('admin');
$pageTitle = $pageTitle ?? 'Yönetici Paneli';
$activeMenu = $activeMenu ?? 'ozet';
$kursAdi = 'METRO SÜRÜCÜ KURSU';

try {
    $row = db()->query("SELECT deger FROM ayarlar WHERE anahtar = 'kurs_adi' LIMIT 1")->fetch();
    if ($row && $row['deger']) {
        $kursAdi = $row['deger'];
    }
} catch (Throwable) {
    // ayarlar tablosu henuz yoksa varsayilan
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> — <?= e(SITE_NAME) ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
  <link rel="icon" href="/assets/img/metro-logo.png">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand"><?= e($kursAdi) ?></div>
    <nav>
      <a class="nav-item <?= $activeMenu === 'ozet' ? 'active' : '' ?>" href="/admin/index.php"><span class="ico">▣</span> Özet Panel</a>
      
      <a class="nav-item <?= $activeMenu === 'kursiyer' || str_starts_with((string)$activeMenu, 'kursiyer') ? 'active' : '' ?>" href="/admin/kursiyerler.php"><span class="ico">👤</span> Kursiyer İşlemleri</a>
      <?php if ($activeMenu === 'kursiyer' || str_starts_with((string)$activeMenu, 'kursiyer')): ?>
      <div class="nav-sub">
        <a class="<?= $activeMenu === 'kursiyer' ? 'active' : '' ?>" href="/admin/kursiyerler.php">Liste</a>
        <a class="<?= $activeMenu === 'kursiyer-ekle' ? 'active' : '' ?>" href="/admin/kursiyer-ekle.php">Bireysel Kayıt</a>
        <a href="#">Excel'den Aktarım</a>
        <a href="#">Mebbis'ten Aktarım</a>
      </div>
      <?php endif; ?>
      
      <a class="nav-item <?= $activeMenu === 'gruplar' || str_starts_with((string)$activeMenu, 'grup') ? 'active' : '' ?>" href="/admin/groups.php"><span class="ico">📁</span> Eğitim Grupları</a>
      <?php if ($activeMenu === 'gruplar' || str_starts_with((string)$activeMenu, 'grup')): ?>
      <div class="nav-sub">
        <a class="<?= $activeMenu === 'gruplar' ? 'active' : '' ?>" href="/admin/groups.php">Grup Listesi</a>
        <a class="<?= $activeMenu === 'kategoriler' ? 'active' : '' ?>" href="/admin/categories.php">Kategoriler</a>
        <a class="<?= $activeMenu === 'konular' ? 'active' : '' ?>" href="/admin/topics.php">Konular</a>
      </div>
      <?php endif; ?>
      
      <a class="nav-item <?= $activeMenu === 'sorular' || str_starts_with((string)$activeMenu, 'sorular') ? 'active' : '' ?>" href="/admin/questions.php"><span class="ico">?</span> Soru Havuzu</a>
      <?php if ($activeMenu === 'sorular' || str_starts_with((string)$activeMenu, 'sorular')): ?>
      <div class="nav-sub">
        <a class="<?= $activeMenu === 'sorular' ? 'active' : '' ?>" href="/admin/questions.php">Tüm Sorular</a>
        <a class="<?= $activeMenu === 'soru-ekle' ? 'active' : '' ?>" href="/admin/question-edit.php">Yeni Soru</a>
        <a class="<?= $activeMenu === 'soru-import' ? 'active' : '' ?>" href="/admin/question-import.php">Toplu İçe Aktar</a>
      </div>
      <?php endif; ?>
      
      <a class="nav-item <?= $activeMenu === 'sinavlar' || str_starts_with((string)$activeMenu, 'sinav') ? 'active' : '' ?>" href="/admin/exams.php"><span class="ico">✎</span> Sınavlar</a>
      <?php if ($activeMenu === 'sinavlar' || str_starts_with((string)$activeMenu, 'sinav')): ?>
      <div class="nav-sub">
        <a class="<?= $activeMenu === 'sinavlar' ? 'active' : '' ?>" href="/admin/exams.php">Sınav Listesi</a>
        <a class="<?= $activeMenu === 'sinav-olustur' ? 'active' : '' ?>" href="/admin/exam-create.php">Sınav Oluştur</a>
      </div>
      <?php endif; ?>
      
      <a class="nav-item <?= $activeMenu === 'videolar' || str_starts_with((string)$activeMenu, 'video') ? 'active' : '' ?>" href="/admin/videos.php"><span class="ico">▶️</span> Videolar</a>
      <a class="nav-item <?= $activeMenu === 'kitaplar' ? 'active' : '' ?>" href="/admin/books.php"><span class="ico">📚</span> E-Kitaplar</a>
      <a class="nav-item <?= $activeMenu === 'animasyonlar' ? 'active' : '' ?>" href="/admin/animations.php"><span class="ico">🎞️</span> Animasyonlar</a>
      <a class="nav-item <?= $activeMenu === 'trafik' ? 'active' : '' ?>" href="/admin/traffic-signs.php"><span class="ico">🅿</span> Trafik İşaretleri</a>

      <a class="nav-item <?= $activeMenu === 'src-ders-notlari' || $activeMenu === 'src-sorular' || str_starts_with((string)$activeMenu, 'src-') ? 'active' : '' ?>" href="/admin/src-ders-notlari.php"><span class="ico">🚛</span> SRC Eğitimi</a>
      <?php if ($activeMenu === 'src-ders-notlari' || $activeMenu === 'src-sorular' || str_starts_with((string)$activeMenu, 'src-')): ?>
      <div class="nav-sub">
        <a class="<?= $activeMenu === 'src-ders-notlari' ? 'active' : '' ?>" href="/admin/src-ders-notlari.php">Ders Notları</a>
        <a class="<?= $activeMenu === 'src-sorular' ? 'active' : '' ?>" href="/admin/src-sorular.php">Soru Havuzu</a>
      </div>
      <?php endif; ?>

      <a class="nav-item <?= $activeMenu === 'raporlar' || str_starts_with((string)$activeMenu, 'rapor') ? 'active' : '' ?>" href="/admin/user-progress.php"><span class="ico">📊</span> İlerleme Takibi</a>
      <a class="nav-item <?= $activeMenu === 'ayarlar' ? 'active' : '' ?>" href="/admin/settings.php"><span class="ico">⚙️</span> Ayarlar</a>
    <a class="nav-item <?= $activeMenu === 'reklam' ? 'active' : '' ?>" href="/admin/reklam-yonetim.php"><span class="ico">📣</span> Reklam Yönetimi</a>
      </nav>
  </aside>

  <div class="main">
    <header class="topbar">
      <button type="button" class="menu-toggle" id="menuToggle" aria-label="Menü">☰</button>
      <div class="user-chip">
        <span><?= e($user['name'] ?? '') ?></span>
        <span class="avatar"><?= e(mb_strtoupper(mb_substr((string)($user['name'] ?? 'Y'), 0, 1))) ?></span>
        <a class="btn-link" href="/logout.php">Çıkış</a>
      </div>
    </header>
    <main class="content">
