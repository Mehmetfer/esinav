<?php
declare(strict_types=1);

/**
 * FARUK havuzu — 23.12.2017 MEB K kitapçığı ile uyumlu cevaplar (şık sırası kullanıcı metnine göre).
 * Prefiks: [FARUK]
 *
 * @return list<array{
 *   ders:string,soru:string,a:string,b:string,c:string,d:string,dogru:string,
 *   gorsel?:string,kaynak?:string
 * }>
 */
$bank = [];

$add = static function (
    string $ders,
    string $soru,
    string $a,
    string $b,
    string $c,
    string $d,
    string $dogru
) use (&$bank): void {
    if (!str_starts_with($soru, '[FARUK]')) {
        $soru = '[FARUK] ' . $soru;
    }
    $bank[] = [
        'ders' => $ders,
        'soru' => $soru,
        'a' => $a,
        'b' => $b,
        'c' => $c,
        'd' => $d,
        'dogru' => strtoupper($dogru),
        'kaynak' => 'faruk',
    ];
};

// —— İlk yardım ——
$add('ilkyardim',
    'Aşağıdakilerden hangisi ilk yardımın öncelikli amaçlarındandır?',
    'Trafikteki kaza sayısını azaltmak',
    'Sağlık personelinin mesleki başarısını artırmak',
    'İnsanları zararlı alışkanlıklarından uzaklaştırmak',
    'Yaşamsal fonksiyonların sürdürülmesini sağlamak',
    'D');

$add('ilkyardim',
    'Aşağıdakilerden hangisi organların çalışmasını, bilinç, algılama, anlama, hareketlerin birbiri ile uyum ve denge içinde olmasını sağlayan vb. işlevleri kontrol eden sistemi oluşturan yapılardandır?',
    'Omurilik',
    'Pankreas',
    'Böbrekler',
    'Akciğerler',
    'A');

$add('ilkyardim',
    "Kazazedenin dolaşımı değerlendirilirken;\nI. Bebeklerde kol atardamarından,\nII. Çocuk ve yetişkinlerde şah damarından nabız alınır.\nVerilenler için aşağıdakilerden hangisi söylenebilir?",
    'I. doğru, II. yanlış',
    'I. yanlış, II. doğru',
    'Her ikisi de doğru',
    'Her ikisi de yanlış',
    'C');

$add('ilkyardim',
    'Aşağıdakilerden hangisi solunum durmasının belirtilerinden biri değildir?',
    'Dudakların morarması',
    'Göz bebeklerinin küçülmesi',
    'Göğüs hareketlerinin kaybolması',
    'Nefes alma ve verme sesinin duyulamaması',
    'B');

$add('ilkyardim',
    'Yetişkinlere yapılan dış kalp masajı uygulamasıyla ilgili olarak verilenlerden hangisi doğrudur?',
    'Ellerin parmakları göğüs kafesiyle temas ettirilmeden, dirsekler bükülmeden ve göğüs kemiği üzerine vücuda dik olacak şekilde tutulması',
    'Göğüs kemiğinin alt ve üst ucunun tespit edilerek üst yarısına, orta ve yüzük parmağının dik olarak yerleştirilmesi',
    'Uygulama hızının dakikada 30 bası olacak şekilde ayarlanması',
    'Göğüs kemiği 3 cm aşağı inecek şekilde bası uygulanması',
    'A');

$add('ilkyardim',
    'Aşağıdakilerden hangisi, çok sayıda yaralının bulunduğu olay yerinde uzuv kopması olan bir kazazedeye turnike uygulayan ilk yardımcının dikkat etmesi gereken kurallardandır?',
    'Çift kemik bulunan bölgeye uygulaması',
    'Tel, lastik, ip gibi malzemeleri kullanması',
    'Uygulanan bölgenin üzerini sargı bezi ile kapatması',
    'Kazazedenin alnına “turnike” ya da “T” harfi yazması',
    'D');

$add('ilkyardim',
    'Aşağıdakilerden hangisi ciddi yaralanmalarda yapılması gereken ilk yardım uygulamalarındandır?',
    'Yara içinin kurcalanması',
    'Yarada kanama varsa durdurulması',
    'Yaranın üzerinin temiz pamukla kapatılması',
    'Yaraya saplanan yabancı cisimlerin çıkarılması',
    'B');

$add('ilkyardim',
    'Aşağıdakilerden hangisi burkulma belirtilerinden biri değildir?',
    'Şişlik',
    'Kızarma',
    'İşlev kaybı',
    'Hareket ile azalan ağrı',
    'D');

