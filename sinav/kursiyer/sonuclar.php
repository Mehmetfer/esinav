<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/education.php';

$pageTitle = 'Sınav Sonuçlarım';
$activeMenu = 'sonuclar';
require __DIR__ . '/_layout_top.php';

$user = current_user() ?? [];
$userId = (int)($user['id'] ?? 0);
$groupId = (int)($user['group_id'] ?? 0);

if ($groupId <= 0) {
    $ug = user_groups($userId);
    $groupId = !empty($ug) ? (int)$ug[0] : 0;
}

$sonuclar = [];
try {
    $pdo = db();
    $st = $pdo->prepare("
        SELECT ea.*, e.title as exam_title, e.type as exam_type 
        FROM exam_attempts ea
        LEFT JOIN exams e ON e.id = ea.exam_id
        WHERE ea.user_id = ? AND ea.group_id = ? AND ea.status = 'bitti'
        ORDER BY ea.finished_at DESC
        LIMIT 50
    ");
    $st->execute([$userId, $groupId]);
    $sonuclar = $st->fetchAll();
} catch (Throwable) {}
?>
  <h1 class="k-page-title">Sınav Sonuçlarım</h1>

  <?php if (empty($sonuclar)): ?>
    <section class="k-card">
      <div style="text-align:center;padding:40px;color:var(--muted)">
        <div style="font-size:3rem;margin-bottom:12px">📊</div>
        <div style="font-size:1.1rem;font-weight:600;margin-bottom:8px">Henüz sınav sonucu yok</div>
        <div style="font-size:.9rem">Sınava başladığınızda sonuçlar burada görünecek.</div>
      </div>
    </section>
  <?php else: ?>
    <section class="k-card">
      <div class="k-table-wrap">
        <table class="k-table">
          <thead>
            <tr>
              <th>Sınav</th>
              <th>Tarih</th>
              <th>Doğru</th>
              <th>Yanlış</th>
              <th>Boş</th>
              <th>Puan</th>
              <th>Sonuç</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sonuclar as $s): ?>
              <tr>
                <td><?= e($s['exam_title'] ?? $s['exam_type']) ?></td>
                <td><?= e(date('d.m.Y H:i', strtotime($s['finished_at']))) ?></td>
                <td style="color:#166534;font-weight:700"><?= (int)$s['correct_count'] ?></td>
                <td style="color:#b91c1c;font-weight:700"><?= (int)$s['wrong_count'] ?></td>
                <td style="color:var(--muted)"><?= (int)$s['empty_count'] ?></td>
                <td style="font-weight:800;color:<?= (int)$s['score'] >= 70 ? '#166534' : '#b91c1c' ?>"><?= (int)$s['score'] ?></td>
                <td><?= (int)$s['is_passed'] === 1 ? '<span style="color:#166534;font-weight:700">Geçti</span>' : '<span style="color:#b91c1c;font-weight:700">Kaldı</span>' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  <?php endif; ?>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
