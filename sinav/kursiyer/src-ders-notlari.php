<?php
require_once __DIR__ . '/../includes/auth.php';

$tur = isset($_GET['tur']) ? strtolower($_GET['tur']) : 'src1';
$grup = isset($_GET['grup']) ? $_GET['grup'] : 'src';
$kID = isset($_GET['kID']) ? (int)$_GET['kID'] : 0;
$tur_baslik = strtoupper($tur);

$dersler = [
    1 => [
        "baslik" => "01 - İŞ SAĞLIĞI VE İŞ GÜVENLİĞİ",
        "ico" => "fa-hard-hat",
        "ozet" => "İş sağlığı ve güvenliği, çevre güvenliği, acil durum talimatları, yük güvenliği ve müşteri memnuniyeti esasları.",
        "icerik" => '
            <div class="ders-detay">
                <h2>1. İş Sağlığı ve Güvenliği, Çevre Güvenliği ve Kalite, Müşteri Memnuniyeti</h2>
                
                <h3>1. Acil Durum ve İş Sağlığı Güvenliği Talimatı</h3>
                <p>İş kazası ve meslek hastalıklarının tanımı iş kanununda yapılmıştır.</p>
                
                <h4>A. Genel İş Sağlığı ve Güvenliği</h4>
                <p>Çalışma hayatında işveren ile çalışan arasındaki iş ilişkisini düzenleyen iki önemli kanun vardır: <strong>4857 Sayılı İş Kanunu (2003)</strong> ve <strong>6331 Sayılı İş Sağlığı ve Güvenliği Kanunu (2012)</strong>. Bu iki kanun ile çalışanların iş hayatındaki tüm yasal hakları güvence altına alınmıştır.</p>
                
                <h5>1. İşverenin Genel Yükümlülüğü (6331 Sayılı Kanun Madde 4)</h5>
                <ul>
                    <li><strong>a)</strong> Mesleki risklerin önlenmesi, eğitim ve bilgi verilmesi dâhil her türlü tedbirin alınması, organizasyonun yapılması, gerekli araç ve gereçlerin sağlanması.</li>
                    <li><strong>b)</strong> İşyerinde alınan iş sağlığı ve güvenliği tedbirlerine uyulup uyulmadığını izler, denetler ve uygunsuzlukları giderir.</li>
                    <li><strong>c)</strong> Risk değerlendirmesi yapar veya yaptırır.</li>
                    <li><strong>ç)</strong> Çalışana görev verirken, çalışanın sağlık ve güvenlik yönünden işe uygunluğunu göz önüne alır.</li>
                    <li><strong>d)</strong> Yeterli bilgi ve talimat verilenler dışındaki çalışanların hayati ve özel tehlike bulunan yerlere girmemesi için gerekli tedbirleri alır.</li>
                </ul>
                <p><em>Not: İşyeri dışındaki uzman kişilerden hizmet alınması veya çalışanların yükümlülükleri işverenin sorumluluğunu ortadan kaldırmaz. İşveren tedbir maliyetini çalışanlara yansıtamaz.</em></p>

                <h5>2. Çalışanların Yükümlülükleri (Madde 19)</h5>
                <ul>
                    <li>İşyerindeki makine, cihaz, araç, gereç ve taşıma ekipmanlarını kurallara uygun kullanmak, güvenlik donanımlarını keyfi olarak çıkarmamak.</li>
                    <li>Kendilerine sağlanan kişisel koruyucu donanımı (KKD) doğru kullanmak ve korumak.</li>
                    <li>Tehlikeli bir durumla karşılaştıklarında işverene veya çalışan temsilcisine derhal haber vermek.</li>
                    <li>Teftişe yetkili makamlar ve işverenle iş birliği yapmak.</li>
                </ul>

                <h5>3. Acil Durumlar & Araç Güvenlik Ekipmanları</h5>
                <p>Acil durum yönetimi; çalışanları iş kazaları ve meslek hastalıklarına karşı koruyarak ruh ve beden bütünlüklerini sağlamaktır.</p>
                <ul>
                    <li>Araçlarda mutlaka <strong>yangın söndürme tüpü</strong> bulunmalı ve kullanımı bilinmelidir.</li>
                    <li>Araç içinde mutlaka <strong>ilk yardım çantası</strong> olmalıdır.</li>
                    <li>Acil telefon numaraları araç içinde görülebilecek bir yere asılmalıdır.</li>
                </ul>

                <h5>4. Sürücünün Çalışma Ortamı ve Yük Güvenliği</h5>
                <p>Sürücülerin çalışma ortamı trafiktir. Sürücülere 6 ayda bir farkındalık eğitimleri verilmeli, defansif sürüş teknikleri uygulanmalıdır.</p>
                <ul>
                    <li><strong>Kabinin Düzenlenmesi:</strong> Buzdolabı, televizyon, yangın söndürücü gibi cihazlar kabin içinde sabitlenmelidir.</li>
                    <li><strong>Yükleme Sonrası Kontroller:</strong> Maksimum brüt ağırlık aşımı, tehlikeli maddelerde turuncu levha kontrolü, yükün mühür ve bağlama aparatları (cırcırlı makaralı gerdirmeler) denetlenmelidir.</li>
                    <li><strong>Yük Güvenliği Sorumluluğu:</strong> Yol güvenliği yasal hükümlere göre tek başınıza sürücünün sorumluluğundadır. Yük güvenliği yetersizse yükleme noktası terk edilmemelidir.</li>
                </ul>

                <h5>5. Araçlarda Yangın & Psikososyal Etkenler</h5>
                <ul>
                    <li>Motor veya yakıt tankı yangınlarında <strong>kesinlikle su kullanılmamalıdır</strong>. Kuru tozlu söndürücü, kum veya toprak tercih edilmelidir.</li>
                    <li>Düzenli stres yönetimi eğitimleri verilmeli ve sürücülerin sağlık kontrolleri aksatılmamalıdır.</li>
                </ul>

                <hr style="margin:20px 0; border:0; border-top:1px solid #e2e8f0;">

                <h3>2. Çevre Güvenliği Önlemleri</h3>
                <p><strong>Acil Durum Sebepleri:</strong> Yangın, deprem, sel, hortum, patlama, sabotaj, trafik kazası, döküntü-sızıntı, gaz zehirlenmesi gibi olaylardır.</p>
                <ul>
                    <li><strong>Acil Durum Ekipleri:</strong> Söndürme, Kurtarma, Koruma ve İlkyardım ekiplerinden oluşur.</li>
                    <li><strong>Tatbikatlar:</strong> Yılda 1 defa <em>Yangın Tatbikatı</em>, yılda 2 defa <em>Acil Tahliye Tatbikatı</em> yapılması zorunludur.</li>
                    <li><strong>Kaza Anında Yapılacaklar:</strong> Çevre emniyete alınmalı, yaralılar güvenli alana taşınmalı, acil servis ve kolluk kuvvetlerine haber verilmelidir.</li>
                </ul>

                <hr style="margin:20px 0; border:0; border-top:1px solid #e2e8f0;">

                <h3>3. Kalite ve Müşteri Memnuniyeti</h3>
                <p><strong>Kalite:</strong> Bir mal veya hizmetin belirli ihtiyaçları karşılayabilme yeteneklerinin tamamıdır.</p>
                <p><strong>Müşteri Memnuniyeti:</strong> Müşterilerin beklenti ve şikayetlerinin karşılanma düzeyidir. Kısaca <em>Müşteri Memnuniyeti = Kalite</em>dir. Kalite lüks veya pahalı olanı değil, şartlara uygunluğu ifade eder.</p>
            </div>
        '
    ],
    2 => [
        "baslik" => "02 - İŞ ORGANİZASYONU",
        "ico" => "fa-sitemap",
        "ozet" => "İş öncesi hazırlık, araç temizliği ve kontrolleri, güzergâh planlama, güzergâh şeması ve taşımacılık belgeleri.",
        "icerik" => '
            <div class="ders-detay">
                <h2>2. İş Organizasyonu</h2>
                
                <h3>2.1. İş Öncesi Hazırlık</h3>
                <p><strong>Aracın Temizliği ve Kontrolü:</strong> Görev talimatını alan personel, araç personeli ile tanıştıktan sonra aracın temizlik ve teknik donanımını kontrol ederek aracı kullanıma hazır duruma getirir.</p>
                <ul>
                    <li><strong>Prestij ve Memnuniyet:</strong> Aracın temizliği yolcu memnuniyetini ve firmanın itibarını doğrudan etkiler.</li>
                    <li><strong>Cam Temizliği:</strong> Yolculuğun sağlıklı geçmesi ve manzara görüşü için kritiktir. Kirliyse temizlenmesi sağlanır.</li>
                    <li><strong>Koltuk ve İç Hijyen:</strong> Koltuk ceplerine çöp/kusmuk torbaları yerleştirilmeli; kirli, yırtık veya sökük döşemeler kaptana bildirilmelidir.</li>
                    <li><strong>Zemin Hijyeni:</strong> Tozlu ve kirli zemin kapalı alanda aynı havayı soluyan yolcuların sağlığını tehdit eder. Süpürülüp silinmesi sağlanmalıdır.</li>
                    <li><strong>Servis Malzemeleri:</strong> Malzemeler kontrol edilmeli, eksikler kaptana rapor edilmeli, gereksiz malzemeler bagaj kısmına indirilmelidir.</li>
                </ul>

                <h4>Araç Teknik ve Güvenlik Kuralları</h4>
                <ul>
                    <li>Gündüz loş/karanlık ortamlarda veya gece kullanımında ön ve arka ışıklar mutlaka yakılmalıdır.</li>
                    <li>Güçlü fren sistemleri ve dikiz aynaları eksiksiz olmalıdır.</li>
                    <li>Benzin, mazot vb. akaryakıtla çalışan araçlar patlayıcı/parlayıcı maddelerin, tozların bulunduğu kapalı binalarda kullanılmamalıdır.</li>
                    <li>Klakson/korna sesleri işyerindeki diğer sesleri bastıracak güçte olmalı ve aynı işyerindeki araçlarda aynı ton tercih edilmelidir.</li>
                    <li>Görevli olmayanların araç/römork üzerine çıkması engellenmeli; gabari dışı yükleme yapılmamalı, yükler sağlam bağlanmalıdır.</li>
                </ul>

                <hr style="margin:20px 0; border:0; border-top:1px solid #e2e8f0;">

                <h3>2.2. Güzergâh Planlama</h3>
                <p>Sipariş, araç, iklim, iş gücü, kapasite, hız, maliyet ve zaman bilgilerine göre başlangıç ile bitiş noktaları arasında en uygun yolun belirlenmesi sürecidir.</p>
                <p><em>“En iyi yol bildiğin yoldur.”</em> ilkesiyle hareket edilirken; navigasyon, harita ve dijital cihazlar yardımıyla iki nokta arasındaki mesafe (km) ve süre hesaplaması yapılır.</p>

                <h4>2.2.1. Güzergâh Şeması</h4>
                <p>Gidilecek yol, ara duraklar, mola yerleri ve yolcu indirme/bindirme noktaları hakkında önceden bilgi sahibi olunmalıdır. İlk kez gidilecekse yol haritası edinilmeli ve notlar alınmalıdır.</p>

                <h4>2.2.2. Güzergâh Şemasının Hazırlanması</h4>
                <p>Gidiş-dönüş güzergâhı, yolculuk saatleri (sabah kahvaltısı vb.), mola yerleri ve ikram planlaması önceden tespit edilir.</p>

                <div style="background:#f7fafc; border:1px solid #cbd5e0; padding:15px; border-radius:6px; margin:15px 0;">
                    <strong style="color:#2b6cb0;">Örnek Güzergâh Şeması</strong>
                    <ul style="margin-top:8px; margin-bottom:0;">
                        <li><strong>Sefer Saati ve Tarih:</strong> 20/12/2016 - 15:00</li>
                        <li><strong>Gidiş Güzergâhı:</strong> Kocaeli / Yalova / Bursa</li>
                        <li><strong>Dönüş Güzergâhı:</strong> Bursa / Yalova / Kocaeli</li>
                        <li><strong>Ara Termindaller / Duraklar:</strong> Gölcük - Orhangazi - Gemlik</li>
                        <li><strong>Mola Yeri ve Saati:</strong> Yalova / 17:00</li>
                    </ul>
                </div>

                <hr style="margin:20px 0; border:0; border-top:1px solid #e2e8f0;">

                <h3>2.3. Belge ve Eşya Kontrolü</h3>
                <p><strong>4925 Sayılı Karayolu Taşıma Yönetmeliği</strong> uyarınca yolcu taşımacılığında bulunması gereken zorunlu belgeler:</p>
                <ul>
                    <li><strong>Taşıt Kartı:</strong> Aracın yetki belgesine ekli olduğunu gösterir.</li>
                    <li><strong>Sigortalar:</strong> Yolcu Taşıma Sorumluluk Sigortası ve ilgili Yolcu Taşıma Sigortası.</li>
                </ul>

                <h4>Uluslararası Taşımacılıkta Aranan Belgeler</h4>
                <h5>Tarifeli Taşımalar:</h5>
                <ul>
                    <li>Taşıma yapılan ve transit geçilen ülkelerden alınan izin belgeleri.</li>
                    <li>Onaylı zaman tarifesi, ücret tarifesi ve hat krokisi.</li>
                    <li>Yolcu biletleri ve onaylı yolcu listesi.</li>
                </ul>

                <h5>Tarifesiz (Turistik) ve Mekik Taşımalar:</h5>
                <ul>
                    <li><strong>Interbus Belgesi</strong> ve onaylı yolcu listesi (Ad, Soyad, Pasaport No).</li>
                    <li>Taşıma yapılacak ülke makamlarınca onaylanan seyahat programı.</li>
                    <li><em>Not: Mekik taşımalarda ilk dönüş seferi ile son gidiş seferi boş olarak gerçekleştirilir.</em></li>
                </ul>
            </div>
        '
    ],
    3 => [
        "baslik" => "03 - ARACIN YOLCULUK ÖNCESİ SÜRÜŞ HAZIRLIĞI",
        "ico" => "fa-clipboard-check",
        "ozet" => "Lastik bakımı, hava basınç değerleri, lastiğin yapısı ve bölümleri ile araç öncesi mekanik/elektronik kontroller.",
        "icerik" => '
            <div class="ders-detay">
                <h2>3. Aracın Yolculuk Öncesi Sürüş Hazırlığı</h2>
                
                <h3>3.1. Lastik Kontrolü</h3>
                <p>Düzenli olarak lastik bakımı yaptırmak kaza riskini önemli ölçüde azaltır. Yedek lastikler dâhil tüm lastikler yılda en az bir kere ve periyodik olarak kontrol ettirilmelidir. Yola çıkmadan en az bir hafta öncesinde yapılan kontroller önceden tedbir almayı sağlar.</p>
                
                <h4>Deformasyon ve Aşınma Kontrolü</h4>
                <ul>
                    <li>Lastikteki kesik, çatlak, balon yapma ve dengesiz aşınmalar gözle, elle ve diş ölçerle zaman zaman farklı noktalardan denetlenmelidir.</li>
                    <li><strong>Yasal Diş Derinliği Sınırı:</strong> Lastiğin diş derinliği yasal sınırı olan <strong>1.6 mm</strong> altına düşmüşse mutlaka değiştirilmelidir.</li>
                </ul>

                <h4>Basınç Değerleri ve Doğru Şişirme</h4>
                <ul>
                    <li>Lastik basınç seviyesi mutlaka <strong>lastik soğukken</strong> ve aracın yüklü ağırlığı göz önünde bulundurularak ölçülmelidir.</li>
                    <li>Basıncın gerekenden düşük veya yüksek olması yol tutuşunu, lastik performansını ve dayanıklılığını olumsuz etkiler.</li>
                    <li>Doğru basınç; yakıt tasarrufu, optimum yol tutuşu ve uzun kilometre ömrü sağlar.</li>
                </ul>

                <h4>Lastiğin Katmanları ve Yapısal Bölümleri</h4>
                <ol>
                    <li><strong>Desen / Taban:</strong> Yol ile temas eden alandır. Yola tutunmayı, kilometre performansını, ses seviyesini belirler ve su tahliyesi yaparak suda kızaklamayı önler.</li>
                    <li><strong>Kuşaklar:</strong> Yanal kuvvetleri dengeler, dönüşlerde hakimiyet sağlar. Tabanın stabil basmasını temin eder.</li>
                    <li><strong>Karkas:</strong> İçerideki basınçlı havayı tutar, esneyerek süspansiyon sağlar ve darbeleri emer.</li>
                    <li><strong>Topuk:</strong> Aracın ve yükün ağırlığını taşımaya yardımcı olur, lastiğin janta sabitlenmesini ve hava sızdırmazlığını sağlar.</li>
                    <li><strong>Omuz:</strong> Yanağı tabana bağlar, dönüşlerde stabilite sağlar.</li>
                    <li><strong>Yanak:</strong> Lastiğin yan taraflarına verilen isimdir.</li>
                </ol>

                <hr style="margin:20px 0; border:0; border-top:1px solid #e2e8f0;">

                <h3>3.2. Mekanik ve Elektronik Kontrol</h3>
                <p>Direksiyon başına geçmeden önce aracın etrafında bir tur atılarak gözle görülür kusurlar taranmalıdır.</p>

                <ul>
                    <li><strong>Far ve Sinyal Kontrolü:</strong> Kışın yanmayan farlar hem görüşü zorlaştırır hem de ceza sebebidir. Temiz farlar fark edilmeyi artırır.</li>
                    <li><strong>Silecek Kontrolü:</strong> Yağmurlu havalar için kritik öneme sahiptir, düzenli temizlenmelidir.</li>
                    <li><strong>Buji Kabloları:</strong> Kışın nem ve yağmur nedeniyle ıslanan buji kabloları risk oluşturur. Bujiler hafif gevşetilip etrafı kurulanmalıdır.</li>
                    <li><strong>Kalorifer Sistemi:</strong> Uzun süre çalışmayan kalorifer elektrik aksamında tıkanıklık ve sorunlara yol açabilir.</li>
                    <li><strong>Radyatör ve Antifriz Kontrolü:</strong> Antifriz, radyatör suyunun donmasını engeller. Antifrizin yetersiz olması kışın motorda ciddi ve pahalı hasarlara sebep olur. Sürekli kontrol edilmelidir.</li>
                </ul>
            </div>
        '
    ],
    4  => ["baslik" => "04 - YOLCU TAŞIMA KURALLARI", "ico" => "fa-users", "ozet" => "Yolcu bindirme/indirme esasları, bagaj kuralları, engelli yolcu erişimi ve yolcu güvenliği önlemleri.", "icerik" => ""],
    5  => ["baslik" => "05 - GÜVENLİ SÜRÜŞ TEKNİKLERİ", "ico" => "fa-shield-alt", "ozet" => "Defansif sürüş, takip mesafesi (2 saniye kuralı), zorlu hava şartlarında sürüş ve kör nokta yönetimi.", "icerik" => ""],
    6  => ["baslik" => "06 - YOLCU TAŞIMA MEVZUATI", "ico" => "fa-bus", "ozet" => "4925 Sayılı Karayolu Taşıma Kanunu, yetki belgeleri, taşıma sözleşmeleri ve uluslararası sözleşmeler.", "icerik" => ""],
    7  => ["baslik" => "07 - TRAFİK KURALLARI VE CEZALAR", "ico" => "fa-gavel", "ozet" => "Hız sınırları, geçiş üstünlükleri, ceza puanı sistemi, alkol kısıtlamaları ve güncel trafik cezaları.", "icerik" => ""],
    8  => ["baslik" => "08 - TRAFİK VE DAVRANIŞ PSİKOLOJİSİ", "ico" => "fa-brain", "ozet" => "Sürücü davranışı, stres yönetimi, öfke kontrolü, yorgunluk ve uykusuzluğun sürüşe etkileri.", "icerik" => ""],
    9  => ["baslik" => "09 - TRAFİK ADABI VE GÖRGÜ KURALLARI", "ico" => "fa-handshake", "ozet" => "Saygılı sürüş kültürü, empati, yol hakkı paylaşımı ve trafikte iletişim etiği.", "icerik" => ""],
    10 => ["baslik" => "10 - İLETİŞİM TEKNOLOJİLERİ VE HARİTA OKUMA", "ico" => "fa-map-marked-alt", "ozet" => "GPS kullanımı, dijital takograf sistemleri, harita okuma yön bilgisi ve iletişim araçları.", "icerik" => ""],
    11 => ["baslik" => "11 - GÜMRÜK - KAÇAKÇILIK VE TIR MEVZUATI", "ico" => "fa-passport", "ozet" => "TIR Karnesi, ATA Karnesi, gümrük kapıları geçiş prosedürleri ve Kaçakçılıkla Mücadele Kanunu.", "icerik" => ""],
    12 => ["baslik" => "12 - YASAL SORUMLULUKLAR VE SİGORTA", "ico" => "fa-file-contract", "ozet" => "Zorunlu Mali Sorumluluk Sigortası, Kasko, CMR Sigortası, taşıyıcı sorumlulukları ve lojistik.", "icerik" => ""],
    13 => ["baslik" => "13 - İLK YARDIM", "ico" => "fa-first-aid", "ozet" => "Kaza yeri güvenliği, temel yaşam desteği, kanamalarda müdahale, kırık-çıkık sabitleme.", "icerik" => ""],
    14 => ["baslik" => "14 - ARAÇ BİLGİSİ VE EKONOMİK SÜRÜŞ", "ico" => "fa-tachometer-alt", "ozet" => "Motor çalışma prensipleri, yeşil bantta sürüş, yakıt tasarrufu teknikleri ve düzenli bakım.", "icerik" => ""],
    15 => ["baslik" => "15 - MESLEKİ GELİŞİM DERSİ", "ico" => "fa-user-graduate", "ozet" => "Müşteri ilişkileri, imaj yönetimi, mesleki etik ve hizmet kalitesini artırma stratejileri.", "icerik" => ""]
];

