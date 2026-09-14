<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/src.php';

$pageTitle = 'SRC Ders Notları';
$activeMenu = 'src-ders-notlari';

$pdo = db();
src_ensure_tables($pdo);

$konular = src_konular();

$rows = [];
try {
    $rows = $pdo->query("SELECT * FROM src_ders_notlari ORDER BY sira, id")->fetchAll();
} catch (Throwable) {
}

require __DIR__ . '/_layout_top.php';
?>
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <h1 style="margin:0;color:var(--navy);font-size:1.25rem">📚 SRC Ders Notları <span style="font-size:.8rem;color:var(--muted)">(<?= count($rows) ?>)</span></h1>
    <a href="/admin/src-ders-notlari-duzenle.php" style="padding:10px 18px;font-size:.9rem;text-decoration:none;display:inline-block;background:#1e3a8a;color:#fff;border-radius:8px;font-weight:700">+ Yeni Ders Notu</a>
  </div>

  <p style="color:var(--muted);font-size:.85rem;margin-top:-6px">Bu içerik katılımcı sayfasındaki <strong>SRC Ders Notları</strong> bölümünü besler. Konu başına birden fazla başlık ekleyebilirsiniz.</p>

  <div class="card" style="padding:0;overflow:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
      <thead>
        <tr style="background:#f8fafc;text-align:left">
          <th style="padding:10px 12px">Sıra</th>
          <th style="padding:10px 12px">Konu</th>
          <th style="padding:10px 12px">Başlık</th>
          <th style="padding:10px 12px">Açıklama</th>
          <th style="padding:10px 12px">Durum</th>
          <th style="padding:10px 12px">İşlem</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="6" style="padding:20px;color:var(--muted)">Kayıt yok. "+ Yeni Ders Notu" ile ekleyin.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr style="border-top:1px solid #eef2f7">
          <td style="padding:10px 12px"><?= (int)$r['sira'] ?></td>
          <td style="padding:10px 12px;white-space:nowrap"><?= e($konular[$r['konu']] ?? (string)$r['konu']) ?></td>
          <td style="padding:10px 12px;max-width:320px">
            <a href="/admin/src-ders-notlari-duzenle.php?id=<?= (int)$r['id'] ?>" style="color:#1d4ed8;font-weight:700;text-decoration:none"><?= e((string)$r['baslik']) ?></a>
          </td>
          <td style="padding:10px 12px;max-width:320px;color:var(--muted)">
            <?php $oz = (string)($r['ozet'] ?? ''); echo e($oz !== '' && mb_strlen($oz) > 80 ? mb_substr($oz, 0, 80) . '…' : $oz); ?>
          </td>
          <td style="padding:10px 12px">
            <form method="post" action="/admin/src-ders-notlari-duzenle.php" style="display:inline">
              <input type="hidden" name="toggle_id" value="<?= (int)$r['id'] ?>">
              <button type="submit" style="border:0;background:transparent;cursor:pointer;font-weight:700;color:<?= (int)$r['aktif'] === 1 ? '#166534' : '#991b1b' ?>"><?= (int)$r['aktif'] === 1 ? 'Aktif' : 'Pasif' ?></button>
            </form>
          </td>
          <td style="padding:10px 12px;white-space:nowrap">
            <a href="/admin/src-ders-notlari-duzenle.php?id=<?= (int)$r['id'] ?>" style="color:#1d4ed8;font-weight:700;text-decoration:none;margin-right:8px">Düzenle</a>
            <form method="post" action="/admin/src-ders-notlari-duzenle.php" style="display:inline" onsubmit="return confirm('Bu ders notu silinsin mi?');">
              <input type="hidden" name="delete_id" value="<?= (int)$r['id'] ?>">
              <button type="submit" style="border:0;background:transparent;color:#b91c1c;font-weight:700;cursor:pointer">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
