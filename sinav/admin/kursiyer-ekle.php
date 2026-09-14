<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$pageTitle = 'Bireysel Kayıt';
$activeMenu = 'kursiyer-ekle';
$message = '';
$error = '';
$form = [
    'telefon' => '',
    'ad' => '',
    'soyad' => '',
    'sifre' => '',
    'email' => '',
    'ehliyet_sinifi' => 'B',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['telefon'] = normalize_gsm((string)($_POST['telefon'] ?? ''));
    $form['ad'] = trim((string)($_POST['ad'] ?? ''));
    $form['soyad'] = trim((string)($_POST['soyad'] ?? ''));
    $form['sifre'] = (string)($_POST['sifre'] ?? '');
    $form['email'] = trim((string)($_POST['email'] ?? ''));
    $form['ehliyet_sinifi'] = trim((string)($_POST['ehliyet_sinifi'] ?? 'B'));

    if (!is_valid_gsm($form['telefon'])) {
        $error = 'Geçerli bir telefon numarası giriniz.';
    } elseif ($form['ad'] === '' || $form['soyad'] === '') {
        $error = 'Ad ve soyad zorunludur.';
    } elseif (strlen($form['sifre']) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır.';
    } else {
        try {
            $hash = password_hash($form['sifre'], PASSWORD_DEFAULT);
            $stmt = db()->prepare(
                'INSERT INTO kursiyerler (telefon, sifre_hash, ad, soyad, email, ehliyet_sinifi, aktif)
                 VALUES (?, ?, ?, ?, ?, ?, 1)'
            );
            $stmt->execute([
                $form['telefon'],
                $hash,
                $form['ad'],
                $form['soyad'],
                $form['email'] !== '' ? $form['email'] : null,
                $form['ehliyet_sinifi'] !== '' ? $form['ehliyet_sinifi'] : null,
            ]);
            $message = 'Kursiyer kaydedildi. Giriş: telefon numarası + şifre.';
            $form = ['telefon' => '', 'ad' => '', 'soyad' => '', 'sifre' => '', 'email' => '', 'ehliyet_sinifi' => 'B'];
        } catch (PDOException $e) {
            if ((int)$e->getCode() === 23000) {
                $error = 'Bu telefon numarası zaten kayıtlı.';
            } else {
                $error = 'Kayıt hatası: ' . $e->getMessage();
            }
        }
    }
}

require __DIR__ . '/_layout_top.php';
?>
  <div class="card" style="max-width:560px">
    <h2 style="margin-top:0;color:var(--navy)">Bireysel Kursiyer Kaydı</h2>

    <?php if ($message !== ''): ?><div class="alert alert-ok"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form method="post" style="display:grid;gap:12px">
      <div>
        <label style="font-size:0.8rem;color:var(--muted)">Telefon (GSM)</label>
        <input name="telefon" inputmode="numeric" maxlength="15" required value="<?= e($form['telefon']) ?>"
               placeholder="5xx xxx xx xx"
               style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div>
          <label style="font-size:0.8rem;color:var(--muted)">Ad</label>
          <input name="ad" required value="<?= e($form['ad']) ?>"
                 style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
        </div>
        <div>
          <label style="font-size:0.8rem;color:var(--muted)">Soyad</label>
          <input name="soyad" required value="<?= e($form['soyad']) ?>"
                 style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
        </div>
      </div>
      <div>
        <label style="font-size:0.8rem;color:var(--muted)">Giriş Şifresi</label>
        <input name="sifre" required minlength="6" value="<?= e($form['sifre']) ?>"
               placeholder="En az 6 karakter"
               style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div>
        <label style="font-size:0.8rem;color:var(--muted)">E-posta</label>
        <input type="email" name="email" value="<?= e($form['email']) ?>"
               style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div>
        <label style="font-size:0.8rem;color:var(--muted)">Ehliyet Sınıfı</label>
        <select name="ehliyet_sinifi" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px">
          <?php foreach (['A','A1','A2','B','BE','C','D','F'] as $sinif): ?>
            <option value="<?= $sinif ?>" <?= $form['ehliyet_sinifi'] === $sinif ? 'selected' : '' ?>><?= $sinif ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <p style="margin:0;font-size:0.82rem;color:var(--muted)">
        Kursiyer, girişte <strong>telefon numarası + belirlediğiniz şifre</strong> ile giriş yapar.
      </p>
      <button type="submit" style="padding:11px;border:0;border-radius:6px;background:var(--meb-red);color:#fff;font-weight:700;cursor:pointer">Kaydet</button>
    </form>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