$top_path = __DIR__ . '/_layout_top.php';
if (file_exists($top_path)) {
    include_once $top_path;
}
?>

<div style="margin-bottom: 15px;">
    <a href="/kursiyer/src-ders-notlari.php?tur=<?php echo urlencode($tur); ?>&grup=<?php echo urlencode($grup); ?>" style="text-decoration: none; color: #3182ce; font-size: 14px; font-weight: bold;">
        <i class="fas fa-arrow-left"></i> Tüm Ders Listesine Dön
    </a>
</div>

<h1 class="k-page-title">SRC Ders Notları (<?php echo htmlspecialchars($tur_baslik); ?>)</h1>

<?php if ($kID > 0 && isset($dersler[$kID])): ?>
    <?php $secili_ders = $dersler[$kID]; ?>
    <section class="k-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="color: #2b6cb0; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-top: 0;">
            <i class="fas <?php echo htmlspecialchars($secili_ders['ico']); ?>"></i> 
            <?php echo htmlspecialchars($secili_ders['baslik']); ?>
        </h2>
        
        <?php if (!empty($secili_ders['icerik'])): ?>
            <div style="line-height: 1.7; color: #2d3748; font-size: 15px;">
                <?php echo $secili_ders['icerik']; ?>
            </div>
        <?php else: ?>
            <p style="color: #718096; font-style: italic;">Bu derse ait notlar henüz eklenmemiştir.</p>
        <?php endif; ?>
    </section>
