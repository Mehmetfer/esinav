<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pdo = db();
src_ensure_tables($pdo);
src_seed_if_needed($pdo);

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$pageTitle = $isEdit ? 'SRC Soru Düzenle' : 'SRC Yeni Soru';
$activeMenu = 'src-sorular';

$form = ['ders' => 'is_sagligi', 'soru' => '', 'secenek_a' => '', 'secenek_b' => '', 'secenek_c' => '', 'secenek_d' => '', 'dogru' => 'A', 'aktif' => 1, 'kaynak' => 'manuel', 'gorsel' => '', 'aciklama' => ''];
$error = '';
$message = '';

if ($isEdit) {
    $st = $pdo->prepare('SELECT * FROM src_sorular WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { redirect('/admin/src-sorular.php'); }
    $form = ['ders' => (string)$row['ders'], 'soru' => (string)$row['soru'], 'secenek_a' => (string)$row['secenek_a'], 'secenek_b' => (string)$row['secenek_b'], 'secenek_c' => (string)$row['secenek_c'], 'secenek_d' => (string)$row['secenek_d'], 'dogru' => strtoupper((string)$row['dogru']), 'aktif' => (int)$row['aktif'], 'kaynak' => (string)($row['kaynak'] ?? 'manuel'), 'gorsel' => (string)($row['gorsel'] ?? ''), 'aciklama' => (string)($row['aciklama'] ?? '')];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ders = (string)($_POST['ders'] ?? 'is_sagligi');
    if (!isset(src_ders_adlari()[$ders])) $ders = 'is_sagligi';
    $dogru = strtoupper(substr((string)($_POST['dogru'] ?? 'A'), 0, 1));
    if (!in_array($dogru, ['A', 'B', 'C', 'D'], true)) $dogru = 'A';
    $kaynak = trim((string)($_POST['kaynak'] ?? 'manuel'));
    if (!in_array($kaynak, ['doc', 'meb', 'manuel'], true)) $kaynak = 'manuel';
    $data = ['ders' => $ders, 'soru' => trim((string)($_POST['soru'] ?? '')), 'secenek_a' => trim((string)($_POST['secenek_a'] ?? '')), 'secenek_b' => trim((string)($_POST['secenek_b'] ?? '')), 'secenek_c' => trim((string)($_POST['secenek_c'] ?? '')), 'secenek_d' => trim((string)($_POST['secenek_d'] ?? '')), 'dogru' => $dogru, 'aktif' => isset($_POST['aktif']) ? 1 : 0, 'kaynak' => $kaynak, 'aciklama' => trim((string)($_POST['aciklama'] ?? ''))];
    
    if ($data['soru'] === '') { $error = 'Soru metni zorunludur.'; }
    elseif ($data['secenek_a'] === '' || $data['secenek_b'] === '' || $data['secenek_c'] === '' || $data['secenek_d'] === '') { $error = 'Tüm şıklar (A–D) doldurulmalıdır.'; }
    else {
        try {
            $gorsel = $form['gorsel'];
            if (!empty($_POST['gorsel_kaldir']) && $gorsel !== '' && str_starts_with($gorsel, 'upload/')) {
                $fs = dirname(__DIR__) . '/assets/img/sorular/' . ltrim(str_replace('\\', '/', $gorsel), '/');
                if (is_file($fs)) @unlink($fs);
                $gorsel = '';
            }
            if (isset($_FILES['gorsel']) && $_FILES['gorsel']['error'] === UPLOAD_ERR_OK) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = (string)$finfo->file($_FILES['gorsel']['tmp_name']);
                $map = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
                if (isset($map[$mime])) {
                    $dir = dirname(__DIR__) . '/assets/img/sorular/upload';
                    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) throw new RuntimeException('Upload klasörü oluşturulamadı.');
                    $name = 'src_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $map[$mime];
                    $dest = $dir . '/' . $name;
                    if (move_uploaded_file($_FILES['gorsel']['tmp_name'], $dest)) {
                        if ($gorsel !== '' && str_starts_with($gorsel, 'upload/')) {
                            $fs = dirname(__DIR__) . '/assets/img/sorular/' . ltrim(str_replace('\\', '/', $gorsel), '/');
                            if (is_file($fs)) @unlink($fs);
                        }
                        $gorsel = 'upload/' . $name;
                    }
                }
            }
            if ($isEdit) {
                $upd = $pdo->prepare('UPDATE src_sorular SET ders=?, soru=?, secenek_a=?, secenek_b=?, secenek_c=?, secenek_d=?, dogru=?, aktif=?, gorsel=?, kaynak=?, aciklama=? WHERE id=?');
                $upd->execute([$data['ders'], $data['soru'], $data['secenek_a'], $data['secenek_b'], $data['secenek_c'], $data['secenek_d'], $data['dogru'], $data['aktif'], $gorsel !== '' ? $gorsel : null, $data['kaynak'], $data['aciklama'], $id]);
                $message = 'Soru güncellendi.';
            } else {
                $ins = $pdo->prepare('INSERT INTO src_sorular (ders, soru, secenek_a, secenek_b, secenek_c, secenek_d, dogru, aktif, gorsel, kaynak, aciklama) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
                $ins->execute([$data['ders'], $data['soru'], $data['secenek_a'], $data['secenek_b'], $data['secenek_c'], $data['secenek_d'], $data['dogru'], $data['aktif'], $gorsel !== '' ? $gorsel : null, $data['kaynak'], $data['aciklama']]);
                $message = 'Soru oluşturuldu.';
                $id = (int)$pdo->lastInsertId();
                $isEdit = true;
            }
            $form = $data + ['gorsel' => $gorsel];
        } catch (Throwable $e) { $error = $e->getMessage(); }
    }
}

