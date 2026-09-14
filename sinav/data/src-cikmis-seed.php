<?php
declare(strict_types=1);

/**
 * SRC Çıkmış Sınav Soruları (ders = 'cikmis', kaynak = 'doc')
 * @return list<array{soru:string,a:string,b:string,c:string,d:string,dogru:string,aciklama:string}>
 */
return [
    [
        'soru' => '"DOT DB9Z 747R 4818" — Araç lastikleri üzerinde bulunan yukarıdaki tanıtıcı ifadelerden hangisi lastiğin üretim tarihi ile ilgili bilgi verir?',
        'a' => 'DOT',
        'b' => 'DB9Z',
        'c' => '747R',
        'd' => '4818',
        'dogru' => 'D',
        'aciklama' => "Lastik üzerindeki 4 haneli sayı üretim tarihini belirtir: ilk iki rakam üretim haftasını (48. hafta), son iki rakam üretim yılını (2018) gösterir.\n\nÖrnek lastik \"P 175/65 R 15 82 T\": 175 taban genişliği (mm), 65 yanak/taban oranı (%), R radyal yapı, 15 jant çapı (inç), 82 yük endeksi, T hız sınıfı. DOT üretim yeri/fabrika, DB9Z boyut kodu, 747R tip kodu, 4818 üretim tarihidir.",
    ],
    [
        'soru' => 'En tehlikeli ve hızlı müdahale edilmesi gereken kanama çeşidi aşağıdakilerden hangisidir?',
        'a' => 'Doğal deliklerde olan kanamalar',
        'b' => 'Toplardamar kanamaları',
        'c' => 'Kılcal damar kanamaları',
        'd' => 'Atardamar kanamaları',
        'dogru' => 'D',
        'aciklama' => "Vücutta kanayan damar tipine göre 3 çeşit kanama vardır: Atardamar, Toplardamar ve Kılcal damar kanaması.\n\nAtardamar Kanaması: Atardamarlar daha derin yerleşimli olduğundan derin kesi ve yaralanmalarda görülür. Kalbin pompalamasına bağlı olarak kalp atımıyla paralel, hızlı ve bol miktarda kanama olur. Kısa sürede çok miktarda kan kaybı gerçekleşebileceğinden oldukça tehlikelidir ve erken müdahale gerektirir. Oksijen yönünden zengin kan parlak kırmızıdır ve kanamanın durması zordur. Atardamar kanamaları kalp atımlarıyla uyumlu kesik kesik (fışkırarak) akar ve açık renklidir.",
    ],
    [
        'soru' => 'Aracın su dolu derin bir çukura girmesi nedeniyle, fren balatalarının ıslanması halinde frenin tutunma özelliğinin azaldığı durumlarda aşağıdakilerden hangisi yapılır?',
        'a' => 'Fren balatalarının kuruması için birkaç sefer kısa sürelerde hafifçe frene basılıp bırakılır',
        'b' => 'Fren balatalarının kuruması için beklenir',
        'c' => 'Fren balatalarının kuruması için hız artırılır',
        'd' => 'Fren balatalarının kuruması için hız azaltılır',
        'dogru' => 'A',
        'aciklama' => "Su dolu derin çukurlar fren balatalarının ıslanmasına ve frenin iyi tutmamasına neden olur. Derin sulardan sonra fren pedalına kısa süre hafifçe basılıp bırakılarak balataların kuruması sağlanır.\n\nYağmurda güvenli sürüş: görüş esastır; silecekler sağlam olmalı, kısa huzmeli farlar gündüz de yakılmalı, bol yağmurda hız 80 km altına düşürülmeli, su birikintilerinde direksiyon düz ve sıkı tutulmalı, takip mesafesi 3-4 saniyeye çıkarılmalıdır.",
    ],
];
