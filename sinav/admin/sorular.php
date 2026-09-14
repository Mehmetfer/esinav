<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/esinav.php';
require_once dirname(__DIR__) . '/includes/admin_soru.php';

$pageTitle = 'Soru Havuzu';
$activeMenu = 'sorular';

$pdo = db();
esinav_ensure_tables($pdo);
esinav_seed_if_needed($pdo);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
    $id = (int)$_POST['toggle_id'];
    $st = $pdo->prepare('UPDATE sorular SET aktif = IF(aktif = 1, 0, 1) WHERE id = ?');
    $st->execute([$id]);
    $message = 'Soru durumu güncellendi.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $row = $pdo->prepare('SELECT gorsel FROM sorular WHERE id = ? LIMIT 1');
    $row->execute([$id]);
    $old = $row->fetch();
    if ($old) {
        admin_soru_gorsel_sil($old['gorsel'] ?? null);
        $pdo->prepare('DELETE FROM sorular WHERE id = ?')->execute([$id]);
        $message = 'Soru silindi.';
    }
}

$q = trim((string)($_GET['q'] ?? ''));
$ders = (string)($_GET['ders'] ?? '');
$kaynak = (string)($_GET['kaynak'] ?? '');
$aktif = (string)($_GET['aktif'] ?? '');
$page = max(1, (int)($_GET['p'] ?? 1));
$per = 30;
$offset = ($page - 1) * $per;

$where = ['1=1'];
$params = [];
if ($q !== '') {
    $where[] = '(soru LIKE ? OR secenek_a LIKE ? OR secenek_b LIKE ? OR secenek_c LIKE ? OR secenek_d LIKE ? OR soru_ar LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like, $like, $like);
}
if ($ders !== '' && isset(admin_soru_ders_secenekleri()[$ders])) {
    $where[] = 'ders = ?';
    $params[] = $ders;
}
if ($kaynak !== '') {
    if ($kaynak === 'bos') {
        $where[] = '(kaynak IS NULL OR kaynak = \'\')';
    } else {
        $where[] = 'kaynak = ?';
        $params[] = $kaynak;
    }
}
if ($aktif === '1' || $aktif === '0') {
    $where[] = 'aktif = ?';
    $params[] = (int)$aktif;
}

$sqlWhere = implode(' AND ', $where);
$countSt = $pdo->prepare("SELECT COUNT(*) FROM sorular WHERE {$sqlWhere}");
$countSt->execute($params);
$total = (int)$countSt->fetchColumn();
$pages = max(1, (int)ceil($total / $per));

$listSt = $pdo->prepare(
    "SELECT id, ders, soru, dogru, aktif, kaynak, gorsel
     FROM sorular WHERE {$sqlWhere}
     ORDER BY id DESC LIMIT {$per} OFFSET {$offset}"
);
$listSt->execute($params);
$rows = $listSt->fetchAll();

$dersAd = admin_soru_ders_secenekleri();

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">Soru Havuzu <span style="color:var(--muted);font-weight:600">(<?= $total ?>)</span></h1>
    <a class="k-start-btn" href="/admin/soru-duzenle.php" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni Soru</a>
  </div>

  <?php if ($message !== ''): ?><div class="alert alert-ok"><?= e($message) ?></div><?php endif; ?>
  <?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

  <div class="card" style="margin-bottom:14px">
    <form method="get" style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr auto;gap:10px;align-items:end">
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Ara</label>
        <input name="q" value="<?= e($q) ?>" placeholder="Soru / şık metni…"
               style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
      </div>
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Ders</label>
        <select name="ders" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Tümü</option>
          <?php foreach ($dersAd as $kod => $ad): ?>
            <option value="<?= e($kod) ?>" <?= $ders === $kod ? 'selected' : '' ?>><?= e($ad) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Kaynak</label>
        <select name="kaynak" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Tümü</option>
          <option value="doc" <?= $kaynak === 'doc' ? 'selected' : '' ?>>DÖÇ</option>
          <option value="meb" <?= $kaynak === 'meb' ? 'selected' : '' ?>>MEB</option>
          <option value="manuel" <?= $kaynak === 'manuel' ? 'selected' : '' ?>>Manuel</option>
          <option value="bos" <?= $kaynak === 'bos' ? 'selected' : '' ?>>Boş</option>
        </select>
      </div>
      <div>
        <label style="font-size:.75rem;color:var(--muted)">Durum</label>
        <select name="aktif" style="width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:6px">
          <option value="">Tümü</option>
          <option value="1" <?= $aktif === '1' ? 'selected' : '' ?>>Aktif</option>
          <option value="0" <?= $aktif === '0' ? 'selected' : '' ?>>Pasif</option>
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
      <?php if (!$rows): ?>
        <tr><td colspan="8" style="padding:20px;color:var(--muted)">Kayıt yok.</td></tr>
      <?php else: foreach ($rows as $r):
          $snippet = (string)$r['soru'];
          if (function_exists('mb_strlen') && mb_strlen($snippet) > 90) {
              $snippet = mb_substr($snippet, 0, 90) . '…';
          } elseif (strlen($snippet) > 90) {
              $snippet = substr($snippet, 0, 90) . '…';
          }
      ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px"><?= (int)$r['id'] ?></td>
          <td style="padding:10px 12px"><?= e($dersAd[$r['ders']] ?? (string)$r['ders']) ?></td>
          <td style="padding:10px 12px;max-width:420px"><?= e($snippet) ?></td>
          <td style="padding:10px 12px;font-weight:800"><?= e((string)$r['dogru']) ?></td>
          <td style="padding:10px 12px"><?= e((string)($r['kaynak'] ?: '—')) ?></td>
          <td style="padding:10px 12px"><?= trim((string)($r['gorsel'] ?? '')) !== '' ? '✓' : '—' ?></td>
          <td style="padding:10px 12px">
            <form method="post" style="display:inline">
              <input type="hidden" name="toggle_id" value="<?= (int)$r['id'] ?>">
              <button type="submit" style="border:0;background:transparent;cursor:pointer;font-weight:700;color:<?= (int)$r['aktif'] === 1 ? '#166534' : '#991b1b' ?>">
                <?= (int)$r['aktif'] === 1 ? 'Aktif' : 'Pasif' ?>
              </button>
            </form>
          </td>
          <td style="padding:10px 12px;white-space:nowrap">
            <a href="/admin/soru-duzenle.php?id=<?= (int)$r['id'] ?>" style="color:#1d4ed8;font-weight:700;text-decoration:none;margin-right:8px">Düzenle</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Bu soru silinsin mi?');">
              <input type="hidden" name="delete_id" value="<?= (int)$r['id'] ?>">
              <button type="submit" style="border:0;background:transparent;color:#b91c1c;font-weight:700;cursor:pointer">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pages > 1): ?>
    <div style="margin-top:12px;display:flex;gap:6px;flex-wrap:wrap">
      <?php for ($i = 1; $i <= $pages; $i++):
        $qs = http_build_query(array_filter([
            'q' => $q !== '' ? $q : null,
            'ders' => $ders !== '' ? $ders : null,
            'kaynak' => $kaynak !== '' ? $kaynak : null,
            'aktif' => $aktif !== '' ? $aktif : null,
            'p' => $i,
        ], static fn($v) => $v !== null));
      ?>
        <a href="?<?= e($qs) ?>" style="padding:6px 10px;border-radius:6px;text-decoration:none;font-weight:700;<?= $i === $page ? 'background:#1e3a8a;color:#fff' : 'background:#e2e8f0;color:#334155' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>