$gorselUrl = null;
if ($form['gorsel'] !== '') { $gorselUrl = '/assets/img/sorular/' . ltrim(str_replace('\\', '/', (string)$form['gorsel']), '/'); }
$lab = 'display:block;font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:4px';
$inp = 'width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:.95rem';
require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem"><?= $isEdit ? 'SRC Soru Düzenle (#' . $id . ')' : 'SRC Yeni Soru' ?></h1>
    <a href="/admin/src-sorular.php" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#e2e8f0;color:#334155;border-radius:8px;font-weight:700">← Listeye Dön</a>
  </div>

  <?php if ($message !== ''): ?><div class="alert alert-ok"><?= e($message) ?></div><?php endif; ?>
  <?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data" style="display:grid;gap:16px;max-width:900px">
    <fieldset style="border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin:0">
      <legend style="font-weight:800;color:var(--navy);padding:0 6px">Kopyala-Yapıştır ile Hızlı Ekleme</legend>
      <p style="font-size:.82rem;color:var(--muted);margin:0 0 10px">Aşağıdaki alana soru metnini yapıştırın. Satır formatı: <code>Soru metni | A şıkkı | B şıkkı | C şıkkı | D şıkkı | Doğru şık (A/B/C/D)</code></p>
      <textarea id="hizliYapistir" rows="6" placeholder="Trafik lambası ne anlama gelir? | Durmak | Geçmek | Yavaşlamak | Hızlanmak | A" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;font-family:monospace;font-size:.88rem"></textarea>
      <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap">
        <button type="button" id="ayristirBtn" style="padding:8px 16px;border:0;border-radius:6px;background:#0f766e;color:#fff;font-weight:700;cursor:pointer;font-size:.85rem">Ayır ve Yerleştir</button>
        <span id="sonuc" style="font-size:.85rem;color:var(--muted);align-self:center"></span>
      </div>
    </fieldset>

    <fieldset style="border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin:0">
      <legend style="font-weight:800;color:var(--navy);padding:0 6px">Soru Bilgileri</legend>
      <div style="display:grid;gap:12px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label style="<?= $lab ?>">Ders</label>
            <select name="ders" style="<?= $inp ?>">
              <?php foreach (src_ders_adlari() as $k => $v): ?>
                <option value="<?= e($k) ?>" <?= $form['ders'] === $k ? 'selected' : '' ?>><?= e($v) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label style="<?= $lab ?>">Kaynak</label>
            <select name="kaynak" style="<?= $inp ?>">
              <option value="manuel" <?= $form['kaynak'] === 'manuel' ? 'selected' : '' ?>>Manuel</option>
              <option value="doc" <?= $form['kaynak'] === 'doc' ? 'selected' : '' ?>>DÖÇ (çıkmış)</option>
              <option value="meb" <?= $form['kaynak'] === 'meb' ? 'selected' : '' ?>>MEB / havuz</option>
            </select>
          </div>
        </div>
        <div>
          <label style="<?= $lab ?>">Soru metni</label>
          <textarea name="soru" rows="4" required style="<?= $inp ?>"><?= e((string)$form['soru']) ?></textarea>
        </div>
        <div>
          <label style="<?= $lab ?>">Açıklama (çözüm)</label>
          <textarea name="aciklama" rows="4" style="<?= $inp ?>"><?= e((string)$form['aciklama']) ?></textarea>
        </div>
        <?php foreach (['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $k => $h): ?>
          <div>
            <label style="<?= $lab ?>">Şık <?= $h ?></label>
            <input name="secenek_<?= $k ?>" required value="<?= e((string)$form['secenek_' . $k]) ?>" style="<?= $inp ?>">
          </div>
        <?php endforeach; ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label style="<?= $lab ?>">Doğru şık</label>
            <select name="dogru" style="<?= $inp ?>">
              <?php foreach (['A', 'B', 'C', 'D'] as $h): ?>
                <option value="<?= $h ?>" <?= $form['dogru'] === $h ? 'selected' : '' ?>><?= $h ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label style="<?= $lab ?>">Durum</label>
            <label style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px">
              <input type="checkbox" name="aktif" value="1" <?= $form['aktif'] ? 'checked' : '' ?>> Aktif
            </label>
          </div>
        </div>
      </div>
    </fieldset>

    <fieldset style="border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin:0">
      <legend style="font-weight:800;color:var(--navy);padding:0 6px">Soru görseli</legend>
      <?php if ($gorselUrl): ?>
        <div style="margin-bottom:12px">
          <img src="<?= e($gorselUrl) ?>" alt="Soru görseli" style="max-width:100%;max-height:280px;border:1px solid #e2e8f0;border-radius:8px;background:#fff">
          <div style="margin-top:8px">
            <label style="display:inline-flex;align-items:center;gap:6px;font-size:.9rem">
              <input type="checkbox" name="gorsel_kaldir" value="1"> Mevcut görseli kaldır
            </label>
          </div>
        </div>
      <?php endif; ?>
      <div>
        <label style="<?= $lab ?>">Yeni görsel yükle (JPG/PNG/WEBP, max 5MB)</label>
        <input type="file" name="gorsel" accept="image/jpeg,image/png,image/webp,image/gif"
               style="width:100%;padding:8px;border:1px dashed #cbd5e1;border-radius:6px;background:#f8fafc">
      </div>
    </fieldset>

    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <button type="submit" style="padding:12px 22px;border:0;border-radius:8px;background:#1e3a8a;color:#fff;font-weight:800;cursor:pointer">
        <?= $isEdit ? 'Kaydet' : 'Oluştur' ?>
      </button>
      <a href="/admin/src-sorular.php" style="padding:12px 18px;border-radius:8px;background:#e2e8f0;color:#334155;font-weight:700;text-decoration:none">İptal</a>
    </div>
  </form>

  <script>
  document.getElementById('ayristirBtn').addEventListener('click', function() {
    const raw = document.getElementById('hizliYapistir').value.trim();
    if (!raw) { document.getElementById('sonuc').textContent = '⚠ Metin boş.'; return; }
    const lines = raw.split('\\n').filter(l => l.trim() !== '');
    const parsed = [];
    for (const line of lines) {
      const parts = line.split('|').map(p => p.trim());
      if (parts.length >= 5) {
        parsed.push({soru: parts[0], A: parts[1], B: parts[2], C: parts[3], D: parts[4], dogru: parts[5] || 'A'});
      }
    }
    if (parsed.length === 0) { document.getElementById('sonuc').textContent = '⚠ Geçerli soru bulunamadı.'; return; }
    const first = parsed[0];
    document.querySelector('[name="soru"]').value = first.soru;
    document.querySelector('[name="secenek_a"]').value = first.A;
    document.querySelector('[name="secenek_b"]').value = first.B;
    document.querySelector('[name="secenek_c"]').value = first.C;
    document.querySelector('[name="secenek_d"]').value = first.D;
    document.querySelector('[name="dogru"]').value = first.dogru;
    document.getElementById('sonuc').textContent = '✔ ' + parsed.length + ' soru ayrıştırıldı (ilk soru yerleştirildi).';
  });
  </script>
<?php require __DIR__ . '/_layout_bottom.php'; ?>

