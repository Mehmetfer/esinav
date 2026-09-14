<?php
declare(strict_types=1);

/**
 * Trafik işaret ve levhaları kataloğu.
 * Görseller: assets/img/signboards/ (+ tt/*.svg)
 */

/** @return array<int,string> */
function metro_isaret_kategoriler(): array
{
    return [
        1 => 'Tehlike Uyarı İşaretleri (T)',
        2 => 'Trafik Tanzim İşaretleri (TT)',
        3 => 'Bilgi İşaretleri (B)',
        4 => 'Durma ve Parketme İşaretleri (P)',
        5 => 'Yatay İşaretleme',
        6 => 'Yeni Standart Trafik İşaretleri',
    ];
}

/**
 * Üst bilgi blokları (eğitim metni).
 *
 * @return list<array{title:string,body:string,items?:list<string>}>
 */
function metro_isaret_egitim(): array
{
    return [
        [
            'title' => 'Trafik Levhaları Nedir?',
            'body' => 'Trafik levhaları, trafiğe çıkan her sürücünün mutlaka dikkat etmesi gereken uyarı işaretleridir. Trafik kurallarını ve gerekliliklerini uygulama açısından önemli bir yeri olan trafik işaretleri mutlaka izlenmelidir. Trafiğin akışı, yolun durumu ve birçok bilgiyi trafik işaret ve levhalarından öğrenebiliriz. Peki trafik işaretleri ve anlamlarını biliyor muyuz? Trafik levha işaretlerinin anlamları nedir? Trafikte güvenliği sağlamanın önemli koşullarından biri trafik uyarı işaretleridir. Bu işaret ve levhalar yalnızca sürücüleri değil, trafiğin ayrılmaz bir parçası olan yayaları da ilgilendirir.',
        ],
        [
            'title' => 'Trafik İşaretleri Kaç Gruba Ayrılır?',
            'body' => 'Trafik işaretleri toplamda 6 ayrı kategoriye ayrılır. Kimi trafik işaretleri tehlike ile ilgili bir uyarı verirken, kimi trafik levhaları da olası tehlikelerde önlem amaçlı bilgi verir.',
            'items' => [
                'Tehlike Uyarı İşaretleri',
                'Trafik Tanzim İşaretleri',
                'Bilgi İşaretleri',
                'Durma ve Park Etme İşaretleri',
                'Yatay İşaretleme',
                'Yeni Standart Trafik İşaretleri',
            ],
        ],
        [
            'title' => 'Tehlike Uyarı İşaretleri (T)',
            'body' => 'Yolda genellikle yolun durumu ile ilgili bilgi veren; dönüş, kavşak ve yol çalışması gibi bilgileri içeren trafik levhaları, Tehlike Uyarı İşaretleri başlığı altında toplanır. Bu trafik levhaları "T" harfi ile başlar.',
            'items' => [
                'Sağa Tehlikeli Viraj (T-1a): Yolun sağında tehlikeli bir virajın çıkacağını ve yavaş hareket edilmesi gerektiğini belirtir.',
                'Sola Tehlikeli Viraj (T-1b): Yolun solunda tehlikeli bir viraj ile karşılaşılacağını ve hızın azaltılması gerektiğini belirtir.',
                'Sağa Tehlikeli Virajlar (T-2a): Yolda sağa doğru birden fazla tehlikeli viraj olduğunu ve hızın azaltılması gerektiğini belirtir.',
                'Sola Tehlikeli Virajlar (T-2b): Yolda sola doğru birden fazla tehlikeli viraj olduğunu ve hızın azaltılması gerektiğini belirtir.',
                'Tehlikeli Eğim İniş (T-3a): Tehlikeli olabilecek eğimli inişleri ifade eder; bu levha ile karşılaşıldığında hızın azaltılması gerekir.',
                'Tehlikeli Eğim Çıkış (T-3b): Yolda rampa ya da tümsek çıkacağını ve bu nedenle hazırlıklı olunması gerektiğini belirtir.',
            ],
        ],
        [
            'title' => 'Trafik Tanzim İşaretleri (TT)',
            'body' => 'Yollarda hayati öneme sahip bir diğer trafik işaretleri grubudur. Öncelik gerektiren, yasak ya da kısıtlama bildiren ve mecburiyetleri anlatan tanzim işaretlerinden oluşur.',
            'items' => [
                'Yol Ver (TT-1): Yolların birleştiği kavşaklarda bulunur; ana yoldaki araçlara öncelik verilmesi gerektiğini belirtir.',
                'Dur (TT-2): Herhangi bir yola girmeden ve hamle yapmadan önce tüm araçlara yol verilmesi gerektiğini belirtir.',
                'Karşıdan Gelene Yol Ver (TT-3): Dar yollarda karşıdan gelen araçların önceliği olduğunu belirtir.',
            ],
        ],
        [
            'title' => 'Trafik Bilgi İşaretleri (B)',
            'body' => 'Yol güvenliği açısından önemli bir diğer trafik işaretleri grubudur. Bu trafik levhaları; yol boyunca şehirleri, yerleşim yerlerini ve yakın çevresinde bulunan hizmet birimleri hakkında bilgi verir. Yolun kenarlarında veya üst levhalarda bulunur ve kısaca "B" harfi ile kodlanır.',
        ],
        [
            'title' => 'Durma ve Park Etme İşaretleri (P)',
            'body' => 'Engelli alanlarına ve yasak park alanlarına park edilmemesi, mağduriyet yaratılmaması için dikkat edilmesi gereken işaretlerdir.',
        ],
        [
            'title' => 'Yatay İşaretleme',
            'body' => 'Yatay işaretleme levhaları zemin üzerinde bulunur ve araçlara yol, araç ve yol şeritleri ile ilgili bilgi verir.',
        ],
        [
            'title' => 'Yeni Standart Trafik İşaretleri',
            'body' => 'Yol kullanıcılarını; yani sürücü, yolcu ve yayaları, yoldaki olası tehlike veya bilgi verilmesi gereken hallerde uyarmak için kullanılan işaret levhalarına yeni standart trafik işaretleri denir. Diğer trafik işaret ve levhalarında olduğu gibi; sürücülerin daha dikkatli olmasını, seyir hızını düşürmesini ve belirtilen kısıtlama, yasak ve uyarılara uymasını gerektirdiğini bildirir.',
        ],
        [
            'title' => 'Trafik Levhaları Şekilleri Nasıldır?',
            'body' => 'Levhaların şekil ve renkleri, anlamlarını hızlı kavramanıza yardımcı olur.',
            'items' => [
                'Mavi trafik levhaları: Trafik tanzim grubunda bulunan, trafiğin akışı ve uyulması gereken kuralları bildiren işaretlerdir (mecburi yön vb.).',
                'Kare trafik levhaları: Genelde bilgi işaretleri grubunda yer alır; park yasağı gibi bilgilendirmeleri ifade eder.',
                'Üçgen trafik levhaları: Tehlike uyarı işaretlerinin çoğunluğu eşkenar üçgen biçimindedir.',
                'Yuvarlak trafik levhaları: Yasaklama ve kısıtlamaları bildirir.',
                'Kırmızı trafik levhaları: Dikkat artırıcıdır; en bilineni kırmızı sekizgen zemin üzerinde DUR yazılı levhadır.',
                'Ters üçgen trafik levhası: İçi boş ters üçgen — Yol Ver. Kavşaklarda yavaşlamayı, gerekirse durmayı emreder.',
            ],
        ],
    ];
}

