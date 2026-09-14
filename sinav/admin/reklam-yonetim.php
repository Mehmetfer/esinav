<?php
declare(strict_types=1);

$pageTitle = 'Reklam Yönetimi';
$activeMenu = 'reklam';
require __DIR__ . '/_layout_top.php';

$mesaj = '';
$hata = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kod = trim((string)($_POST['reklam_kodu'] ?? ''));
    try {
        $st = db()->prepare(
            "INSERT INTO ayarlar (anahtar, deger) VALUES ('reklam_kodu', :d)
             ON DUPLICATE KEY UPDATE deger = VALUES(deger)"
        );
        $st->execute([':d' => $kod]);
        $mesaj = 'Reklam kodu kaydedildi.';
    } catch (Throwable $e) {
        $hata = 'Kayıt sırasında hata oluştu: ' . $e->getMessage();
    }
}

$reklamKodu = '';
try {
    $row = db()->query("SELECT deger FROM ayarlar WHERE anahtar = 'reklam_kodu' LIMIT 1")->fetch();
    if ($row) {
        $reklamKodu = (string)$row['deger'];
    }
} catch (Throwable) {
    $hata = 'ayarlar tablosu okunamadı.';
}
?>
  <div class="card">
    <h2 style="margin-top:0;color:var(--navy)">📣 Reklam Yönetimi</h2>
    <p style="color:var(--muted)">
      Buraya eklediğiniz kod (Google AdSense, özel banner vb.) <strong>Video Dersler</strong> sayfasında
      başlığın hemen altında gösterilir.
    </p>

    <?php if ($mesaj !== ''): ?>
      <div style="background:#e6f7e9;border:1px solid #9adfa8;color:#1c7a2e;padding:10px 14px;border-radius:8px;margin:12px 0">
        ✔ <?= e($mesaj) ?>
      </div>
    <?php endif; ?>
    <?php if ($hata !== ''): ?>
      <div style="background:#fdeaea;border:1px solid #f0a6a6;color:#a11;padding:10px 14px;border-radius:8px;margin:12px 0">
        ✖ <?= e($hata) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="">
      <label for="reklam_kodu" style="font-weight:600;display:block;margin:14px 0 6px">Reklam Kodu (HTML / AdSense)</label>
      <textarea
        id="reklam_kodu"
        name="reklam_kodu"
        rows="14"
        style="width:100%;box-sizing:border-box;font-family:Consolas,monospace;font-size:13px;padding:10px;border:1px solid var(--border,#ccc);border-radius:8px"
        placeholder="Örnek: <script async src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXX'></script> ..."><?= e($reklamKodu) ?></textarea>
      <div style="margin-top:14px">
        <button type="submit" class="btn btn-primary">💾 Kaydet</button>
      </div>
    </form>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
