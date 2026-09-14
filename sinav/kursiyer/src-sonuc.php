<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC Sonuç';
$activeMenu = 'src';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$kursiyerId = (int)($user['id'] ?? 0);
$error = '';
$istatistik = null;
$oturum = null;

$oturumId = (int)($_GET['id'] ?? 0);

try {
    $pdo = db();
    src_ensure_tables($pdo);
    
    if ($oturumId > 0) {
        $st = $pdo->prepare("SELECT * FROM src_oturum WHERE id = ? AND kursiyer_id = ? AND durum = 'bitti' LIMIT 1");
        $st->execute([$oturumId, $kursiyerId]);
        $oturum = $st->fetch();
    }
    
    if (!$oturum) {
        // son oturumu bul
        $st = $pdo->prepare("SELECT * FROM src_oturum WHERE kursiyer_id = ? AND durum = 'bitti' ORDER BY id DESC LIMIT 1");
        $st->execute([$kursiyerId]);
        $oturum = $st->fetch();
    }
    
    if ($oturum) {
        $istatistik = src_istatistik($pdo, (int)$oturum['id']);
    } else {
        $error = 'Sonuç bulunamadı.';
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
  <h1 class="k-page-title">SRC Sonuç</h1>

  <?php if ($error !== ''): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
  <?php endif; ?>

  <?php if ($istatistik): ?>
  <section class="k-card" style="text-align:center">
    <div style="font-size:3rem;margin-bottom:8px"><?= ((int)$oturum['basarili'] === 1) ? '🎉' : '😔' ?></div>
    <div style="font-size:1.5rem;font-weight:800;color:var(--navy);margin-bottom:4px">
      <?= ((int)$oturum['basarili'] === 1) ? 'GEÇTİ' : 'KALDI' ?>
    </div>
    <div style="font-size:2.5rem;font-weight:900;color:#1e3a8a;margin:12px 0">
      <?= (int)$istatistik['puan'] ?> / 100
    </div>
    <div style="display:flex;justify-content:center;gap:24px;margin-top:16px;flex-wrap:wrap">
      <div>
        <div style="font-size:1.8rem;font-weight:800;color:#166534"><?= (int)$istatistik['dogru'] ?></div>
        <div style="font-size:.85rem;color:var(--muted)">Doğru</div>
      </div>
      <div>
        <div style="font-size:1.8rem;font-weight:800;color:#b91c1c"><?= (int)$istatistik['yanlis'] ?></div>
        <div style="font-size:.85rem;color:var(--muted)">Yanlış</div>
      </div>
      <div>
        <div style="font-size:1.8rem;font-weight:800;color:var(--muted)"><?= (int)$istatistik['bos'] ?></div>
        <div style="font-size:.85rem;color:var(--muted)">Boş</div>
      </div>
    </div>
  </section>

  <?php if (!empty($istatistik['dersler'])): ?>
  <section class="k-card" style="margin-top:16px">
    <h2 class="k-section-title">Ders Bazlı Dağılım</h2>
    <div style="display:grid;gap:12px">
      <?php foreach ($istatistik['dersler'] as $d): ?>
        <?php if ((int)$d['toplam'] > 0): ?>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:8px">
            <div>
              <div style="font-weight:700;color:var(--navy)"><?= e((string)$d['ad']) ?></div>
              <div style="font-size:.8rem;color:var(--muted)"><?= (int)$d['toplam'] ?> soru</div>
            </div>
            <div style="display:flex;gap:12px;font-size:.85rem">
              <span style="color:#166534;font-weight:700">✓ <?= (int)$d['dogru'] ?></span>
              <span style="color:#b91c1c;font-weight:700">✗ <?= (int)$d['yanlis'] ?></span>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <div style="margin-top:16px;text-align:center">
    <a href="/kursiyer/src.php" class="k-start-btn" style="text-decoration:none;display:inline-block">Ana Sayfaya Dön</a>
  </div>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