$add('ilkyardim',
    'Aşağıdakilerden hangisi kalp spazmında görülen ağrının özelliklerindendir?',
    'Nefes alıp vermekle şiddetinin değişmesi',
    'Genellikle göğüs ortasında başlaması',
    'Dinlenmekle geçmemesi',
    'Uzun süreli olması',
    'B');

$add('ilkyardim',
    "I. Sıkan giysiler gevşetilir.\nII. Sırtüstü yatırılarak ayakları 45 cm kaldırılır.\nIII. Kusma varsa mide içeriğini yutması için yarı oturur pozisyonda tutulur.\nIV. Solunum yolu açıklığı kontrol edilir ve açıklığın korunması sağlanır.\nYukarıdakilerden hangileri bayılmış olan bir kazazedeye yapılması gereken ilk yardım uygulamalarındandır?",
    'I ve IV.',
    'II ve III.',
    'I, III ve IV.',
    'I, II, III ve IV.',
    'A');

$add('ilkyardim',
    'Aşağıdakilerden hangisi ayak bileklerinden sürükleme yönteminde yapılmaması gereken uygulamalardandır?',
    'Kazazedenin baş, boyun ve gövde ekseni bozulmadan sürüklenmesi',
    'İlk yardımcının, kazazedenin ayak kısmına çömelmesi',
    'Kazazedenin ellerinin yanda serbest bırakılması',
    'Kazazedeye yakın mesafede durulması.',
    'C');

// —— Trafik ——
$add('trafik',
    'Aşağıdakilerden hangisi Kara Yolları Genel Müdürlüğünün görev ve yetkilerindendir?',
    'Belediye sınırları içindeki yollarda park düzeni, işaretleme, yaya ve okul geçitlerini belirlemek',
    'Kara yolları üzerinde ilk yardım istasyonları kurmak, bu istasyonlara gerekli personeli, araç ve gereci sağlamak',
    'Trafik kazalarının oluş nedenlerine göre verileri hazırlamak ve kara yollarında gerekli önleyici teknik tedbirleri almak veya aldırmak',
    'Motorlu araç sürücülerinin yetiştirilmesi için sürücü kursları açmak, özel sürücü kursu açılmasına izin vermek ve bunları her safhada denetlemek',
    'C');

$add('trafik',
    'Bir aracın güvenle taşıyabileceği en çok yük ağırlığına veya yolcu ve hizmetli sayısına ne denir?',
    'Gabari',
    'Taşıma sınırı',
    'Dingil ağırlığı',
    'Azami ağırlık',
    'B');

$add('trafik',
    'Kavşağa yaklaşan bir sürücü, trafik işaret ışığının aralıklarla kırmızı yanıp söndüğünü fark etmiştir. Bu durumda sürücü nasıl davranmalıdır?',
    'Durmalı, ilk geçiş hakkını kendisi kullanmalı',
    'Hızını sabit tutmalı, kontrollü bir şekilde geçmeli',
    'Hızını azaltmalı, kontrollü bir şekilde durmadan geçmeli',
    'Durmalı, ilk geçiş hakkını diğer yönden gelen araçlara vermeli',
    'D');

$add('trafik',
    'Şekildeki trafik işaretinin anlamı nedir?',
    'Hemzemin geçit',
    'Kontrollü demir yolu geçidi',
    'Kontrolsüz demir yolu geçidi',
    'Tramvay hattı ile oluşan kavşak',
    'D');

$add('trafik',
    'Şekildeki trafik işaretlerinin anlamları sırasıyla hangi seçenekte doğru olarak verilmiştir?',
    'I. Yol ver - II. Girişi olmayan yol',
    'I. Dur - II. Taşıt trafiğine kapalı yol',
    'I. Taşıt trafiğine kapalı yol - II. Girişi olmayan yol',
    'I. Azami hız sınırlaması - II. Bütün yasaklama ve kısıtlamaların sonu',
    'C');

$add('trafik',
    'Verilen şekle göre 2 numaralı aracın hangisini yapması doğrudur?',
    'Bisiklet yolunu kullanması',
    'Yayaları ikaz ederek bekletmesi',
    'Sol şeride geçip yoluna devam etmesi',
    'Bu bölgede azami 30 kilometre/saat hızla gitmesi',
    'D');

