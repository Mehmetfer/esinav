<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$pageTitle = 'Özet Panel';
$activeMenu = 'ozet';

$kursiyerSayisi = 0;
$yoneticiSayisi = 0;
$soruSayisi = 0;
try {
    require_once dirname(__DIR__) . '/includes/esinav.php';
    $pdo = db();
    esinav_ensure_tables($pdo);
    esinav_seed_if_needed($pdo);
    $kursiyerSayisi = (int)$pdo->query('SELECT COUNT(*) FROM kursiyerler WHERE aktif = 1')->fetchColumn();
    $yoneticiSayisi = (int)$pdo->query('SELECT COUNT(*) FROM yoneticiler WHERE aktif = 1')->fetchColumn();
    $soruSayisi = (int)$pdo->query('SELECT COUNT(*) FROM sorular')->fetchColumn();
} catch (Throwable) {
}

require __DIR__ . '/_layout_top.php';
?>
  <div class="stats">
    <div class="stat">
      <div class="label">Aktif Kursiyer</div>
      <div class="value"><?= $kursiyerSayisi ?></div>
    </div>
    <div class="stat">
      <div class="label">Yönetici</div>
      <div class="value"><?= $yoneticiSayisi ?></div>
    </div>
    <div class="stat">
      <div class="label">Deneme Sınavı</div>
      <div class="value">0</div>
    </div>
    <div class="stat">
      <div class="label">Soru Bankası</div>
      <div class="value"><a href="/admin/sorular.php" style="color:inherit;text-decoration:none"><?= $soruSayisi ?></a></div>
    </div>
  </div>

  <div class="card">
    <h2 style="margin-top:0;color:var(--navy)">Hoş geldiniz</h2>
    <p style="color:var(--muted);margin-bottom:12px">
      METRO e-SINAV yönetici paneli. Soru havuzunu düzenlemek, görsel eklemek ve yeni soru hazırlamak için:
    </p>
    <a href="/admin/sorular.php" style="display:inline-block;background:#1e3a8a;color:#fff;padding:10px 16px;border-radius:8px;font-weight:700;text-decoration:none">Soru Havuzunu Aç</a>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>

