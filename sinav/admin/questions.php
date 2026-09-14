<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$pageTitle = 'Soru Havuzu';
$activeMenu = 'questions';

$bank = [];
try {
    $bank = (array)(require dirname(__DIR__) . '/data/soru-havuzu.php');
} catch (Throwable $e) {
    $bank = [];
}

$search = trim((string)($_GET['q'] ?? ''));
$dersF  = trim((string)($_GET['ders'] ?? ''));
$kaynakF = trim((string)($_GET['kaynak'] ?? ''));
$aktifF = trim((string)($_GET['aktif'] ?? ''));
$page   = max(1, (int)($_GET['p'] ?? 1));
$perPage = 20;

$filtered = array_values(array_filter($bank, static function (array $q) use ($search, $dersF, $kaynakF, $aktifF): bool {
    if ($dersF !== '' && (string)($q['ders'] ?? '') !== $dersF) { return false; }
    if ($kaynakF !== '') {
        $src = (string)($q['kaynak'] ?? 'manuel');
        if ($src !== $kaynakF) { return false; }
    }
    if ($aktifF !== '') {
        $aktif = (int)($q['aktif'] ?? 1);
        if ($aktif !== (int)$aktifF) { return false; }
    }
    if ($search !== '' && mb_stripos((string)($q['soru'] ?? ''), $search) === false) { return false; }
    return true;
}));

$total      = count($filtered);
$totalPages = max(1, (int)ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;
$items      = array_slice($filtered, $offset, $perPage);

$dersLabel = [
    'trafik'    => 'Trafik ve Çevre Bilgisi',
    'ilkyardim' => 'İlk Yardım Bilgisi',
    'arac'      => 'Motor ve Araç Tekniği',
    'adab'      => 'Trafik Adabı'
];

require __DIR__ . '/_layout_top.php';
?>

  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">Soru Havuzu <span style="color:var(--muted);font-weight:600">(<?= count($bank) ?>)</span></h1>
    <a href="/admin/soru-duzenle.php" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni Soru</a>
  </div>

  <div class="card" style="margin-bottom:14px">
    <form method="get" style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr auto;gap:10px;align-items:end">
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Ara</label>
        <input name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Soru metni…" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Ders</label>
        <select name="ders" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Tümü</option>
          <?php foreach ($dersLabel as $k => $v): ?>
            <option value="<?= $k ?>" <?= $dersF === $k ? 'selected' : '' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Kaynak</label>
        <select name="kaynak" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Tümü</option>
          <option value="doc" <?= $kaynakF === 'doc' ? 'selected' : '' ?>>DÖÇ</option>
          <option value="meb" <?= $kaynakF === 'meb' ? 'selected' : '' ?>>MEB</option>
          <option value="faruk" <?= $kaynakF === 'faruk' ? 'selected' : '' ?>>FARUK</option>
          <option value="manuel" <?= $kaynakF === 'manuel' ? 'selected' : '' ?>>Manuel</option>
        </select>
      </div>
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Durum</label>
        <select name="aktif" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Tümü</option>
          <option value="1" <?= $aktifF === '1' ? 'selected' : '' ?>>Aktif</option>
          <option value="0" <?= $aktifF === '0' ? 'selected' : '' ?>>Pasif</option>
        </select>
      </div>
      <button type="submit" style="padding:10px 16px;border:0;border-radius:6px;background:#1e3a8a;color:#fff;font-weight:700;cursor:pointer">Filtrele</button>
    </form>
  </div>

  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
      <thead>
        <tr style="background:#f8fafc;text-align:left">
          <th style="padding:10px 12px">ID</th>
          <th style="padding:10px 12px">Ders</th>
          <th style="padding:10px 12px">Soru</th>
          <th style="padding:10px 12px">Doğru</th>
          <th style="padding:10px 12px">Kaynak</th>
          <th style="padding:10px 12px">Görsel</th>
          <th style="padding:10px 12px">Durum</th>
          <th style="padding:10px 12px">İşlem</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($items)): ?>
        <tr><td colspan="8" style="padding:20px;color:var(--muted);text-align:center">Soru bulunamadı.</td></tr>
      <?php else: foreach ($items as $idx => $q): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px"><?= $idx ?></td>
          <td style="padding:10px 12px"><?= htmlspecialchars($dersLabel[$q['ders'] ?? ''] ?? ($q['ders'] ?? '')) ?></td>
          <td style="padding:10px 12px;max-width:420px"><?= htmlspecialchars(mb_substr((string)($q['soru'] ?? ''), 0, 90)) ?>...</td>
          <td style="padding:10px 12px;font-weight:800"><?= htmlspecialchars((string)($q['dogru'] ?? '')) ?></td>
          <td style="padding:10px 12px"><?= htmlspecialchars((string)($q['kaynak'] ?? 'manuel')) ?></td>
          <td style="padding:10px 12px"><?= !empty($q['gorsel']) ? 'Var' : '—' ?></td>
          <td style="padding:10px 12px"><?= ((int)($q['aktif'] ?? 1) === 1) ? 'Aktif' : 'Pasif' ?></td>
          <td style="padding:10px 12px;white-space:nowrap">
            <a href="/admin/soru-duzenle.php?id=<?= $idx ?>" style="color:#1d4ed8;font-weight:700;text-decoration:none;margin-right:8px">Düzenle</a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
  <div style="margin-top:12px;display:flex;gap:6px;flex-wrap:wrap;justify-content:center">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?p=<?= $i ?>" style="padding:6px 10px;border-radius:6px;text-decoration:none;font-weight:700;<?= $i === $page ? 'background:#1e3a8a;color:#fff' : 'background:#e2e8f0;color:#334155' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
