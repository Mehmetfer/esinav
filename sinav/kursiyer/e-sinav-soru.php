<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/esinav.php';
require_once dirname(__DIR__) . '/includes/dil.php';

$user = require_role('kursiyer');
dil_baslat((string)($user['gsm'] ?? ''));
$pdo = db();
esinav_ensure_tables($pdo);
esinav_ensure_ar_columns($pdo);

$oturum = esinav_aktif_oturum($pdo, (int)$user['id']);
if (!$oturum) {
    start_app_session();
    redirect(!empty($_SESSION['demo_mode']) ? '/mobil/demo/esinav.php' : '/kursiyer/e-sinav.php');
}

$oturumId = (int)$oturum['id'];
$kalan = esinav_kalan_sn($oturum);
if ($kalan <= 0) {
    require_once dirname(__DIR__) . '/includes/aktivite.php';
    $sonuc = esinav_bitir($pdo, $oturumId);
    aktivite_log(
        (int)$user['id'],
        'esinav_bitir',
        'E-Sınav süre doldu',
        'Oturum #' . $oturumId . ' · Doğru: ' . $sonuc['dogru'] . '/50',
        $sonuc['puan'],
        $sonuc['basarili']
    );
    redirect('/kursiyer/e-sinav-sonuc.php?id=' . $oturumId);
}

$q = max(1, min(ESINAV_SORU_SAYISI, (int)($_GET['q'] ?? 1)));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sira = (int)($_POST['sira'] ?? $q);
    $cevap = (string)($_POST['cevap'] ?? '');
    $action = (string)($_POST['action'] ?? 'next');
    if ($cevap !== '') {
        esinav_cevap_kaydet($pdo, $oturumId, $sira, $cevap);
    }
    if ($action === 'finish') {
        redirect('/kursiyer/e-sinav-bitir.php');
    }
    if ($action === 'prev') {
        redirect('/kursiyer/e-sinav-soru.php?q=' . max(1, $sira - 1));
    }
    if ($action === 'goto') {
        redirect('/kursiyer/e-sinav-soru.php?q=' . max(1, min(ESINAV_SORU_SAYISI, (int)($_POST['goto'] ?? 1))));
    }
    redirect('/kursiyer/e-sinav-soru.php?q=' . min(ESINAV_SORU_SAYISI, $sira + 1));
}

try {
    $soruRow = esinav_oturum_soru($pdo, $oturumId, $q);
} catch (Throwable $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><meta charset="utf-8"><p>Sınav sorusu yüklenemedi. Lütfen yöneticiye bildirin.</p>';
    if (!empty($_GET['debug'])) {
        echo '<pre>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
    }
    exit;
}
if (!$soruRow) {
    redirect('/kursiyer/e-sinav.php');
}
$goster = esinav_soru_gosterim($soruRow);

$cevaps = $pdo->prepare('SELECT sira, cevap FROM esinav_soru WHERE oturum_id = ? ORDER BY sira');
$cevaps->execute([$oturumId]);
$cevapMap = [];
$cevapliSayisi = 0;
foreach ($cevaps->fetchAll() as $row) {
    $cevapMap[(int)$row['sira']] = $row['cevap'] ? strtoupper((string)$row['cevap']) : '';
    if (!empty($row['cevap'])) {
        $cevapliSayisi++;
    }
}

$secili = strtoupper((string)($soruRow['cevap'] ?? ''));
$ilerleme = (int)round(($cevapliSayisi / ESINAV_SORU_SAYISI) * 100);
$ad = (string)($user['name'] ?? 'Kursiyer');
$gsm = (string)($user['gsm'] ?? '');
$initial = function_exists('mb_substr')
    ? mb_strtoupper(mb_substr($ad, 0, 1, 'UTF-8'), 'UTF-8')
    : strtoupper(substr($ad, 0, 1));