$add('trafik',
    'Şekildeki “park etme bilgi işaretine” göre hangi numaralı araçlar yanlış park etmiştir?',
    'Yalnız 3',
    '1 ve 2',
    '1 ve 3',
    '2 ve 3',
    'A');

$add('trafik',
    'Şekle göre 1 numaralı araç sürücüsünün aşağıdakilerden hangisini yapması yanlıştır?',
    'Hızını azaltması',
    'Öndeki aracı geçmesi',
    'Takip mesafesini artırması',
    'Duraklama yapmaktan kaçınması',
    'B');

$add('trafik',
    'Trafik kuralının ihlal edildiği tarihten geriye doğru bir yıl içinde, toplam 100 ceza puanını aştığı birinci defa tespit edilen sürücülerin sürücü belgeleri kaç ay süre ile geri alınır?',
    '2',
    '3',
    '6',
    '7',
    'A');

$add('trafik',
    'Sürücünün aşağıdakilerden hangisini yapması kural ihlali sayılır?',
    'Üç şeritli ve iki yönlü yollarda sağ şeritten gitmesi',
    'Aracın cinsine ve hızına uygun olan şeridi kullanması',
    'Geçme, dönme gibi mecburi hâller dışında şerit değiştirmesi',
    'Şerit değiştirmeden önce, gireceği şeritte sürülen araçların güvenle geçişlerini beklemesi',
    'C');

$add('trafik',
    'Arkadan çarpma şeklindeki trafik kazalarının en önemli sebebi aşağıdakilerden hangisidir?',
    'Takip mesafesi kuralına uyulmaması',
    'Görüş mesafesinin kötü olması',
    'Öndeki aracın durması',
    'Havanın yağışlı olması',
    'A');

$add('trafik',
    'Şekilde iki yönlü ve üç şeritli kara yolu bölümünde seyreden araçlar görülmektedir. Yol çizgilerine göre hangi numaralı araç sürücüleri hatalı sollama yapmaktadır?',
    'Yalnız 3',
    '1 ve 2',
    '2 ve 3',
    '1, 2 ve 3',
    'C');

$add('trafik',
    "Araç sürücülerinin duraklanan veya park edilen yerden çıkarken;\nI. Işıkla veya kolla çıkış işareti vermeleri,\nII. Araçlarını ve araçların etrafını kontrol etmeleri,\nIII. Yoldan geçen araçları ikaz ederek durdurmaları,\nIV. Görüş alanları dışında kalan yerler varsa gözcü bulundurmaları mecburidir.\nVerilen bilgilerden hangileri doğrudur?",
    'I ve III.',
    'I, II ve IV.',
    'II, III ve IV.',
    'I, II, III ve IV.',
    'B');

$add('trafik',
    'Şekildeki gibi bir kavşakta karşılaşan araçların geçiş hakkı sıralaması nasıl olmalıdır?',
    '1 - 2 - 3 - 4',
    '1 - 2 - 4 - 3',
    '2 - 1 - 4 - 3',
    '4 - 1 - 2 - 3',
    'A');

$add('trafik',
    'Aşağıdakilerden hangisi geçiş üstünlüğüne sahip araçların sürülmesine ilişkin esaslardan biri değildir?',
    'Bu araçlar, görev hâlinde iken geçiş üstünlüğü hakkına sahiptir.',
    'Bu hak, halkın can ve mal güvenliğini tehlikeye sokmamak, ışıklı ve sesli uyarı işaretlerini bir arada vermek şartı ile kullanılır.',
    'Emniyet ve asayiş işlerinde kullanılan, boyama şekilleri ve ayrım işareti bulunmayan araçlar anında sökülüp takılabilen ışıklı ihbar işareti bulundurmak zorundadır.',
    'Bu araçların görev hâli dışında geçiş üstünlüğü işaret ve hakkını kullanmaları serbesttir.',
    'D');

$add('trafik',
    "• Trafik işaretiyle yasaklanmış olan yerlerde\n• Belirlenmiş yangın musluklarına her iki yönden 5 metre mesafe içinde\n• Yerleşim yeri içinde kavşaklara ve bağlantı yollarına 5 metre mesafe içinde\nBelirtilen yerlerde aşağıdakilerden hangisi yasaktır?",
    'Durmak',
    'Duraklamak',
    'Hızı azaltmak',
    'Vites değiştirmek',
    'B');

