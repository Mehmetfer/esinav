<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Kullanıcılar';
$activeMenu = (($_GET['group'] ?? 'ehliyet') === 'src') ? 'src-users' : 'ehliyet-users';
$activeGroup = $_GET['group'] ?? 'ehliyet';
$search = trim((string)($_GET['q'] ?? ''));

$pdo = db();

// Kullanıcıları listele (grup bazlı)
$users = [];
try {
    if ($search !== '') {
        $st = $pdo->prepare(
            "SELECT DISTINCT k.* FROM kursiyerler k
             JOIN user_groups ug ON k.id = ug.user_id
             JOIN education_groups eg ON ug.group_id = eg.id
             WHERE eg.slug = ? AND (k.ad LIKE ? OR k.soyad LIKE ? OR k.telefon LIKE ? OR k.gsm LIKE ?)
             ORDER BY k.ad, k.soyad LIMIT 200"
        );
        $like = '%' . $search . '%';
        $st->execute([$activeGroup, $like, $like, $like, $like]);
    } else {
        $st = $pdo->prepare(
            "SELECT DISTINCT k.* FROM kursiyerler k
             JOIN user_groups ug ON k.id = ug.user_id
             JOIN education_groups eg ON ug.group_id = eg.id
             WHERE eg.slug = ?
             ORDER BY k.ad, k.soyad LIMIT 200"
        );
        $st->execute([$activeGroup]);
    }
    $users = $st->fetchAll();
} catch (Throwable) {
}

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">👥 Kullanıcılar — <?= e(strtoupper($activeGroup)) ?></h1>
    <a href="/admin/kursiyer-ekle.php?group=<?= e($activeGroup) ?>" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni Kullanıcı</a>
  </div>

  <div class="card" style="margin-bottom:14px">
    <form method="get" style="display:flex;gap:10px">
      <input type="hidden" name="group" value="<?= e($activeGroup) ?>">
      <input name="q" value="<?= e($search) ?>" placeholder="Ad, soyad, telefon ara…" style="flex:1;padding:9px 12px;border:1px solid #d1d5db;border-radius:6px">
      <button type="submit" style="padding:10px 16px;border:0;border-radius:6px;background:#1e3a8a;color:#fff;font-weight:700;cursor:pointer">Ara</button>
    </form>
  </div>

  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
      <thead>
        <tr style="background:#f8fafc;text-align:left">
          <th style="padding:10px 12px">Ad Soyad</th>
          <th style="padding:10px 12px">Ehliyet</th>
          <th style="padding:10px 12px">Telefon</th>
          <th style="padding:10px 12px;text-align:center">Sınav</th>
          <th style="padding:10px 12px;text-align:center">Başarılı</th>
          <th style="padding:10px 12px">Son Sınav</th>
          <th style="padding:10px 12px"></th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($users)): ?>
        <tr><td colspan="7" style="padding:20px;color:var(--muted)">Kullanıcı yok.</td></tr>
      <?php else: foreach ($users as $u): ?>
        <?php
        // İstatistikler
        $kid = (int)$u['id'];
        $srcToplam = $srcBasarili = 0;
        try {
            $st2 = $pdo->prepare("SELECT COUNT(*) as t, SUM(basarili) as b FROM exam_attempts WHERE user_id = ? AND group_id = (SELECT id FROM education_groups WHERE slug = ?) AND status = 'bitti'");
            $st2->execute([$kid, $activeGroup]);
            $r = $st2->fetch();
            $srcToplam = (int)($r['t'] ?? 0);
            $srcBasarili = (int)($r['b'] ?? 0);
        } catch (Throwable) {}
        
        $sonSinav = '';
        try {
            $st3 = $pdo->prepare("SELECT finished_at FROM exam_attempts WHERE user_id = ? AND status = 'bitti' ORDER BY id DESC LIMIT 1");
            $st3->execute([$kid]);
            $son = $st3->fetchColumn();
            $sonSinav = $son ? date('d.m.Y', strtotime($son)) : '—';
        } catch (Throwable) {}
        ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px;font-weight:600"><?= e(trim((string)$u['ad'] . ' ' . (string)$u['soyad'])) ?></td>
          <td style="padding:10px 12px"><span style="padding:2px 8px;background:#dbeafe;color:#1e40af;border-radius:4px;font-weight:700;font-size:.8rem"><?= e((string)($u['ehliyet_sinifi'] ?: 'B')) ?></span></td>
          <td style="padding:10px 12px;font-size:.82rem"><?= e((string)($u['telefon'] ?: $u['gsm'] ?: '—')) ?></td>
          <td style="padding:10px 12px;text-align:center"><?= $srcToplam ?></td>
          <td style="padding:10px 12px;text-align:center;color:<?= $srcBasarili > 0 ? '#166534' : '#991b1b' ?>;font-weight:700"><?= $srcBasarili ?></td>
          <td style="padding:10px 12px;font-size:.82rem"><?= e($sonSinav) ?></td>
          <td style="padding:10px 12px">
            <a href="/admin/kursiyer-detay.php?id=<?= (int)$u['id'] ?>" style="padding:4px 10px;background:#0f766e;color:#fff;border-radius:4px;text-decoration:none;font-size:.8rem">Detay</a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
