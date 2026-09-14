<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

start_app_session();
$user = current_user();
if ($user !== null) {
    if (($user['role'] ?? '') === 'admin') {
        redirect('/admin/index.php');
    }
    redirect('/kursiyer/index.php');
}

$error = '';
$gsmValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gsmValue = normalize_gsm((string)($_POST['telefon'] ?? ''));
    $password = (string)($_POST['sifre'] ?? '');
    $result = attempt_login($gsmValue, $password);
    if ($result['ok']) {
        login_user($result['user']);
        if ($result['user']['role'] === 'admin') {
            redirect('/admin/index.php');
        }
        redirect('/kursiyer/index.php');
    }
    $error = $result['error'] ?? 'Giris basarisiz.';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(SITE_NAME) ?> — Giriş</title>
  <style>
    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; height: 100%; }
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      color: #1a1a1a;
      background:
        linear-gradient(160deg, #f3f5fa 0%, #e6eaf2 55%, #f0f2f5 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      width: min(380px, 92vw);
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 10px 26px rgba(30, 42, 90, 0.18);
      padding: 30px 26px;
      text-align: center;
    }
    .login-card img {
      width: 120px;
      height: auto;
      display: block;
      margin: 0 auto 16px;
    }
    .login-card h2 {
      margin: 0 0 4px;
      font-size: 1.15rem;
      font-weight: 700;
      color: #1e2a5a;
    }
    .login-card .subtitle {
      margin: 0 0 22px;
      font-size: 0.8rem;
      color: #6b7280;
    }
    .alert-error {
      padding: 9px 12px;
      border-radius: 6px;
      font-size: 0.82rem;
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
      margin: 0 0 14px;
    }
    .login-form label {
      display: block;
      text-align: left;
      font-size: 0.82rem;
      color: #4b5563;
      margin-bottom: 5px;
    }
    .login-form input {
      width: 100%;
      padding: 11px 13px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      font-size: 0.95rem;
      margin-top: 2px;
      outline: none;
      transition: border-color .15s, box-shadow .15s;
    }
    .login-form input:focus {
      border-color: #e30a17;
      box-shadow: 0 0 0 3px rgba(227, 10, 23, 0.15);
    }
    .login-form .field { margin-bottom: 14px; }
    .login-form button {
      width: 100%;
      padding: 12px 14px;
      border: 0;
      border-radius: 8px;
      background: #e30a17;
      color: #fff;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      margin-top: 4px;
    }
    .login-form button:hover { background: #b50812; }
    .login-foot {
      margin-top: 16px;
      font-size: 0.76rem;
      color: #9ca3af;
    }
  </style>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6435990060938299"
     crossorigin="anonymous"></script>
  </head>
<body>
  <div class="login-card">
    <img src="/assets/img/metro-logo.png" alt="METRO e-SINAV">

    <h2>KURUM GİRİŞİ</h2>
    <p class="subtitle">Telefon numaranızla giriş yapın</p>

    <?php if ($error !== ''): ?>
      <div class="alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form class="login-form" method="post" action="/index.php" autocomplete="on">
      <div class="field">
        <label for="telefon">Telefon Numarası (GSM)</label>
        <input
          type="tel"
          id="telefon"
          name="telefon"
          inputmode="numeric"
          maxlength="15"
          required
          autocomplete="tel"
          value="<?= e($gsmValue) ?>"
          placeholder="5xx xxx xx xx"
        >
      </div>
      <div class="field">
        <label for="sifre">Şifre <em style="color:#9ca3af">(Yalnızca yöneticiler)</em></label>
        <input
          type="password"
          id="sifre"
          name="sifre"
          autocomplete="current-password"
          placeholder="Yönetici iseniz şifrenizi girin"
        >
      </div>
      <button type="submit">Giriş Yap</button>
      <p style="margin-top:10px;font-size:0.78rem;color:#6b7280;text-align:left">
        Telefon numaranızla direkt giriş yapın. Yeni numara otomatik kaydedilir.
      </p>
    </form>

    <div class="login-foot">METRO SÜRÜCÜ KURSU • e-SINAV</div>
  </div>
</body>
</html>
