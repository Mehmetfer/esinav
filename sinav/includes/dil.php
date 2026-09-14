<?php
declare(strict_types=1);

/**
 * Dil tercihi: TC 99 ile başlıyorsa varsayılan Arapça;
 * kullanıcı istediğinde Türkçe’ye (veya tekrar Arapça’ya) geçebilir.
 */

require_once __DIR__ . '/auth.php';

/** GSM ile giriste yabanci kimlik (TC 99…) tespiti yapilamaz.
 * Varsayilan dil Turkce; kullanici arayuzden degistirebilir. */
function tc_yabanci(string $ident): bool
{
    return false;
}

function dil_gecerli(?string $dil): bool
{
    return $dil === 'tr' || $dil === 'ar';
}

/**
 * Oturum dilini başlat / güncelle.
 * ?dil=tr|ar veya POST dil ile değiştirilebilir.
 */
function dil_baslat(?string $tc = null): void
{
    start_app_session();

    $req = null;
    if (isset($_GET['dil']) && dil_gecerli((string)$_GET['dil'])) {
        $req = (string)$_GET['dil'];
    } elseif (isset($_POST['dil']) && dil_gecerli((string)$_POST['dil'])) {
        $req = (string)$_POST['dil'];
    }
    if ($req !== null) {
        $_SESSION['dil'] = $req;
        return;
    }

    if (!empty($_SESSION['dil']) && dil_gecerli((string)$_SESSION['dil'])) {
        return;
    }

    if ($tc === null) {
        $user = current_user();
        $tc = (string)($user['gsm'] ?? '');
    }
    $_SESSION['dil'] = tc_yabanci($tc) ? 'ar' : 'tr';
}

/** Aktif dil: tr | ar */
function dil(): string
{
    dil_baslat();
    return ($_SESSION['dil'] ?? 'tr') === 'ar' ? 'ar' : 'tr';
}

function dil_ar(): bool
{
    return dil() === 'ar';
}

/** Giriş sonrası varsayılan dili ayarla (GSM bazli: varsayilan Turkce). */
function dil_login_ayarla(string $ident): void
{
    start_app_session();
    $_SESSION['dil'] = tc_yabanci($ident) ? 'ar' : 'tr';
}

/**
 * Mevcut URL’ye dil parametresi ekle (toggle linkleri için).
 */
function dil_url(string $hedefDil, ?string $path = null): string
{
    $hedefDil = dil_gecerli($hedefDil) ? $hedefDil : 'tr';
    if ($path === null) {
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $parts = parse_url($uri);
        $path = (string)($parts['path'] ?? '/');
        parse_str((string)($parts['query'] ?? ''), $q);
    } else {
        $q = [];
        $parts = parse_url($path);
        $path = (string)($parts['path'] ?? $path);
        parse_str((string)($parts['query'] ?? ''), $q);
    }
    $q['dil'] = $hedefDil;
    return $path . '?' . http_build_query($q);
}

