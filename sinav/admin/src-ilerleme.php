<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC & E-Sinav Ilerleme';
$activeMenu = 'src-ilerleme';

$pdo = db();
src_ensure_tables($pdo);

$ehliyetFiltre = (string)($_GET['ehliyet'] ?? '');
$arama = trim((string)($_GET['q'] ?? ''));

$where = ['1=1'];
$params = [];

if ($arama !== '') {
    $where[] = '(ad LIKE ? OR soyad LIKE ? OR telefon LIKE ?)';
    $like = '%' . $arama . '%';
    array_push($params, $like, $like, $like);
}

if ($ehliyetFiltre !== '') {
    $where[] = 'ehliyet_sinifi = ?';
    $params[] = $ehliyetFiltre;
}

$sqlWhere = implode(' AND ', $where);

$kursiyerler = [];
try {
    $st = $pdo->prepare("SELECT id, ad, soyad, ehliyet_sinifi, telefon FROM kursiyerler WHERE {$sqlWhere} ORDER BY ad, soyad");
    $st->execute($params);
    $kursiyerler = $st->fetchAll();
} catch (Throwable) {
}

$ilerleme = [];
foreach ($kursiyerler as $k) {
    $kid = (int)$k['id'];
    
    $srcToplam = $srcBasarili = $srcSonPuan = 0;
    try {
        $st = $pdo->prepare("SELECT COUNT(*) as toplam, SUM(basarili) as basarili, MAX(puan) as son_puan FROM src_oturum WHERE kursiyer_id = ? AND durum = 'bitti'");
        $st->execute([$kid]);
        $srcRow = $st->fetch();
        $srcToplam = (int)($srcRow['toplam'] ?? 0);
        $srcBasarili = (int)($srcRow['basarili'] ?? 0);
        $srcSonPuan = (int)($srcRow['son_puan'] ?? 0);
    } catch (Throwable) {}
    
    $esinavToplam = $esinavBasarili = $esinavSonPuan = 0;
    try {
        $st = $pdo->prepare("SELECT COUNT(*) as toplam, SUM(basarili) as basarili, MAX(puan) as son_puan FROM esinav_oturum WHERE kursiyer_id = ? AND durum = 'bitti'");
        $st->execute([$kid]);
        $esRow = $st->fetch();
        $esinavToplam = (int)($esRow['toplam'] ?? 0);
        $esinavBasarili = (int)($esRow['basarili'] ?? 0);
        $esinavSonPuan = (int)($esRow['son_puan'] ?? 0);
    } catch (Throwable) {}
    
    $ilerleme[] = [
        'kursiyer' => $k,
        'src_toplam' => $srcToplam,
        'src_basarili' => $srcBasarili,
        'src_son_puan' => $srcSonPuan,
        'esinav_toplam' => $esinavToplam,
        'esinav_basarili' => $esinavBasarili,
        'esinav_son_puan' => $esinavSonPuan,
    ];
}

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">SRC & E-Sinav Ilerleme Durumu</h1>
    <span style="font-size:.85rem;color:var(--muted)"><?= count($ilerleme) ?> kursiyer</span>
  </div>

  <div class="card" style="margin-bottom:14px">
    <form method="get" style="display:grid;grid-template-columns:2fr 1fr auto;gap:10px;align-items:end">
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Ara</label>
        <input name="q" value="<?= e($arama) ?>" placeholder="Ad, soyad, telefon…"
               style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Ehliyet Sinifi</label>
        <select name="ehliyet" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Tumu</option>
          <option value="B" <?= $ehliyetFiltre === 'B' ? 'selected' : '' ?>>B - Otomobil</option>
          <option value="A" <?= $ehliyetFiltre === 'A' ? 'selected' : '' ?>>A - Motosiklet</option>
          <option value="A1" <?= $ehliyetFiltre === 'A1' ? 'selected' : '' ?>>A1 - Motosiklet (125cc)</option>
          <option value="A2" <?= $ehliyetFiltre === 'A2' ? 'selected' : '' ?>>A2 - Motosiklet (35kW)</option>
          <option value="C" <?= $ehliyetFiltre === 'C' ? 'selected' : '' ?>>C - Kamyon</option>
          <option value="D" <?= $ehliyetFiltre === 'D' ? 'selected' : '' ?>>D - Otobus</option>
          <option value="F" <?= $ehliyetFiltre === 'F' ? 'selected' : '' ?>>F - Traktor</option>
        </select>
      </div>
      <button type="submit" style="padding:10px 16px;border:0;border-radius:6px;background:#1e3a8a;color:#fff;font-weight:700;cursor:pointer">Filtrele</button>
    </form>
  </div>

  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
      <thead>
        <tr style="background:#f8fafc;text-align:left">
          <th style="padding:10px 12px">Ad Soyad</th>
          <th style="padding:10px 12px">Ehliyet</th>
          <th style="padding:10px 12px">Telefon</th>
          <th style="padding:10px 12px;text-align:center">SRC Deneme</th>
          <th style="padding:10px 12px;text-align:center">SRC Basarili</th>
          <th style="padding:10px 12px;text-align:center">SRC Son Puan</th>
          <th style="padding:10px 12px;text-align:center">E-Sinav Deneme</th>
          <th style="padding:10px 12px;text-align:center">E-Sinav Basarili</th>
          <th style="padding:10px 12px;text-align:center">E-Sinav Son Puan</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($ilerleme)): ?>
        <tr><td colspan="9" style="padding:20px;color:var(--muted)">Kayit yok.</td></tr>
      <?php else: foreach ($ilerleme as $i): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px;font-weight:600"><?= e(trim((string)$i['kursiyer']['ad'] . ' ' . (string)$i['kursiyer']['soyad'])) ?></td>
          <td style="padding:10px 12px"><span style="padding:2px 8px;background:#dbeafe;color:#1e40af;border-radius:4px;font-weight:700;font-size:.8rem"><?= e((string)($i['kursiyer']['ehliyet_sinifi'] ?: 'B')) ?></span></td>
          <td style="padding:10px 12px;font-size:.82rem"><?= e((string)($i['kursiyer']['telefon'] ?: '—')) ?></td>
          <td style="padding:10px 12px;text-align:center"><?= (int)$i['src_toplam'] ?></td>
          <td style="padding:10px 12px;text-align:center;color:<?= $i['src_basarili'] > 0 ? '#166534' : '#991b1b' ?>;font-weight:700"><?= (int)$i['src_basarili'] ?></td>
          <td style="padding:10px 12px;text-align:center;font-weight:700;color:<?= $i['src_son_puan'] >= 70 ? '#166534' : '#b91c1c' ?>"><?= $i['src_son_puan'] > 0 ? (int)$i['src_son_puan'] : '—' ?></td>
          <td style="padding:10px 12px;text-align:center"><?= (int)$i['esinav_toplam'] ?></td>
          <td style="padding:10px 12px;text-align:center;color:<?= $i['esinav_basarili'] > 0 ? '#166534' : '#991b1b' ?>;font-weight:700"><?= (int)$i['esinav_basarili'] ?></td>
          <td style="padding:10px 12px;text-align:center;font-weight:700;color:<?= $i['esinav_son_puan'] >= 70 ? '#166534' : '#b91c1c' ?>"><?= $i['esinav_son_puan'] > 0 ? (int)$i['esinav_son_puan'] : '—' ?></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
