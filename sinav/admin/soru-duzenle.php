<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/admin_soru.php';

$havuzDosyasi = dirname(__DIR__) . '/data/soru-havuzu.php';
$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$pageTitle = $isEdit ? 'Soru Düzenle' : 'Yeni Soru';
$activeMenu = 'questions';

$bank = [];
if (file_exists($havuzDosyasi)) { $bank = (array)(require $havuzDosyasi); }

$form = ['ders'=>'trafik','soru'=>'','secenek_a'=>'','secenek_b'=>'','secenek_c'=>'','secenek_d'=>'','dogru'=>'A','aktif'=>1,'kaynak'=>'manuel','gorsel'=>''];
$error = ''; $message = '';

if ($isEdit && isset($bank[$id])) {
    $row = $bank[$id];
    $form = [
        'ders' => (string)($row['ders'] ?? 'trafik'),
        'soru' => (string)($row['soru'] ?? ''),
        'secenek_a' => (string)($row['a'] ?? $row['secenek_a'] ?? ''),
        'secenek_b' => (string)($row['b'] ?? $row['secenek_b'] ?? ''),
        'secenek_c' => (string)($row['c'] ?? $row['secenek_c'] ?? ''),
        'secenek_d' => (string)($row['d'] ?? $row['secenek_d'] ?? ''),
        'dogru' => strtoupper((string)($row['dogru'] ?? 'A')),
        'aktif' => (int)($row['aktif'] ?? 1),
        'kaynak' => (string)($row['kaynak'] ?? 'manuel'),
        'gorsel' => (string)($row['gorsel'] ?? ''),
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = admin_soru_normalize_post($_POST);
    $gorsel = $form['gorsel'];
    $yeniSoru = [
        'ders' => $data['ders'], 'soru' => $data['soru'],
        'a' => $data['secenek_a'], 'b' => $data['secenek_b'],
        'c' => $data['secenek_c'], 'd' => $data['secenek_d'],
        'dogru' => strtoupper($data['dogru']), 'aktif' => (int)$data['aktif'], 'kaynak' => $data['kaynak']
    ];
    if ($gorsel !== '') { $yeniSoru['gorsel'] = $gorsel; }
    
    if ($isEdit) { $bank[$id] = $yeniSoru; } else { $bank[] = $yeniSoru; }
    file_put_contents($havuzDosyasi, "<?php\ndeclare(strict_types=1);\n\nreturn " . var_export($bank, true) . ";\n");
    header('Location: /admin/questions.php');
    exit;
}

$inp = 'width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;font:inherit';
$lab = 'font-size:0.8rem;color:var(--muted);display:block;margin-bottom:4px';
require __DIR__ . '/_layout_top.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px">
  <h1 style="margin:0;color:var(--navy);font-size:1.25rem"><?= e($pageTitle) ?></h1>
  <a href="/admin/questions.php" style="color:#1d4ed8;font-weight:700;text-decoration:none">← Soru listesi</a>
</div>

<form method="post" enctype="multipart/form-data" class="card" style="display:grid;gap:16px;max-width:920px">
  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
    <div>
      <label style="<?= $lab ?>">Ders</label>
      <select name="ders" required style="<?= $inp ?>">
        <option value="trafik">Trafik ve Çevre Bilgisi</option>
        <option value="ilkyardim">İlk Yardım Bilgisi</option>
        <option value="arac">Motor ve Araç Tekniği</option>
        <option value="adab">Trafik Adabı</option>
      </select>
    </div>
    <div>
      <label style="<?= $lab ?>">Kaynak</label>
      <select name="kaynak" style="<?= $inp ?>">
        <option value="doc">DÖÇ</option>
        <option value="meb">MEB</option>
        <option value="manuel">Manuel</option>
      </select>
    </div>
    <div>
      <label style="<?= $lab ?>">Doğru şık</label>
      <select name="dogru" required style="<?= $inp ?>">
        <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
      </select>
    </div>
  </div>

  <label style="display:flex;align-items:center;gap:8px;font-weight:700">
    <input type="checkbox" name="aktif" value="1" checked> Aktif (sınavda çıksın)
  </label>

  <fieldset style="border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin:0">
    <legend style="font-weight:800;color:var(--navy);padding:0 6px">Türkçe</legend>
    <div style="display:grid;gap:12px">
      
      <!-- GELİŞMİŞ AKILLI YAPIŞTIRICI KUTUSU -->
      <div style="background:#f0fdf4;padding:12px;border-radius:8px;border:2px dashed #16a34a">
        <label style="font-size:0.85rem;font-weight:800;color:#166534;display:block;margin-bottom:6px">✨ Gelişmiş Akıllı Soru Yapıştırıcı</label>
        <textarea id="smartPasteArea" rows="5" placeholder="Soruyu ve A, B, C, D şıklarını buraya yapıştırıp butona basın..." style="width:100%;padding:10px;border:1px solid #bbf7d0;border-radius:6px;background:#fff;font:inherit"></textarea>
        <button type="button" onclick="parseSmartQuestion()" style="margin-top:8px;padding:10px 18px;background:#16a34a;color:#fff;border:0;border-radius:6px;font-weight:800;cursor:pointer">Şıkları Otomatik Ayır</button>
      </div>

      <div>
        <label style="<?= $lab ?>">Soru metni</label>
        <textarea name="soru" id="fieldSoru" rows="4" required style="<?= $inp ?>"><?= e($form['soru']) ?></textarea>
      </div>
      <div>
        <label style="<?= $lab ?>">Şık A</label>
        <input name="secenek_a" id="fieldSecenekA" required value="<?= e($form['secenek_a']) ?>" style="<?= $inp ?>">
      </div>
      <div>
        <label style="<?= $lab ?>">Şık B</label>
        <input name="secenek_b" id="fieldSecenekB" required value="<?= e($form['secenek_b']) ?>" style="<?= $inp ?>">
      </div>
      <div>
        <label style="<?= $lab ?>">Şık C</label>
        <input name="secenek_c" id="fieldSecenekC" required value="<?= e($form['secenek_c']) ?>" style="<?= $inp ?>">
      </div>
      <div>
        <label style="<?= $lab ?>">Şık D</label>
        <input name="secenek_d" id="fieldSecenekD" required value="<?= e($form['secenek_d']) ?>" style="<?= $inp ?>">
      </div>
    </div>
  </fieldset>

  <script>
  function parseSmartQuestion() {
      let text = document.getElementById('smartPasteArea').value.trim();
      if (!text) return;

      // Satır satır ayıralım
      const rawLines = text.split(/\r?\n/).map(l => l.trim()).filter(l => l !== '');
      let lines = [];

      // Eğer A, B, C, D tek başına satırdaysa bir sonraki satırla birleştir
      for (let i = 0; i < rawLines.length; i++) {
          let curr = rawLines[i];
          let upper = curr.toUpperCase();
          if ((upper === 'A' || upper === 'B' || upper === 'C' || upper === 'D') && i + 1 < rawLines.length) {
              lines.push(curr + ' ' + rawLines[i+1]);
              i++;
          } else {
              lines.push(curr);
          }
      }

      let qLines = [], optA = [], optB = [], optC = [], optD = [];
      let currentOpt = null;

      for (let line of lines) {
          // A, A), A- ile başlayanlar
          if (/^A[\)\.\s\-]+/i.test(line)) {
              currentOpt = 'A';
              optA.push(line.replace(/^A[\)\.\s\-]+\s*/i, ''));
              continue;
          }
          if (/^B[\)\.\s\-]+/i.test(line)) {
              currentOpt = 'B';
              optB.push(line.replace(/^B[\)\.\s\-]+\s*/i, ''));
              continue;
          }
          if (/^C[\)\.\s\-]+/i.test(line)) {
              currentOpt = 'C';
              optC.push(line.replace(/^C[\)\.\s\-]+\s*/i, ''));
              continue;
          }
          if (/^D[\)\.\s\-]+/i.test(line)) {
              currentOpt = 'D';
              optD.push(line.replace(/^D[\)\.\s\-]+\s*/i, ''));
              continue;
          }

          if (currentOpt === 'A') optA.push(line);
          else if (currentOpt === 'B') optB.push(line);
          else if (currentOpt === 'C') optC.push(line);
          else if (currentOpt === 'D') optD.push(line);
          else qLines.push(line);
      }

      document.getElementById('fieldSoru').value = qLines.join(' \n');
      document.getElementById('fieldSecenekA').value = optA.join(' ');
      document.getElementById('fieldSecenekB').value = optB.join(' ');
      document.getElementById('fieldSecenekC').value = optC.join(' ');
      document.getElementById('fieldSecenekD').value = optD.join(' ');
  }
  </script>

  <div style="display:flex;gap:10px">
    <button type="submit" style="padding:12px 22px;border:0;border-radius:8px;background:#1e3a8a;color:#fff;font-weight:800;cursor:pointer">Oluştur</button>
    <a href="/admin/questions.php" style="padding:12px 18px;border-radius:8px;background:#e2e8f0;color:#334155;font-weight:700;text-decoration:none">İptal</a>
  </div>
</form>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
