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
          <th style="padding:10px 12px">Grup</th>
          <th style="padding:10px 12px">Telefon</th>
          <th style="padding:10px 12px;text-align:center">📚 Ders</th>
          <th style="padding:10px 12px;text-align:center">🎬 Video</th>
          <th style="padding:10px 12px;text-align:center">📝 Sınav</th>
          <th style="padding:10px 12px;text-align:center">Başarılı</th>
          <th style="padding:10px 12px;text-align:center">Ort. Puan</th>
          <th style="padding:10px 12px">Son İşlem</th>
          <th style="padding:10px 12px"></th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($users)): ?>
        <tr><td colspan="10" style="padding:20px;color:var(--muted)">Kullanıcı yok.</td></tr>
      <?php else: foreach ($users as $u): ?>
        <?php
        $kid = (int)$u['id'];
        $gidEhliyet = 1; $gidSrc = 2;
        try { $gidEhliyet = (int)($pdo->query("SELECT id FROM education_groups WHERE slug='ehliyet'")->fetchColumn()) ?: 1; } catch (Throwable) {}
        try { $gidSrc = (int)($pdo->query("SELECT id FROM education_groups WHERE slug='src'")->fetchColumn()) ?: 2; } catch (Throwable) {}

        // Grup üyelikleri
        $gruplar = [];
        try {
            $stg = $pdo->prepare("SELECT eg.slug, eg.name FROM user_groups ug JOIN education_groups eg ON eg.id = ug.group_id WHERE ug.user_id = ?");
            $stg->execute([$kid]);
            foreach ($stg->fetchAll() as $g) { $gruplar[$g['slug']] = $g['name']; }
        } catch (Throwable) {}

        // Ders (kitap + animasyon ilerlemesi)
        $dersAdet = 0; $dersTamam = 0;
        try {
            $st2 = $pdo->prepare("SELECT COUNT(*) AS t, SUM(is_completed) AS c FROM user_progress WHERE user_id = ? AND content_type IN ('book','animation')");
            $st2->execute([$kid]);
            $r = $st2->fetch();
            $dersAdet = (int)($r['t'] ?? 0);
            $dersTamam = (int)($r['c'] ?? 0);
        } catch (Throwable) {}

        // Video izleme
        $videoAdet = 0;
        try {
            $st2 = $pdo->prepare("SELECT COUNT(*) FROM video_izleme WHERE kursiyer_id = ?");
            $st2->execute([$kid]);
            $videoAdet = (int)$st2->fetchColumn();
        } catch (Throwable) {}
        try {
            $st2 = $pdo->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND content_type = 'video'");
            $st2->execute([$kid]);
            $videoAdet = max($videoAdet, (int)$st2->fetchColumn());
        } catch (Throwable) {}

        // Sınavlar (her iki grup toplamı)
        $sinavAdet = 0; $sinavBasarili = 0; $ortPuan = 0.0; $sonSinav = '—';
        try {
            $st2 = $pdo->prepare("SELECT COUNT(*) AS t, SUM(is_passed) AS b, ROUND(AVG(NULLIF(score,0)),1) AS p, MAX(finished_at) AS s FROM exam_attempts WHERE user_id = ? AND status = 'bitti'");
            $st2->execute([$kid]);
            $r = $st2->fetch();
            $sinavAdet = (int)($r['t'] ?? 0);
            $sinavBasarili = (int)($r['b'] ?? 0);
            $ortPuan = (float)($r['p'] ?? 0);
            $sonSinav = $r['s'] ? date('d.m.Y H:i', strtotime($r['s'])) : '—';
        } catch (Throwable) {}

        // Son işlem (sınav / ilerleme / video)
        $sonIslem = $sonSinav;
        foreach ([
            ["SELECT MAX(updated_at) FROM user_progress WHERE user_id = ?", [$kid]],
            ["SELECT MAX(son_izleme) FROM video_izleme WHERE kursiyer_id = ?", [$kid]],
            ["SELECT MAX(created_at) FROM kursiyer_aktivite WHERE kursiyer_id = ?", [$kid]],
        ] as $q) {
            try {
                $st2 = $pdo->prepare($q[0]);
                $st2->execute($q[1]);
                $v = $st2->fetchColumn();
                if ($v && ($sonIslem === '—' || strtotime($v) > strtotime($sonIslem))) $sonIslem = date('d.m.Y H:i', strtotime($v));
            } catch (Throwable) {}
        }
        ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px;font-weight:600"><?= e(trim((string)$u['ad'] . ' ' . (string)$u['soyad'])) ?></td>
          <td style="padding:10px 12px">
            <?php foreach ($gruplar as $slug => $ad): ?>
              <span style="padding:2px 8px;border-radius:4px;font-weight:700;font-size:.78rem;margin-right:4px;background:<?= $slug === 'src' ? '#fee2e2;color:#b91c1c' : '#dbeafe;color:#1e40af' ?>"><?= e(strtoupper($ad)) ?></span>
            <?php endforeach; ?>
            <?php if (!$gruplar): ?><span style="color:var(--muted)">—</span><?php endif; ?>
          </td>
          <td style="padding:10px 12px;font-size:.82rem"><?= e((string)($u['telefon'] ?: $u['gsm'] ?: '—')) ?></td>
          <td style="padding:10px 12px;text-align:center"><?= $dersAdet ?><?= $dersTamam ? ' <span style="font-size:.72rem;color:#166534">(' . $dersTamam . ' ✓)</span>' : '' ?></td>
          <td style="padding:10px 12px;text-align:center"><?= $videoAdet ?></td>
          <td style="padding:10px 12px;text-align:center"><?= $sinavAdet ?></td>
          <td style="padding:10px 12px;text-align:center;color:<?= $sinavBasarili > 0 ? '#166534' : '#991b1b' ?>;font-weight:700"><?= $sinavBasarili ?></td>
          <td style="padding:10px 12px;text-align:center;font-weight:700;color:var(--navy)"><?= $ortPuan > 0 ? e((string)$ortPuan) : '—' ?></td>
          <td style="padding:10px 12px;font-size:.8rem"><?= e($sonIslem) ?></td>
          <td style="padding:10px 12px">
            <a href="/admin/kursiyer-detay.php?id=<?= (int)$u['id'] ?>" style="padding:4px 10px;background:#0f766e;color:#fff;border-radius:4px;text-decoration:none;font-size:.8rem">Detay</a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