/**
 * @return list<array{title:string,text:string}>
 */
function metro_isaret_sekiller(): array
{
    return [
        ['title' => 'Mavi', 'text' => 'Mecburi yön / tanzim kuralları'],
        ['title' => 'Kare', 'text' => 'Bilgi ve park düzeni'],
        ['title' => 'Üçgen', 'text' => 'Tehlike uyarısı'],
        ['title' => 'Yuvarlak', 'text' => 'Yasak / kısıtlama'],
        ['title' => 'Kırmızı', 'text' => 'Dur ve dikkat'],
        ['title' => 'Ters üçgen', 'text' => 'Yol ver'],
    ];
}

/**
 * @return list<array{cat:int,code?:string,title:string,file:string}>
 */
function metro_isaretler(): array
{
    // Gercek levha fotolari (tt-photos). Fotosu olmayan kodlar SVG kullanir.
    $foto = [
        'TT-1' => 'yol-ver-levhasi-tt-1-1024x683.png',
        'TT-2' => 'dur-levhasi-tt-2-1024x683.png',
        'TT-3' => 'karsidan-gelene-yol-ver-levhasi-tt-3-1024x683.png',
        'TT-4' => 'girisi-olmayan-yol-levhasi-tt-4-1024x683.png',
        'TT-5' => 'tasit-trafigine-kapali-yol-levhasi-tt-5-1024x683.png',
        'TT-6' => 'motorlu-tasit-trafigine-kapali-yol-levhasi-tt-6-1024x683.png',
        'TT-7' => 'motosiklet-giremez-levhasi-tt-7-1024x683.png',
        'TT-8' => 'bisiklet-giremez-levhasi-tt-8-1024x683.png',
        'TT-9' => 'mopet-giremez-levhasi-tt-9-1024x683.png',
        'TT-10a' => 'kamyon-giremez-levhasi-tt-10a-1024x683.png',
        'TT-10b' => 'otobus-giremez-levhasi-tt-10b-1024x683.png',
        'TT-11' => 'treyler-giremez-levhasi-tt-11-1024x683.png',
        'TT-13' => 'at-arabasi-giremez-levhasi-tt-13-1024x683.png',
        'TT-14' => 'el-arabasi-giremez-levhasi-tt-14-1024x683.png',
        'TT-15' => 'traktor-giremez-levhasi-tt-15-1024x683.png',
        'TT-16a' => 'parlayici-madde-tasiyan-tasit-giremez-levhasi-tt-16a-1024x683.png',
        'TT-16b' => 'tehlikeli-madde-tasiyan-tasit-giremez-levhasi-tt-16b-1024x683.png',
        'TT-17' => 'su-kirletici-madde-tasiyan-tasit-giremez-levhasi-tt-17-1024x683.png',
        'TT-18' => 'motorlu-tasit-giremez-levhasi-tt-18-1024x683.png',
        'TT-19' => 'tasit-giremez-levhasi-tt-19-1024x683.png',
        'TT-20' => 'genisligi-metreden-fazla-olan-tasit-giremez-levhasi-tt-20-1024x683.png',
        'TT-21' => 'yuksekligi-metreden-fazla-olan-tasit-giremez-levhasi-tt-21-1024x683.png',
        'TT-22' => 'uzunlugu-belirtilen-metreden-fazla-olan-tasit-giremez-levhasi-tt-22-1024x683.png',
        'TT-23' => 'dingil-basina-tondan-fazla-yuk-dusen-tasit-giremez-levhasi-tt-23-1024x683.png',
        'TT-24' => 'yuklu-agirligi-tondan-fazla-olan-tasit-giremez-levhasi-tt-24-1024x683.png',
        'TT-25' => 'ondeki-tasit-belirtilen-metreden-daha-yakin-takip-edilemez-levhasi-tt-25-1024x683.png',
        'TT-26a' => 'saga-donulemez-levhasi-tt-26a-1024x683.png',
        'TT-26b' => 'sola-donulemez-levhasi-tt-26b-1024x683.png',
        'TT-26c' => 'u-donusu-yapilamaz-levhasi-tt-26c-1024x683.png',
        'TT-27' => 'ondeki-tasiti-gecmek-yasaktir-levhasi-tt-27-1024x683.png',
        'TT-28' => 'kamyonlar-icin-ondeki-tasiti-gecmek-yasaktir-levhasi-tt-28-1024x683.png',
        'TT-29' => 'azami-hiz-sinirlamasi-30-levhasi-tt-29-1024x683.png',
        'TT-29b' => 'okul-bolgesi-azami-hiz-siniri-levhasi-tt-29b-1024x683.png',
        'TT-30' => 'sesli-ikaz-cihazlarinin-kullanilmasi-yasaktir-levhasi-tt-30-1024x683.png',
        'TT-31' => 'gumruk-durmadan-gecmek-yasak-levhasi-tt-31-1024x683.png',
        'TT-32' => 'butun-yasaklamalar-ve-kisitlamalarin-sonu-levhasi-tt-32-1024x683.png',
        'TT-33' => 'hiz-sinirlamasi-sonu-levhasi-tt-33-1024x683.png',
        'TT-34a' => 'gecme-yasagi-sonu-levhasi-tt-34a-1024x683.png',
        'TT-34b' => 'kamyonlar-icin-gecme-yasagi-sonu-levhasi-tt-34b-1024x683.png',
        'TT-35b' => 'sola-mecburi-yon-levhasi-tt-35b-1024x683.png',
        'TT-35d' => 'ileri-ve-saga-mecburi-yon-levhasi-tt-35d-1024x683.png',
        'TT-35e' => 'ileri-ve-sola-mecburi-yon-levhasi-tt-35e-1024x683.png',
        'TT-35f' => 'saga-ve-sola-mecburi-yon-levhasi-tt-35f-1024x683.png',
        'TT-35g' => 'ileriden-saga-mecburi-yon-levhasi-tt-35-1024x683.png',
        'TT-35h' => 'ileriden-sola-mecburi-yon-levhasi-tt-35h-1024x683.png',
        'TT-36a' => 'sagdan-gidiniz-levhasi-tt-36a-1024x683.png',
        'TT-36b' => 'soldan-gidiniz-levhasi-tt-36b-1024x683.png',
        'TT-36c' => 'her-iki-yandan-gidiniz-levhasi-tt-36c-1024x683.png',
        'TT-37' => 'ada-etrafinda-donunuz-levhasi-tt-37-1024x683.png',
        'TT-38a' => 'mecburi-bisiklet-yolu-levhasi-tt-38a-1024x683.png',
        'TT-38b' => 'mecburi-bisiklet-yolu-sonu-levhasi-tt-38b-1024x683.png',
        'TT-39a' => 'mecburi-yaya-yolu-levhasi-tt-39a-1024x683.png',
        'TT-39b' => 'mecburi-yaya-yolu-sonu-levhasi-tt-39b-1024x683.png',
        'TT-40a' => 'mecburi-atli-yolu-levhasi-tt-40a-1024x683.png',
        'TT-40b' => 'mecburi-atli-yolu-sonu-levhasi-tt-40b-1024x683.png',
        'TT-41a' => 'mecburi-asgari-hiz-levhasi-tt-41a-1024x683.png',
        'TT-41b' => 'mecburi-asgari-hiz-sonu-levhasi-tt-41b-1024x683.png',
        'TT-42a' => 'zincir-takmak-mecburidir-levhasi-tt-42a-1024x683.png',
        'TT-43a' => 'tehlikeli-madde-tasiyan-tasitlarin-izleyecekleri-mecburi-yon-levhasi-tt-43b-1024x683.png',
        'TT-44a' => 'yayalar-ve-bisikletliler-tarafindan-kullanilabilen-yol-levhasi-tt-44a-1024x683.png',
    ];
    $tt = static function (string $code, string $title) use ($foto): array {
        return [
            'cat' => 2,
            'code' => $code,
            'title' => $title . ' (' . $code . ')',
            'file' => isset($foto[$code]) ? 'tt-photos/' . $foto[$code] : 'tt/' . $code . '.svg',
        ];
    };

    $list = [
        // —— Trafik Tanzim (TT) ——
        $tt('TT-1', 'Yol ver'),
        $tt('TT-2', 'Dur'),
        $tt('TT-3', 'Karşıdan gelene yol ver'),
        $tt('TT-4', 'Girişi olmayan yol'),
        $tt('TT-5', 'Taşıt trafiğine kapalı yol'),
        $tt('TT-6', 'Motorlu taşıt trafiğine kapalı yol'),
        $tt('TT-7', 'Motosiklet giremez'),
        $tt('TT-8', 'Bisiklet giremez'),
        $tt('TT-9', 'Mopet giremez'),
        $tt('TT-10a', 'Kamyon giremez'),
        $tt('TT-10b', 'Otobüs giremez'),
        $tt('TT-11', 'Treyler giremez'),
        $tt('TT-13', 'At arabası giremez'),
        $tt('TT-14', 'El arabası giremez'),
        $tt('TT-15', 'Traktör giremez'),
        $tt('TT-16a', 'Parlayıcı madde taşıyan taşıt giremez'),
        $tt('TT-16b', 'Tehlikeli madde taşıyan taşıt giremez'),
        $tt('TT-17', 'Su kirletici madde taşıyan taşıt giremez'),
        $tt('TT-18', 'Motorlu taşıt giremez'),
        $tt('TT-19', 'Taşıt giremez'),
        $tt('TT-20', 'Genişliği belirtilen metreden fazla olan taşıt giremez'),
        $tt('TT-21', 'Yüksekliği belirtilen metreden fazla olan taşıt giremez'),
        $tt('TT-22', 'Uzunluğu belirtilen metreden fazla olan taşıt giremez'),
        $tt('TT-23', 'Dingil başına belirtilen tondan fazla yük düşen taşıt giremez'),
        $tt('TT-24', 'Yüklü ağırlığı belirtilen tondan fazla olan taşıt giremez'),
        $tt('TT-25', 'Öndeki taşıt belirtilen metreden daha yakın takip edilemez'),
        $tt('TT-26a', 'Sağa dönülemez'),
        $tt('TT-26b', 'Sola dönülemez'),
        $tt('TT-26c', 'U dönüşü yapılamaz'),
        $tt('TT-27', 'Öndeki taşıtı geçmek yasaktır'),
        $tt('TT-28', 'Kamyonlar için öndeki taşıtı geçmek yasaktır'),
        $tt('TT-29', 'Azami hız sınırlaması'),
        $tt('TT-29b', 'Okul bölgesi azami hız sınırı'),
        $tt('TT-30', 'Sesli ikaz cihazlarının kullanılması yasaktır'),
        $tt('TT-31', 'Gümrük (durmadan geçmek yasak)'),
        $tt('TT-32', 'Bütün yasaklamalar ve kısıtlamaların sonu'),
        $tt('TT-33', 'Hız sınırlaması sonu'),
        $tt('TT-34a', 'Geçme yasağı sonu'),
        $tt('TT-34b', 'Kamyonlar için geçme yasağı sonu'),
        $tt('TT-35a', 'Sağa mecburi yön'),
        $tt('TT-35b', 'Sola mecburi yön'),
        $tt('TT-35d', 'İleri ve sağa mecburi yön'),
        $tt('TT-35e', 'İleri ve sola mecburi yön'),
        $tt('TT-35f', 'Sağa ve sola mecburi yön'),
        $tt('TT-35g', 'İleriden sağa mecburi yön'),
        $tt('TT-35h', 'İleriden sola mecburi yön'),
        $tt('TT-36a', 'Sağdan gidiniz'),
        $tt('TT-36b', 'Soldan gidiniz'),
        $tt('TT-36c', 'Her iki yandan gidiniz'),
        $tt('TT-37', 'Ada etrafında dönünüz'),
        $tt('TT-38a', 'Mecburi bisiklet yolu'),
        $tt('TT-38b', 'Mecburi bisiklet yolu sonu'),
        $tt('TT-39a', 'Mecburi yaya yolu'),
        $tt('TT-39b', 'Mecburi yaya yolu sonu'),
        $tt('TT-40a', 'Mecburi atlı yolu'),
        $tt('TT-40b', 'Mecburi atlı yolu sonu'),
        $tt('TT-41a', 'Mecburi asgari hız'),
        $tt('TT-41b', 'Mecburi asgari hız sonu'),
        $tt('TT-42a', 'Zincir takmak mecburidir'),
        $tt('TT-43a', 'Tehlikeli madde taşıyan taşıtların izleyecekleri mecburi yön'),
        $tt('TT-44a', 'Yayalar ve bisikletliler tarafından kullanılabilen yol'),

        // —— Yatay işaretleme (mevcut görseller) ——
        ['cat' => 5, 'title' => 'Kesikli çizgi: öndeki araç geçilebilir', 'file' => '1756854280_2212_1712857447.png'],
        ['cat' => 5, 'title' => 'Devamlı çizgi: öndeki aracı geçmek yasaktır', 'file' => '1756854228_4528_1712857453.png'],
        ['cat' => 5, 'title' => 'Kesik ve devamlı çizgi', 'file' => '1756854003_6359_1712857458.png'],
        ['cat' => 5, 'title' => 'Tırmanma şeridi', 'file' => '1756853941_9767_1712857468.png'],
        ['cat' => 5, 'title' => 'İki devamlı çizgi', 'file' => '1756853857_7141_1712857463.png'],
        ['cat' => 5, 'title' => 'Katılma', 'file' => '1756853744_6572_1712857477.png'],
        ['cat' => 5, 'title' => 'Ayrılma', 'file' => '1756853613_2266_1712857473.png'],
        ['cat' => 5, 'title' => 'Bölünmüş yol başlangıcı', 'file' => '1756853569_4631_1751974395.jpg'],
        ['cat' => 5, 'title' => 'Taralı alana girilmez', 'file' => '1756853529_2625_yolresim10.jpg'],
        ['cat' => 5, 'title' => 'Yaya geçidi', 'file' => '1756853493_3756_yolresim37.jpg'],
        ['cat' => 5, 'title' => 'Yaya geçidi (alternatif)', 'file' => '1756853473_6904_yolresim36.jpg'],
        ['cat' => 5, 'title' => 'Yavaşlama şeridi', 'file' => '1756853239_1642_yolresim7.jpg'],
    ];

    return $list;
}