<?php else: ?>
    <section class="k-card">
        <div style="margin-bottom: 15px; font-weight: bold; color: #555;">
            <i class="fas fa-book-open"></i> Ders Notlarını Okuyabilirsiniz (Toplam <?php echo count($dersler); ?> Ders)
        </div>

        <div style="display:grid; gap:12px;">
            <?php foreach ($dersler as $kKey => $d): ?>
                <?php if (is_array($d)): ?>
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <strong style="font-size: 15px; color: #2d3748;">
                                <i class="fas <?php echo htmlspecialchars($d['ico'] ?? 'fa-book'); ?>" style="margin-right: 8px; color: #3182ce;"></i>
                                <?php echo htmlspecialchars($d['baslik'] ?? ''); ?>
                            </strong>
                            <a class="btn-link" href="/kursiyer/src-ders-notlari.php?tur=<?php echo urlencode($tur); ?>&grup=<?php echo urlencode($grup); ?>&kID=<?php echo $kKey; ?>" style="background: #3182ce; color: #fff; padding: 5px 12px; border-radius: 4px; text-decoration: none; font-size: 13px;">
                                Oku <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <p style="margin: 0; font-size: 13px; color: #718096; line-height: 1.5;">
                            <?php echo htmlspecialchars($d['ozet'] ?? ''); ?>
                        </p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php
$bottom_path = __DIR__ . '/_layout_bottom.php';
if (file_exists($bottom_path)) {
    include_once $bottom_path;
}
?>