$add('trafik',
    'Araç ışıklarının kullanılması kurallarına göre aşağıdakilerden hangisi doğrudur?',
    'Sadece park veya sis ışıkları yakılarak araç sürülmesi',
    'Gündüzleri görüşü azaltan sisli, yağışlı ve benzeri havalarda sadece sis ışıklarının kullanılması',
    'Geçme sırasında uyarı amacıyla uzağı ve yakını gösteren ışıkların çok kısa süre içinde sıra ile veya ikisinin birlikte aynı zamanda yakılması',
    'Karşı yönden gelen araç sürücülerinin ve kara yolunu kullanan diğer kişilerin gözlerini kamaştıracak bütün hâllerde, uzağı gösteren ışıkların yakılması',
    'C');

$add('trafik',
    'Kara Yolları Trafik Yönetmeliğine göre gerekli hâllerde kamyon, kamyonet, römork ve yarı römorklarla yolcu taşınabilir. Aşağıdakilerden hangisi bu araçlarla yolcu taşınabilmesi için yerine getirilmesi gereken şartlardan biri değildir?',
    'Kasa kapaklarının 70 cm yüksekliğinde olması',
    'Kasanın yan ve arka kapaklarının kapalı olması',
    'Yolcuların kasa içinde ayrılacak bir yerde oturtulması',
    'Yüklerin sağlam olarak yerleştirilmiş ve bağlanmış olması',
    'A');

$add('trafik',
    'Kaza anında araç dışına fırlama riskini azaltmak için araçlarda bulunan güvenlik sistemi aşağıdakilerden hangisidir?',
    'ASR',
    'ABS',
    'Emniyet kemeri',
    'Hava yastığı',
    'C');

$add('trafik',
    "I. Maddi hasar tespiti yapmak\nII. Olayı en yakın zabıta veya sağlık kuruluşuna bildirmek\nIII. Kaza yerinde usulüne uygun ilk yardım tedbirlerini almak\nIV. Yetkililerin isteği hâlinde yaralıları en yakın sağlık kuruluşuna götürmek\nKazaya karışan veya olay yerinden geçmekte olan kişiler yukarıdakilerden hangilerini yapmakla yükümlüdürler?",
    'Yalnız III',
    'I, II ve IV.',
    'I, III ve IV.',
    'II, III ve IV.',
    'D');

$add('trafik',
    'Kara Yolları Trafik Kanununa göre “M, A1, A2, A, B1, B, BE, F ve G” sınıfı sürücü belgeleri kaç yıl süreyle geçerlidir?',
    '10',
    '15',
    '20',
    '25',
    'A');

$add('trafik',
    'Aşağıdakilerden hangisi trafiğin çevreye verdiği zararları önlemeye yönelik davranışlardandır?',
    'Temiz olmayan yakıt kullanılması',
    'Kimyasal maddelerin ambalajlanarak taşınması',
    'Hususi araçların kullanılmasına gayret edilmesi',
    'Araç motorunun duraklama ve park etme sırasında çalışması',
    'B');

// —— Motor / araç ——
$add('arac',
    'Şekilde soru işareti (?) ile gösterilen sistemin görevi aşağıdakilerden hangisidir?',
    'Yakıt tüketimini azaltmak',
    'Aracın fren mesafesini kısaltmak',
    'Yoldan gelen darbelerin etkisini azaltmak',
    'Aracın daha çabuk hızlanmasını sağlamak',
    'C');

$add('arac',
    'Şekildeki araç güç aktarma organlarının adları hangi seçenekte doğru olarak verilmiştir?',
    'I. Şaft - II. Diferansiyel - III. Aks',
    'I. Şaft - II. Aks - III. Diferansiyel',
    'I. Aks - II. Diferansiyel - III. Şaft',
    'I. Diferansiyel - II. Aks - III. Şaft',
    'A');

$add('arac',
    'Marş yapıldığında gösterge ışıkları yanıyor ancak marş motoru dönmüyorsa problem aşağıdakilerden hangisi olabilir?',
    'Yakıt bitmiştir.',
    'Batarya zayıflamıştır.',
    'Lastik basınçları düşüktür.',
    'Motor yağ seviyesi azalmıştır.',
    'B');

$add('arac',
    'Marşa basıldığında motor dönüyor ancak çalışmıyorsa ilk olarak aşağıdakilerden hangisi kontrol edilmelidir?',
    'Bujiler',
    'Akü suyu',
    'Motor yağ seviyesi',
    'Depodaki yakıt seviyesi',
    'D');

$add('arac',
    'Periyodik bakımda aşağıdakilerden hangisinin değiştirilmemesi araç motorunun çalışmasını olumsuz etkiler?',
    'Polen filtresinin',
    'Yağ filtresinin',
    'Araç lastiklerinin',
    'Cam sileceklerinin',
    'B');