$adUpper = function_exists('mb_strtoupper') ? mb_strtoupper($ad, 'UTF-8') : strtoupper($ad);
$mm = (int)floor($kalan / 60);
$ss = (int)($kalan % 60);
$lang = dil();
$dir = $lang === 'ar' ? 'rtl' : 'ltr';
$showLang = tc_yabanci($gsm) || dil_ar();
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>" dir="<?= e($dir) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(__('sinav_turu_deger')) ?></title>
  <link rel="stylesheet" href="../assets/css/esinav.css?v=7">
  <style>
    .dil-toggle{display:flex;align-items:center;gap:6px;font-size:.78rem;font-weight:700;margin-inline-end:10px}
    .dil-toggle .dil-opt{color:#334155;text-decoration:none;padding:4px 8px;border-radius:6px;background:#f1f5f9}
    .dil-toggle .dil-opt.active{background:#1e3a8a;color:#fff}
    .dil-sep{opacity:.45}
    .mtsk-qtext,.mtsk-opt span{unicode-bidi:plaintext}
    html[dir=rtl] .mtsk-footer{direction:rtl}
    html[dir=rtl] .mtsk-nav{direction:rtl}
  </style>
</head>
<body class="mtsk-body">
<form method="post" id="examForm" class="mtsk-wrap">
  <input type="hidden" name="sira" value="<?= $q ?>">
  <input type="hidden" name="action" id="actionField" value="next">
  <input type="hidden" name="goto" id="gotoField" value="">
  <input type="hidden" name="cevap" id="cevapField" value="<?= e($secili) ?>">

  <header class="mtsk-header">
    <div class="mtsk-brand">
      <div class="mtsk-mini-grid" aria-hidden="true">
        <?php for ($i = 1; $i <= 12; $i++): ?>
          <i class="<?= !empty($cevapMap[$i]) ? 'on' : '' ?>"></i>
        <?php endfor; ?>
      </div>
      <strong>e-sınav</strong>
    </div>

    <div class="mtsk-user">
      <span class="mtsk-photo"><?= e($initial) ?></span>
      <div>
        <div class="mtsk-sayin"><?= e(__('sayin')) ?> <?= e($adUpper) ?></div>
        <div class="mtsk-tc"><?= e(__('gsm_no')) ?>: <?= e($gsm) ?></div>
      </div>
    </div>

    <div class="mtsk-exam-type"><?= e(__('sinav_turu')) ?>: <b><?= e(__('sinav_turu_deger')) ?></b></div>

    <div class="mtsk-right">
      <?php if ($showLang): ?>
        <?= dil_toggle_html('mtsk-dil') ?>
      <?php endif; ?>
      <div class="mtsk-clock" title="<?= e(__('kalan_sure')) ?>">
        <svg viewBox="0 0 36 36" class="mtsk-ring">
          <path class="bg" d="M18 2.5 a 15.5 15.5 0 1 1 0 31 a 15.5 15.5 0 1 1 0 -31"/>
          <path id="ringPath" class="fg" stroke-dasharray="97.4, 100" d="M18 2.5 a 15.5 15.5 0 1 1 0 31 a 15.5 15.5 0 1 1 0 -31"/>
        </svg>
        <span id="timer" data-remain="<?= $kalan ?>" data-total="<?= ESINAV_SURE_SN ?>"><?= sprintf('%02d:%02d', $mm, $ss) ?></span>
      </div>
      <div class="mtsk-clock-label"><?= e(__('kalan_sure')) ?></div>
      <button type="button" class="mtsk-info" title="i">i</button>
      <button type="submit" class="mtsk-finish"
              onclick="document.getElementById('actionField').value='finish'">⏻ <?= e(__('sinavi_bitir')) ?></button>
    </div>
  </header>

  <main class="mtsk-main">
    <section class="mtsk-qbox">
      <div class="mtsk-qhead">
        <h1><?= e(__('soru')) ?> : <?= $q ?></h1>
        <div class="mtsk-progress">
          <span><?= e(__('ilerleme')) ?></span>
          <div class="mtsk-bar"><i style="width:<?= $ilerleme ?>%"></i></div>
          <b>%<?= $ilerleme ?></b>
        </div>
      </div>
      <p class="mtsk-qtext"><?= nl2br(e($goster['soru'])) ?></p>
      <?php
      $gorselRel = $goster['gorsel'] ?? null;
      if ($gorselRel):
          $gorselFs = dirname(__DIR__) . '/assets/img/sorular/' . ltrim(str_replace('\\', '/', (string)$gorselRel), '/');
          $gorselUrl = '../assets/img/sorular/' . ltrim(str_replace('\\', '/', (string)$gorselRel), '/');
          if (is_file($gorselFs)):
      ?>
        <div class="mtsk-qimg">
          <img src="<?= e($gorselUrl) ?>" alt="Soru görseli" loading="lazy">
        </div>
      <?php
          endif;
      endif;
      $soruId = (int)($soruRow['soru_id'] ?? 0);
      ?>
      <div class="mtsk-report-wrap">
        <button type="button" class="mtsk-report" id="btnHataliBildir"
                data-soru="<?= $soruId ?>" data-oturum="<?= $oturumId ?>">
          ⚑ <?= e(__('hatali_soru_bildir')) ?>
        </button>
        <span class="mtsk-report-msg" id="hataliBildirMsg" hidden></span>
      </div>
    </section>

    <section class="mtsk-opts">
      <?php
      $opts = ['A' => $goster['a'], 'B' => $goster['b'], 'C' => $goster['c'], 'D' => $goster['d']];
      foreach ($opts as $harf => $metin):
      ?>
        <button type="button" class="mtsk-opt <?= $secili === $harf ? 'selected' : '' ?>" data-val="<?= $harf ?>">
          <em><?= $harf ?>)</em>
          <span><?= e((string)$metin) ?></span>
        </button>
      <?php endforeach; ?>
    </section>
  </main>

  <footer class="mtsk-footer">
    <button type="submit" class="mtsk-nav" <?= $q <= 1 ? 'disabled' : '' ?>
            onclick="document.getElementById('actionField').value='prev'">◀ <?= e(__('onceki')) ?></button>

    <div class="mtsk-grid">
      <?php for ($i = 1; $i <= ESINAV_SORU_SAYISI; $i++):
        $ans = $cevapMap[$i] ?? '';
        $cls = 'mtsk-cell';
        if ($i === $q) {
            $cls .= ' current';
        } elseif ($ans !== '') {
            $cls .= ' answered';
        }
      ?>
        <button type="submit" class="<?= $cls ?>"
                onclick="document.getElementById('actionField').value='goto';document.getElementById('gotoField').value='<?= $i ?>'">
          <?php if ($ans !== '' && $i !== $q): ?>
            <span class="ans"><?= e($ans) ?></span>
          <?php elseif ($i === $q): ?>
            <span class="cur"></span>
          <?php else: ?>
            <span class="empty"></span>
          <?php endif; ?>
          <span class="no"><?= $i ?></span>
        </button>
      <?php endfor; ?>
    </div>

    <button type="submit" class="mtsk-nav"
            onclick="document.getElementById('actionField').value='next'">
      <?= $q >= ESINAV_SORU_SAYISI ? e(__('son')) : e(__('sonraki')) . ' ▶' ?>
    </button>
  </footer>
</form>

<script>
(function () {
  document.querySelectorAll('.mtsk-opt').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.mtsk-opt').forEach(function (b) { b.classList.remove('selected'); });
      btn.classList.add('selected');
      document.getElementById('cevapField').value = btn.getAttribute('data-val') || '';
    });
  });

  var reportBtn = document.getElementById('btnHataliBildir');
  var reportMsg = document.getElementById('hataliBildirMsg');
  if (reportBtn) {
    reportBtn.addEventListener('click', function () {
      var note = window.prompt(<?= json_encode(__('hatali_soru_aciklama'), JSON_UNESCAPED_UNICODE) ?>, '');
      if (note === null) {
        return;
      }
      reportBtn.disabled = true;
      var fd = new FormData();
      fd.append('soru_id', reportBtn.getAttribute('data-soru') || '0');
      fd.append('oturum_id', reportBtn.getAttribute('data-oturum') || '0');
      fd.append('not', note);
      fetch('/kursiyer/e-sinav-bildir.php', { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (reportMsg) {
            reportMsg.hidden = false;
            reportMsg.textContent = (j && j.ok)
              ? (j.message || <?= json_encode(__('bildirim_alindi'), JSON_UNESCAPED_UNICODE) ?>)
              : ((j && j.error) || 'Hata');
            reportMsg.className = 'mtsk-report-msg ' + (j && j.ok ? 'ok' : 'err');
          }
          if (!(j && j.ok)) {
            reportBtn.disabled = false;
          }
        })
        .catch(function () {
          reportBtn.disabled = false;
          if (reportMsg) {
            reportMsg.hidden = false;
            reportMsg.textContent = 'Bağlantı hatası';
            reportMsg.className = 'mtsk-report-msg err';
          }
        });
    });
  }

  var el = document.getElementById('timer');
  var remain = parseInt(el.getAttribute('data-remain') || '0', 10);
  var total = parseInt(el.getAttribute('data-total') || '2700', 10);
  var ring = document.getElementById('ringPath');
  var circ = 97.4;
  function pad(n) { return (n < 10 ? '0' : '') + n; }
  function tick() {
    if (remain <= 0) {
      el.textContent = '00:00';
      document.getElementById('actionField').value = 'finish';
      document.getElementById('examForm').submit();
      return;
    }
    var m = Math.floor(remain / 60);
    var s = remain % 60;
    el.textContent = pad(m) + ':' + pad(s);
    if (ring) {
      var pct = Math.max(0, remain / total);
      ring.setAttribute('stroke-dasharray', (circ * pct).toFixed(1) + ', 100');
    }
    remain -= 1;
    setTimeout(tick, 1000);
  }
  tick();
})();
</script>
</body>
</html>