/** @return array<string,string> */
function dil_metinler(string $lang): array
{
    static $pack = null;
    if ($pack === null) {
        $pack = [
            'tr' => [
                'sayin' => 'Sayın',
                'tc_no' => 'T.C. No',
                'gsm_no' => 'GSM Numarası',
                'sinav_turu' => 'Sınav Türü',
                'sinav_turu_deger' => 'MTSK TEORİK E-SINAV',
                'kalan_sure' => 'Sınavda Kalan Süre',
                'sinavi_bitir' => 'Sınavı Bitir',
                'soru' => 'Soru',
                'ilerleme' => 'Sınav İlerleme Durumu',
                'onceki' => 'Önceki Soru',
                'sonraki' => 'Sonraki Soru',
                'son' => 'Son',
                'basla' => 'Başla',
                'devam_et' => 'Devam Et',
                'esinav' => 'E-SINAV',
                'sure' => 'Sınav Süresi',
                'onemli' => 'Önemli Bilgi',
                'basla_info' => 'Başla butonuna bastıktan sonra sorular sayfasına yönlendirileceksiniz ve süreniz başlamış olacaktır. Her sınav 50 sorudur. Her doğru 2 puan, yanlış doğruyu götürmez. 70 puan ve üzeri başarılıdır.',
                'devam_info' => 'Devam eden bir sınavınız var. Kalan süre sayaçta görünecek.',
                'gecmis' => 'Katıldığınız E-Sınavlar',
                'dil_tr' => 'Türkçe',
                'dil_ar' => 'العربية',
                'hosgeldiniz' => 'Hoş Geldiniz',
                'giris_aciklama' => 'Lütfen TC Kimlik No ve şifrenizle giriş yapınız.',
                'tc_label' => 'TC Kimlik No',
                'sifre_label' => 'Şifre',
                'giris_yap' => 'GİRİŞ YAP',
                'guvenli' => 'Güvenli Giriş',
                'sonuc' => 'Sonuç',
                'gecti' => 'Geçti',
                'kaldi' => 'Kaldı',
                'not' => 'Not',
                'toplam_soru' => 'Toplam Soru',
                'dogru' => 'Doğru',
                'yanlis' => 'Yanlış',
                'bos' => 'Boş',
                'esinava_don' => 'E-Sınav’a Dön',
                'uygulama_menu' => 'Uygulama Ana Menü',
                'trafik_isaret' => 'Trafik İşaretleri',
                'konu_anlatim' => 'Konu Anlatımı',
                'videolu' => 'Videolu Dersler',
                'cikis' => 'Çıkış Yap',
                'hatali_soru_bildir' => 'Hatalı Soruyu Bildir',
                'hatali_soru_aciklama' => 'Hatalı olduğunu düşündüğünüz kısmı kısaca yazın:',
                'bildirim_alindi' => 'Bildiriminiz alındı, teşekkürler.',
            ],
            'ar' => [
                'sayin' => 'السيد/ة',
                'tc_no' => 'رقم الهوية',
                'gsm_no' => 'رقم الهاتف',
                'sinav_turu' => 'نوع الاختبار',
                'sinav_turu_deger' => 'اختبار MTSK النظري الإلكتروني',
                'kalan_sure' => 'الوقت المتبقي',
                'sinavi_bitir' => 'إنهاء الاختبار',
                'soru' => 'السؤال',
                'ilerleme' => 'تقدم الاختبار',
                'onceki' => 'السابق',
                'sonraki' => 'التالي',
                'son' => 'النهاية',
                'basla' => 'ابدأ',
                'devam_et' => 'متابعة',
                'esinav' => 'الاختبار الإلكتروني',
                'sure' => 'مدة الاختبار',
                'onemli' => 'معلومة مهمة',
                'basla_info' => 'بعد الضغط على ابدأ سيتم توجيهك إلى الأسئلة ويبدأ الوقت. كل اختبار 50 سؤالاً. كل إجابة صحيحة نقطتان، والخطأ لا يخصم. النجاح من 70 درجة فأكثر.',
                'devam_info' => 'لديك اختبار جارٍ. سيظهر الوقت المتبقي في العداد.',
                'gecmis' => 'اختباراتك السابقة',
                'dil_tr' => 'Türkçe',
                'dil_ar' => 'العربية',
                'hosgeldiniz' => 'مرحباً بكم',
                'giris_aciklama' => 'يرجى تسجيل الدخول برقم الهوية وكلمة المرور.',
                'tc_label' => 'رقم الهوية التركية',
                'sifre_label' => 'كلمة المرور',
                'giris_yap' => 'تسجيل الدخول',
                'guvenli' => 'دخول آمن',
                'sonuc' => 'النتيجة',
                'gecti' => 'ناجح',
                'kaldi' => 'راسب',
                'not' => 'الدرجة',
                'toplam_soru' => 'مجموع الأسئلة',
                'dogru' => 'صحيح',
                'yanlis' => 'خطأ',
                'bos' => 'فارغ',
                'esinava_don' => 'العودة للاختبار',
                'uygulama_menu' => 'القائمة الرئيسية',
                'trafik_isaret' => 'إشارات المرور',
                'konu_anlatim' => 'شرح المواضيع',
                'videolu' => 'دروس فيديو',
                'cikis' => 'تسجيل الخروج',
                'hatali_soru_bildir' => 'الإبلاغ عن سؤال خاطئ',
                'hatali_soru_aciklama' => 'اكتب باختصار الجزء الذي تعتقد أنه خاطئ:',
                'bildirim_alindi' => 'تم استلام بلاغك، شكراً لك.',
            ],
        ];
    }
    return $pack[$lang] ?? $pack['tr'];
}

function __(string $key): string
{
    $m = dil_metinler(dil());
    if (isset($m[$key])) {
        return $m[$key];
    }
    $tr = dil_metinler('tr');
    return $tr[$key] ?? $key;
}

/**
 * Dil değiştirme çubuğu HTML (99’lu kullanıcı veya AR tercihi).
 */
function dil_toggle_html(string $extraClass = ''): string
{
    $user = current_user();
    $gsm = (string)($user['gsm'] ?? '');
    $show = tc_yabanci($gsm) || dil_ar();
    if (!$show && $gsm === '') {
        // Giriş öncesi: herkese göster
        $show = true;
    }
    if (!$show) {
        return '';
    }
    $cur = dil();
    $trUrl = e(dil_url('tr'));
    $arUrl = e(dil_url('ar'));
    $cls = 'dil-toggle' . ($extraClass !== '' ? ' ' . e($extraClass) : '');
    $trActive = $cur === 'tr' ? ' active' : '';
    $arActive = $cur === 'ar' ? ' active' : '';
    return '<nav class="' . $cls . '" aria-label="Language">'
        . '<a class="dil-opt' . $trActive . '" href="' . $trUrl . '">Türkçe</a>'
        . '<span class="dil-sep">|</span>'
        . '<a class="dil-opt' . $arActive . '" href="' . $arUrl . '" dir="rtl">العربية</a>'
        . '</nav>';
}

