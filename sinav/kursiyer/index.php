<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ana Sayfa — METRO e-SINAV</title>
  <link rel="stylesheet" href="/assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="icon" href="/assets/img/metro-logo.png">
  <style>
    .d-menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
      margin-top: 15px;
      margin-bottom: 25px;
    }
    .d-menu-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 22px 14px;
      border-radius: 16px;
      text-decoration: none;
      font-weight: 800;
      font-size: 0.95rem;
      text-align: center;
      box-shadow: 0 4px 14px rgba(15,23,42,.07);
      border: 1.5px solid rgba(0,0,0,.06);
      transition: transform .15s, box-shadow .15s;
    }
    .d-menu-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(15,23,42,.12); }
    .d-menu-card:active { transform: scale(.97); }
    .d-menu-card .d-menu-icon { font-size: 2rem; line-height: 1; }
    .d-menu-card.full {
      grid-column: 1 / -1;
      flex-direction: row;
      gap: 14px;
      padding: 18px 20px;
      font-size: 1.1rem;
    }
    .d-sub-text { font-size: 0.75rem; font-weight: 600; opacity: 0.8; }
    .nav-item-btn { cursor: pointer; user-select: none; }
  </style>
</head>
<body class="theme-ehliyet">
<div class="app-shell">
  <aside class="sidebar sidebar-ehliyet" id="sidebar">
    <div class="sidebar-brand" id="brand-title"><i class="fas fa-car-side"></i> EHLIYET EĞİTİMİ</div>
    <nav>
      <a class="nav-item active" href="/kursiyer/index.php"><span class="ico"><i class="fas fa-home"></i></span> Ana Sayfa</a>
      
      <!-- Ehliyet Eğitimi Ana Menüsü -->
      <details class="nav-group" id="group-ehliyet" open>
        <summary class="nav-item nav-item-btn" onclick="grupSec('ehliyet')">
          <span class="ico"><i class="fas fa-car-side"></i></span> Ehliyet Eğitimi <span class="nav-caret">▾</span>
        </summary>
        <a class="nav-sub-item" href="/kursiyer/books.php?grup=ehliyet"><span class="ico"><i class="fas fa-book-open"></i></span> E-Kitap</a>
        <a class="nav-sub-item" href="/kursiyer/animations.php?grup=ehliyet"><span class="ico"><i class="fas fa-film"></i></span> Animasyonlar</a>
        <details class="nav-group nav-sub-group" open>
          <summary class="nav-sub-item">
            <span class="ico"><i class="fas fa-user-tie"></i></span> Video Dersler <span class="nav-caret">▾</span>
          </summary>
          <a class="nav-sub-item" href="/kursiyer/videos.php?hoca=ebru&grup=ehliyet" style="padding-left:38px;"><span class="ico"><i class="fas fa-user-tie"></i></span> Ebru Hoca</a>
          <a class="nav-sub-item" href="/kursiyer/videos.php?hoca=cenk&grup=ehliyet" style="padding-left:38px;"><span class="ico"><i class="fas fa-user-tie"></i></span> Cenk Hoca</a>
        </details>
        <a class="nav-sub-item" href="/kursiyer/traffic-signs.php?grup=ehliyet"><span class="ico"><i class="fas fa-traffic-light"></i></span> Trafik İşaretleri</a>
        <a class="nav-sub-item" href="/kursiyer/konu-sinavlari.php?grup=ehliyet"><span class="ico"><i class="fas fa-file-alt"></i></span> Konu Sınavları</a>
        <a class="nav-sub-item" href="/kursiyer/deneme-sinavlari.php?grup=ehliyet"><span class="ico"><i class="fas fa-pencil-alt"></i></span> Deneme Sınavları</a>
        <a class="nav-sub-item" href="/kursiyer/e-sinav.php?grup=ehliyet"><span class="ico"><i class="fas fa-clock"></i></span> E-Sınav</a>
      </details>
      
      <!-- SRC Eğitimi Ana Menüsü -->
      <details class="nav-group nav-src" id="group-src">
        <summary class="nav-item nav-item-btn" onclick="grupSec('src')">
          <span class="ico"><i class="fas fa-truck-moving"></i></span> SRC Eğitimi <span class="nav-caret">▾</span>
        </summary>
        <details class="nav-group">
          <summary class="nav-item"><span class="ico"><i class="fas fa-book-open"></i></span> Ders Notları <span class="nav-caret">▾</span></summary>
          <a class="nav-sub-item" href="/kursiyer/src-ders-notlari.php?tur=src1&grup=src">SRC 1</a>
          <a class="nav-sub-item" href="/kursiyer/src-ders-notlari.php?tur=src2&grup=src">SRC 2</a>
          <a class="nav-sub-item" href="/kursiyer/src-ders-notlari.php?tur=src3&grup=src">SRC 3</a>
          <a class="nav-sub-item" href="/kursiyer/src-ders-notlari.php?tur=src4&grup=src">SRC 4</a>
          <a class="nav-sub-item" href="/kursiyer/src-ders-notlari.php?tur=odyudy&grup=src">ODY-ÜDY</a>
          <a class="nav-sub-item" href="/kursiyer/src-ders-notlari.php?tur=kurye&grup=src">SRC Kurye</a>
        </details>
        <details class="nav-group">
          <summary class="nav-item"><span class="ico"><i class="fas fa-file-alt"></i></span> Konulu Sınavlar <span class="nav-caret">▾</span></summary>
          <a class="nav-sub-item" href="/kursiyer/src-konu-sinavlari.php?tur=src1&grup=src">SRC 1</a>
          <a class="nav-sub-item" href="/kursiyer/src-konu-sinavlari.php?tur=src2&grup=src">SRC 2</a>
          <a class="nav-sub-item" href="/kursiyer/src-konu-sinavlari.php?tur=src3&grup=src">SRC 3</a>
          <a class="nav-sub-item" href="/kursiyer/src-konu-sinavlari.php?tur=src4&grup=src">SRC 4</a>
          <a class="nav-sub-item" href="/kursiyer/src-konu-sinavlari.php?tur=odyudy&grup=src">ODY-ÜDY</a>
          <a class="nav-sub-item" href="/kursiyer/src-konu-sinavlari.php?tur=kurye&grup=src">SRC Kurye</a>
        </details>
        <details class="nav-group">
          <summary class="nav-item"><span class="ico"><i class="fas fa-thumbtack"></i></span> Çıkmış Sınav Soruları <span class="nav-caret">▾</span></summary>
          <a class="nav-sub-item" href="/kursiyer/src-cikmis-sorular.php?tur=src1&grup=src">SRC 1</a>
          <a class="nav-sub-item" href="/kursiyer/src-cikmis-sorular.php?tur=src2&grup=src">SRC 2</a>
          <a class="nav-sub-item" href="/kursiyer/src-cikmis-sorular.php?tur=src3&grup=src">SRC 3</a>
          <a class="nav-sub-item" href="/kursiyer/src-cikmis-sorular.php?tur=src4&grup=src">SRC 4</a>
          <a class="nav-sub-item" href="/kursiyer/src-cikmis-sorular.php?tur=odyudy&grup=src">ODY-ÜDY</a>
          <a class="nav-sub-item" href="/kursiyer/src-cikmis-sorular.php?tur=kurye&grup=src">SRC Kurye</a>
        </details>
        <details class="nav-group">
          <summary class="nav-item"><span class="ico"><i class="fas fa-pencil-alt"></i></span> Deneme Sınavları <span class="nav-caret">▾</span></summary>
          <a class="nav-sub-item" href="/kursiyer/src-deneme-sinavlari.php?tur=src1&grup=src">SRC 1</a>
          <a class="nav-sub-item" href="/kursiyer/src-deneme-sinavlari.php?tur=src2&grup=src">SRC 2</a>
          <a class="nav-sub-item" href="/kursiyer/src-deneme-sinavlari.php?tur=src3&grup=src">SRC 3</a>
          <a class="nav-sub-item" href="/kursiyer/src-deneme-sinavlari.php?tur=src4&grup=src">SRC 4</a>
          <a class="nav-sub-item" href="/kursiyer/src-deneme-sinavlari.php?tur=odyudy&grup=src">ODY-ÜDY</a>
          <a class="nav-sub-item" href="/kursiyer/src-deneme-sinavlari.php?tur=kurye&grup=src">SRC Kurye</a>
        </details>
        <details class="nav-group">
          <summary class="nav-item"><span class="ico"><i class="fas fa-video"></i></span> Uygulama Sınav Videoları <span class="nav-caret">▾</span></summary>
          <a class="nav-sub-item" href="/kursiyer/src-videolar.php?tur=src1&grup=src">SRC 1</a>
          <a class="nav-sub-item" href="/kursiyer/src-videolar.php?tur=src2&grup=src">SRC 2</a>
          <a class="nav-sub-item" href="/kursiyer/src-videolar.php?tur=src3&grup=src">SRC 3</a>
          <a class="nav-sub-item" href="/kursiyer/src-videolar.php?tur=src4&grup=src">SRC 4</a>
          <a class="nav-sub-item" href="/kursiyer/src-videolar.php?tur=odyudy&grup=src">ODY-ÜDY</a>
          <a class="nav-sub-item" href="/kursiyer/src-videolar.php?tur=kurye&grup=src">SRC Kurye</a>
        </details>
      </details>
      
      <a class="nav-item" href="/kursiyer/sonuclar.php"><span class="ico"><i class="fas fa-chart-bar"></i></span> Sınav Sonuçları</a>
      <a class="nav-item" href="/kursiyer/ilerlemem.php"><span class="ico"><i class="fas fa-user"></i></span> Profilim</a>
      <a class="nav-item" href="/logout.php"><span class="ico"><i class="fas fa-sign-out-alt"></i></span> Çıkış Yap</a>
    </nav>
  </aside>
  
  <script>
  function grupSec(g) {
    try { fetch('/kursiyer/index.php?grup=' + g, {cache: 'no-store'}); } catch (e) {}
    var b = document.body;
    var s = document.getElementById('sidebar');
    var brand = document.getElementById('brand-title');
    var ehliyetSec = document.getElementById('menu-ehliyet');
    var srcSec = document.getElementById('menu-src');
    var groupEhliyet = document.getElementById('group-ehliyet');
    var groupSrc = document.getElementById('group-src');
    
    if (g === 'src') {
      b.classList.remove('theme-ehliyet'); b.classList.add('theme-src');
      s.classList.remove('sidebar-ehliyet'); s.classList.add('sidebar-src');
      if(brand) brand.innerHTML = '<i class="fas fa-truck-moving"></i> SRC EĞİTİMİ';
      if(ehliyetSec) ehliyetSec.style.display = 'none';
      if(srcSec) srcSec.style.display = 'block';
      if(groupSrc) groupSrc.open = true;
    } else {
      b.classList.remove('theme-src'); b.classList.add('theme-ehliyet');
      s.classList.remove('sidebar-src'); s.classList.add('sidebar-ehliyet');
      if(brand) brand.innerHTML = '<i class="fas fa-car-side"></i> EHLIYET EĞİTİMİ';
      if(ehliyetSec) ehliyetSec.style.display = 'block';
      if(srcSec) srcSec.style.display = 'none';
      if(groupEhliyet) groupEhliyet.open = true;
    }
  }
  
  // URL Parametresine Göre Otomatik Seçim Yap
  document.addEventListener("DOMContentLoaded", function() {
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('grup') === 'src') {
      grupSec('src');
    }
  });
  </script>

  <div class="main">
    <header class="topbar">
      <button type="button" class="menu-toggle" id="menuToggle" aria-label="Menü">☰</button>
      <div class="user-chip">
        <span>Yeni Kullanici</span>
        <span class="avatar">Y</span>
        <a class="btn-link" href="/logout.php">Çıkış</a>
      </div>
    </header>
    <main class="content">
      <h1 class="k-page-title">🚗 EĞİTİM PANELİ</h1>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px">
        <div class="k-card" style="text-align:center;padding:16px">
          <div style="font-size:2rem;font-weight:900;color:var(--navy)">0</div>
          <div style="font-size:.8rem;color:var(--muted)">Toplam Sınav</div>
        </div>
        <div class="k-card" style="text-align:center;padding:16px">
          <div style="font-size:2rem;font-weight:900;color:#166534">0</div>
          <div style="font-size:.8rem;color:var(--muted)">Başarılı</div>
        </div>
        <div class="k-card" style="text-align:center;padding:16px">
          <div style="font-size:2rem;font-weight:900;color:#1e40af">0</div>
          <div style="font-size:.8rem;color:var(--muted)">Çözülen Soru</div>
        </div>
        <div class="k-card" style="text-align:center;padding:16px">
          <div style="font-size:2rem;font-weight:900;color:#7c3aed">0</div>
          <div style="font-size:.8rem;color:var(--muted)">Video</div>
        </div>
      </div>

      <!-- Ehliyet Kart Grubu -->
      <div id="menu-ehliyet">
        <h2 class="k-menu-section k-menu-section-ehliyet">EHLİYET EĞİTİMİ</h2>
        <div class="d-menu-grid">
          <a class="d-menu-card" href="/kursiyer/books.php?grup=ehliyet" style="background:#eff6ff; color:#1d4ed8;">
            <span class="d-menu-icon">📘</span>
            <span>E-KİTAP</span>
            <span class="d-sub-text">İncelemek</span>
          </a>
          <a class="d-menu-card" href="/kursiyer/animations.php?grup=ehliyet" style="background:#f0fdfa; color:#0d9488;">
            <span class="d-menu-icon">🎞️</span>
            <span>ANİMASYONLAR</span>
            <span class="d-sub-text">İzle</span>
          </a>
          <a class="d-menu-card" href="/kursiyer/videos.php?hoca=ebru&grup=ehliyet" style="background:#fef2f2; color:#dc2626;">
            <span class="d-menu-icon">👩‍🏫</span>
            <span>EBRU HOCA</span>
            <span class="d-sub-text">Video Dersler</span>
          </a>
          <a class="d-menu-card" href="/kursiyer/videos.php?hoca=cenk&grup=ehliyet" style="background:#fef2f2; color:#dc2626;">
            <span class="d-menu-icon">👨‍🏫</span>
            <span>CENK HOCA</span>
            <span class="d-sub-text">Video Dersler</span>
          </a>
          <a class="d-menu-card" href="/kursiyer/traffic-signs.php?grup=ehliyet" style="background:#f0fdf4; color:#16a34a;">
            <span class="d-menu-icon">🚦</span>
            <span>TRAFİK İŞARETLERİ</span>
            <span class="d-sub-text">Öğren</span>
          </a>
          <a class="d-menu-card" href="/kursiyer/konu-sinavlari.php?grup=ehliyet" style="background:#f5f3ff; color:#7c3aed;">
            <span class="d-menu-icon">📝</span>
            <span>KONU SINAVLARI</span>
            <span class="d-sub-text">Başla</span>
          </a>
          <a class="d-menu-card" href="/kursiyer/deneme-sinavlari.php?grup=ehliyet" style="background:#fefce8; color:#ca8a04;">
            <span class="d-menu-icon">✏️</span>
            <span>DENEME SINAVLARI</span>
            <span class="d-sub-text">Başla</span>
          </a>
          <a class="d-menu-card full" href="/kursiyer/e-sinav.php?grup=ehliyet" style="background:#fff4ed; color:#e65c00;">
            <span class="d-menu-icon">⏱️</span>
            <span>E-SINAV</span>
            <span class="d-sub-text">Gerçek Sınava Başla</span>
          </a>
        </div>
      </div>

      <!-- SRC Kart Grubu -->
      <div id="menu-src" style="display:none;">
        <h2 class="k-menu-section k-menu-section-src">SRC EĞİTİMİ</h2>
        <div class="d-menu-grid">
          <a class="d-menu-card" href="/kursiyer/src-ders-notlari.php?grup=src" style="background:#eff6ff; color:#1d4ed8;">
            <span class="d-menu-icon">📚</span>
            <span>DERS NOTLARI</span>
            <span class="d-sub-text">SRC 1-4, ODY-ÜDY</span>
          </a>
          <a class="d-menu-card" href="/kursiyer/src-konu-sinavlari.php?grup=src" style="background:#f5f3ff; color:#7c3aed;">
            <span class="d-menu-icon">📑</span>
            <span>KONULU SINAVLAR</span>
            <span class="d-sub-text">Test Et</span>
          </a>
          <a class="d-menu-card" href="/kursiyer/src-cikmis-sorular.php?grup=src" style="background:#fefce8; color:#ca8a04;">
            <span class="d-menu-icon">📌</span>
            <span>ÇIKMIŞ SORULAR</span>
            <span class="d-sub-text">Önceki Sınavlar</span>
          </a>
          <a class="d-menu-card" href="/kursiyer/src-deneme-sinavlari.php?grup=src" style="background:#f0fdf4; color:#16a34a;">
            <span class="d-menu-icon">✏️</span>
            <span>DENEME SINAVLARI</span>
            <span class="d-sub-text">Deneme Çöz</span>
          </a>
          <a class="d-menu-card full" href="/kursiyer/src-videolar.php?grup=src" style="background:#fef2f2; color:#dc2626;">
            <span class="d-menu-icon">🎥</span>
            <span>UYGULAMA SINAV VİDEOLARI</span>
            <span class="d-sub-text">Görsel Anlatım</span>
          </a>
        </div>
      </div>
    </main>
  </div>
</div>

<script>
  document.getElementById('menuToggle')?.addEventListener('click', function () {
    document.getElementById('sidebar')?.classList.toggle('open');
  });
</script>
</body>
</html>
