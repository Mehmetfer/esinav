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
            'title' => 'Trafik Levhaları Kaça Ayrılır?',
            'body' => 'Trafik işaretleri ve levhaları toplamda 6 farklı gruba ayrılmıştır.',
            'items' => [
                'Tehlike Uyarı İşaretleri (T grubu)',
                'Trafik Tanzim İşaretleri (TT grubu)',
                'Bilgi İşaretleri (B grubu)',
                'Durma ve Parketme İşaretleri (P grubu)',
                'Yatay İşaretleme',
                'Yeni Standart Trafik İşaretleri',
            ],
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
    $tt = static function (string $code, string $title): array {
        return [
            'cat' => 2,
            'code' => $code,
            'title' => $title . ' (' . $code . ')',
            'file' => 'tt/' . $code . '.svg',
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
