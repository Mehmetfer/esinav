<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC';
$activeMenu = 'src';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$kursiyerId = (int)($user['id'] ?? 0);
$error = '';
$soru = null;
$oturum = null;
$istatistik = null;

try {
    $pdo = db();
    src_ensure_tables($pdo);
    $oturum = src_aktif_oturum($pdo, $kursiyerId);
    if (!$oturum) {
        redirect('/kursiyer/src.php');
    }
    
    $kalan = src_kalan_sn($oturum);
    if ($kalan <= 0) {
        redirect('/kursiyer/src-bitir.php');
    }
    
    $sira = max(1, (int)($_GET['q'] ?? 1));
    $soru = src_oturum_soru($pdo, (int)$oturum['id'], $sira);
    
    if (!$soru) {
        redirect('/kursiyer/src-bitir.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cevap'])) {
        src_cevap_kaydet($pdo, (int)$oturum['id'], $sira, (string)$_POST['cevap']);
        $sonraki = $sira + 1;
        $sonSoru = (int)$pdo->prepare("SELECT MAX(sira) FROM src_soru WHERE oturum_id = ?")->execute([(int)$oturum['id']]);
        redirect('/kursiyer/src-soru.php?q=' . $sonraki);
    }
    
    $istatistik = src_istatistik($pdo, (int)$oturum['id']);
} catch (Throwable $e) {
    $error = $e->getMessage();
}

$gorselUrl = null;
if (!empty($soru['gorsel'])) {
    $gorselUrl = '/assets/img/sorular/' . ltrim(str_replace('\\', '/', (string)$soru['gorsel']), '/');
}
?>
  <h1 class="k-page-title">SRC - Soru <?= (int)$sira ?></h1>

  <?php if ($error !== ''): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
  <?php endif; ?>

  <section class="k-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <div style="font-size:.85rem;color:var(--muted)">Kalan Süre</div>
      <div style="font-size:1.2rem;font-weight:800;color:var(--navy)" id="kalanSure"><?= gmdate('H:i:s', $kalan) ?></div>
    </div>

    <?php if ($gorselUrl): ?>
      <div style="margin-bottom:16px">
        <img src="<?= e($gorselUrl) ?>" alt="Soru görseli" style="max-width:100%;max-height:300px;border:1px solid #e2e8f0;border-radius:8px">
      </div>
    <?php endif; ?>

    <div style="font-size:1.05rem;font-weight:600;color:var(--navy);margin-bottom:20px"><?= e((string)$soru['soru']) ?></div>

    <form method="post" style="display:grid;gap:12px">
      <?php foreach (['A' => 'secenek_a', 'B' => 'secenek_b', 'C' => 'secenek_c', 'D' => 'secenek_d'] as $h => $k): ?>
        <label style="display:flex;align-items:center;gap:12px;padding:14px 16px;border:2px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:all .2s">
          <input type="radio" name="cevap" value="<?= $h ?>" required style="width:20px;height:20px">
          <span style="font-weight:700;color:var(--navy);margin-right:8px"><?= $h ?></span>
          <span><?= e((string)($soru[$k] ?? '')) ?></span>
        </label>
      <?php endforeach; ?>
      <div style="display:flex;gap:10px;margin-top:12px">
        <button type="submit" class="k-start-btn" style="border:0;cursor:pointer">Sonraki →</button>
      </div>
    </form>
  </section>

  <section class="k-card" style="margin-top:16px">
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <?php for ($i = 1; $i <= 50; $i++): ?>
        <a href="src-soru.php?q=<?= $i ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:6px;text-decoration:none;font-weight:700;font-size:.8rem;<?= $i === $sira ? 'background:#1e3a8a;color:#fff' : 'background:#e2e8f0;color:#334155' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </section>

  <script>
  let kalan = <?= (int)$kalan ?>;
  function goster() {
    if (kalan <= 0) { window.location.href = '/kursiyer/src-bitir.php'; return; }
    const h = Math.floor(kalan / 3600);
    const m = Math.floor((kalan % 3600) / 60);
    const s = kalan % 60;
    document.getElementById('kalanSure').textContent = 
      String(h).padStart(2,'0') + ' : ' + String(m).padStart(2,'0') + ' : ' + String(s).padStart(2,'0');
    kalan--;
  }
  goster();
  setInterval(goster, 1000);
  </script>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