$add('arac',
    'Araçta yanmış bir sigortayı daha yüksek amperli bir sigortayla değiştirmek ya da telle sarmak aşağıdakilerden hangisine neden olabilir?',
    'Bujinin daha iyi ateşlemesine',
    'Farların daha canlı yanmasına',
    'Akünün daha çabuk bitmesine',
    'Elektrik tesisatının yanmasına',
    'D');

$add('arac',
    'Radyatördeki su miktarının azalması aşağıdakilerden hangisine neden olur?',
    'Motorun hararet yapmasına',
    'Motor devrinin yükselmesine',
    'Klimanın düzensiz çalışmasına',
    'Akünün kısa zamanda bitmesine',
    'A');

$add('arac',
    "I. Şarj\nII. ABS\nIII. Yağ basıncı\nVerilen ikaz lambalarından hangilerinin araç gösterge panelinde yanması aracın derhal durdurulmasını ve kontağın kapatılmasını gerektirir?",
    'Yalnız III',
    'I ve II.',
    'I ve III.',
    'II ve III.',
    'C');

$add('arac',
    "• Ani duruş ve hızlanmalardan kaçınılması\n• Tavsiye edilen tip ve ebatlarda araç lastiği kullanılması\n• Araçta yapılması gerekli bakım ve ayarların zamanında yapılması\nVerilenler sonucunda aşağıdakilerden hangisinin gerçekleşmesi beklenir?",
    'Çevre kirliliğinin artması',
    'Sürüş konforunun azalması',
    'Trafik yoğunluğunun artması',
    'Aracın daha az yakıt tüketmesi',
    'D');

// —— Trafik adabı ——
$add('adab',
    '- - - - ; trafik içinde sorumluluk, yardımlaşma, tahammül, saygı, fedakârlık, sabır vb. değerlere sahip olabilme yetisidir. Verilen ifadede boş bırakılan yere aşağıdakilerden hangisi yazılmalıdır?',
    'Beden dili',
    'Konuşma üslubu',
    'Trafik adabı',
    'Trafikte hak ihlali',
    'C');

$add('adab',
    'Aşağıdakilerden hangisi sorumluluk duygusuna sahip bir sürücünün özelliklerindendir?',
    'Trafik kurallarını önemsemeden araç kullanması',
    'Davranışlarının sonuçlarını düşünerek hareket etmesi',
    'Sevdiklerinin hayatını tehlikeye atmaktan çekinmemesi',
    'Kendi yetki alanına giren herhangi bir olayı başkalarının üstlenmesini beklemesi',
    'B');

$add('adab',
    'Birlikte yaşadığımız trafik ortamında, kişinin belki de farkında bile olmadan yaptığı olumsuz bir davranış hiçbir suçu olmayan bir başka kişinin ölümüne, yaralanmasına ya da ömür boyu sakat kalmasına neden olabilir. Buna göre trafik içinde hatalı davranış sergileyen bir sürücüye hangisinin yapılması, hem o sürücünün hem de trafikteki diğer sürücülerin kaza yapma ya da olumsuz bir durum oluşturma riskini azaltır?',
    'Aşırı tepki gösterilmesi',
    'Kaba ve saldırgan davranılması',
    'Kızgın biçimde kornaya basılması',
    'Nezaket ve saygı çerçevesinde uyarılması',
    'D');

$add('adab',
    'Aracını park ettikten sonra durduğu yerin diğer yol kullanıcıları açısından görme-görülme ya da manevra engeli oluşturup oluşturmadığını kontrol eden bir sürücünün bu davranışı trafikteki hangi değere uygundur?',
    'Empati',
    'Tahammül',
    'Beden dili',
    'Konuşma üslubu',
    'A');

$add('adab',
    'Aşağıdakilerden hangisi öfkenin vücutta ortaya çıkardığı fizyolojik tepkilerden biri değildir?',
    'Yüzün kızarması',
    'Kaşların çatılması',
    'Yumrukların sıkılması',
    'Kontrollü davranılması',
    'D');

$add('adab',
    "Trafik kazası geçiren kişiler:\nI. Canlarına bir zarar gelmese bile psikolojik olarak zarar görürler.\nII. Kişilerin bu bozuk psikolojileri ailelere ve topluma olumsuz yansır.\nVerilenler için aşağıdakilerden hangisi söylenebilir?",
    'I. doğru, II. yanlış',
    'I. yanlış, II. doğru',
    'Her ikisi de doğru',
    'Her ikisi de yanlış',
    'C');

// —— Ek FARUK (tedbirsiz sürüş, ışık, durma/park, takip vb.) ——
$add('adab',
    'Aşağıdakilerden hangisi tedbirsiz ve saygısız araç sürmeye örnek değildir?',
    'Hız kurallarına uyulması',
    'Yayalara su sıçratılması',
    'Diğer sürücülerin korkutulması',
    'Seyir hâlinde iken sürücünün elindeki cep telefonunu kullanması',
    'A');

$add('trafik',
    'Aşağıdakilerden hangisi, aracın yavaşlaması ve durması hâllerinde diğer araçları ikaz etmek amacıyla yanar?',
    'Sis lambaları',
    'Park lambaları',
    'Fren lambaları',
    'İç aydınlatma lambaları',
    'C');

$add('adab',
    "Trafikte güvenle seyahat etmek yüksek seviyede konsantrasyon gerektirir.\nBuna göre, direksiyon başında iken seyir emniyetinin tehlikeye düşmemesi için, aşağıdakilerden hangisinin yapılması doğru bir uygulama değildir?",
    'Elde cep telefonu ile konuşulması',
    'Temiz hava için araç camlarının kısa süreliğine açılması',
    'Yol şartlarına göre kontrol edilebilecek hızda araç kullanılması',
    'Trafik yoğunluğu düşük olan alternatif güzergâhların seçilmesi',
    'A');

$add('trafik',
    'Trafik zorunlulukları nedeniyle aracın durdurulmasına ne ad verilir?',
    'Park etme',
    'Durma',
    'Bekleme',
    'Duraklama',
    'B');

$add('trafik',
    'Sürücülerin aşağıdakilerden hangisini yapması yasaktır?',
    'İşaret vermeden şerit değiştirmesi',
    'Aracın cinsine ve hızına uygun şeritten gitmesi',
    'Gidişe ayrılan en sol şeridi geçiş amaçlı olarak kullanması',
    'Şerit değiştirmeden önce gireceği şeritteki araçların güvenli geçişlerini beklemesi',
    'A');

$add('trafik',
    '110 km/saat hızla seyreden bir aracın, önündeki aracı takip mesafesi en az kaç metre olmalıdır?',
    '65',
    '60',
    '55',
    '50',
    'C');

$add('trafik',
    'Taşıtlarla ilgili aşağıdaki hâllerden hangisi park etmeye örnektir?',
    '5 dakikadan az beklemek',
    'Yük yüklemek veya boşaltmak',
    '5 dakikadan fazla beklemek',
    'Yolun kapalı olması durumunda beklemek',
    'C');

$add('trafik',
    'Kara yollarında uzun süreli beklemeyi gerektiren duraklamalarda aşağıdakilerden hangisinin yapılması gerekir?',
    'Motorun durdurulup el freninin çekilmesi',
    'Motor çalışır halde farların yakılması',
    'Trafik görevlisine haber verilmesi',
    'Aracın başında gözcü bulundurulması',
    'A');

$add('trafik',
    'Araçlarda ilk yardım çantası bulundurulması konusunda aşağıdakilerden hangisi doğrudur?',
    'Sadece şehir içi taşımacılık yapan araçlarda zorunludur',
    'Sadece şehirlerarası taşımacılık yapan araçlarda zorunludur',
    'Motorlu araçlarda (motorlu bisiklet, motosiklet ve traktör hariç) zorunludur',
    'Sadece A1, A2 ve F sınıfı belge ile kullanılan araçlarda zorunludur',
    'C');

$add('trafik',
    'Aşağıdakilerden hangisi trafik kazasında sürücünün asli kusurlu sayılacağı durumlardan biri değildir?',
    'Kurallara uygun olarak park etmiş araçlara çarpmak',
    'Geçme yasağı olan yerlerden geçmek',
    'Kavşaklarda geçiş önceliğine uymak',
    'Arkadan çarpmak',
    'C');

$add('trafik',
    'Öndeki araç geçilirken, geçiş şeridinde ne kadar seyredilmelidir?',
    'Geçilen aracın boyunun yarısı kadar',
    'Geçilen aracın ön hizasına gelinceye kadar',
    'Karşıdan gelen araçla karşılaşıncaya kadar',
    'Geriyi görme aynasından geçilen araç görülünceye kadar',
    'D');

return $bank;
