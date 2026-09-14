<?php
declare(strict_types=1);

/**
 * DÖÇ — daha önce çıkmış (MEB/MTSK) sorular.
 * Prefiks: [DÖÇ] · isteğe bağlı gorsel: assets/img/sorular/doc/{slug}.png
 *
 * @return list<array{
 *   ders:string,soru:string,a:string,b:string,c:string,d:string,dogru:string,
 *   soru_ar?:string,a_ar?:string,b_ar?:string,c_ar?:string,d_ar?:string,
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
    string $dogru,
    ?string $soruAr = null,
    ?string $aAr = null,
    ?string $bAr = null,
    ?string $cAr = null,
    ?string $dAr = null,
    ?string $gorsel = null
) use (&$bank): void {
    if (!str_starts_with($soru, '[DÖÇ]')) {
        $soru = '[DÖÇ] ' . $soru;
    }
    if ($soruAr !== null && $soruAr !== '' && !str_starts_with($soruAr, '[DÖÇ]')) {
        $soruAr = '[DÖÇ] ' . $soruAr;
    }
    $row = [
        'ders' => $ders,
        'soru' => $soru,
        'a' => $a,
        'b' => $b,
        'c' => $c,
        'd' => $d,
        'dogru' => strtoupper($dogru),
        'kaynak' => 'doc',
    ];
    if ($soruAr) {
        $row['soru_ar'] = $soruAr;
        $row['a_ar'] = (string)$aAr;
        $row['b_ar'] = (string)$bAr;
        $row['c_ar'] = (string)$cAr;
        $row['d_ar'] = (string)$dAr;
    }
    if ($gorsel) {
        $row['gorsel'] = $gorsel;
    }
    $bank[] = $row;
};

// —— Metin soruları (WhatsApp ekran görüntüleri) ——

$add('ilkyardim',
    'Trafik kazasına tanık olan ilk yardımcının sesli ve ağrılı uyaranlara yanıt vermeyen, yutkunma ve öksürük gibi tepkileri olmayan yaralı için hangisini yapması yanlıştır?',
    'Sırtüstü yatırması',
    'Sıkan kıyafetlerini gevşetmesi',
    'Solunum yolu açıklığını sağlaması',
    'Kusması var ise Heimlich manevrası uygulaması',
    'D',
    'بالنسبة للمصاب الذي لا يستجيب للمنبهات الصوتية والمؤلمة ولا تظهر لديه ردود فعل مثل البلع أو السعال، أي مما يلي خطأ أن يفعله المسعف؟',
    'وضعه على ظهره',
    'فك الملابس الضيقة',
    'تأمين مجرى التنفس',
    'تطبيق مناورة هايمليخ إذا كان يتقيأ');

$add('trafik',
    "I. Trafik, ülkenin kaynaklarını, sanayi, ticaret alanlarını ve sosyo-ekonomik yapısını etkiler.\nII. Mühendislik ve sosyal bilimler, doğrudan ve dolaylı bir biçimde trafik kavramı içindedir.\nIII. Trafiğin üç temel unsuru; insan, araç ve çevredir.\nNumaralanmış ifadelerden hangileri doğrudur?",
    'I ve II', 'I ve III', 'II ve III', 'I, II ve III', 'D',
    "I. يؤثر المرور على موارد البلاد والصناعة والتجارة والبنية الاجتماعية الاقتصادية.\nII. الهندسة والعلوم الاجتماعية ضمن مفهوم المرور بشكل مباشر وغير مباشر.\nIII. عناصر المرور الثلاثة: الإنسان والمركبة والبيئة.\nأي العبارات صحيحة؟",
    'I و II', 'I و III', 'II و III', 'I و II و III');

$add('arac',
    "I. Yakıt sistemi\nII. Ateşleme sistemi\nIII. Soğutma sistemi\nMarşa basıldığında marş motoru dönüyor, araç motoru çalışmıyorsa verilen sistemlerden hangilerinde arıza olabilir?",
    'Yalnız I', 'I ve II', 'II ve III', 'I, II ve III', 'B',
    "I. نظام الوقود\nII. نظام الإشعال\nIII. نظام التبريد\nإذا دار محرك التشغيل عند الضغط على المفتاح ولم يعمل محرك المركبة، ففي أي الأنظمة قد يكون العطل؟",
    'I فقط', 'I و II', 'II و III', 'I و II و III');

$add('trafik',
    'Ticari amaçla yük taşımacılığı yapan ve azami ağırlığı 3,5 tonu geçen araç şoförlerinin, sürekli 4,5 saatlik araç kullanma süresi sonunda, eğer istirahata çekilmiyor ise en az kaç dakika mola alması mecburidir?',
    '20', '25', '35', '45', 'D',
    'ما الحد الأدنى لدقائق الاستراحة الإلزامية لسائقي مركبات نقل البضائع التجارية التي يزيد وزنها الأقصى عن 3.5 طن بعد قيادة متواصلة لمدة 4.5 ساعة إن لم يدخلوا راحة كاملة؟',
    '20', '25', '35', '45');

$add('adab',
    "Tablodaki eşleştirmelerden hangisi yanlıştır?\nI. Arabanı hızlı kullanıyorsun! → Sen dili\nII. Kaza yapacağız diye korktum! → Ben dili\nIII. Yanlış yoldan gidiyorsun! → Ben dili\nIV. Arabanı dikkatli kullanman sevindim! → Ben dili",
    'I', 'II', 'III', 'IV', 'C',
    "أي مطابقة في الجدول خاطئة؟\nI. تقود بسرعة! → لغة أنت\nII. خفت أن نتعرض لحادث! → لغة أنا\nIII. تسير في الطريق الخطأ! → لغة أنا\nIV. سررت لقيادتك بحذر! → لغة أنا",
    'I', 'II', 'III', 'IV',
    'doc/adab-ben-sen-dili-tablo.png');

$add('ilkyardim',
    'Çocuklara (1-8 yaş) yapılan dış kalp masajı uygulamalarından hangisi doğrudur?',
    'Tek elle göğüs kemiğinin alt yarısına bası',
    'İki elle göğüs kemiğine bası',
    'Karın bölgesine bası',
    'Köprücük kemiği yakınına bası',
    'A',
    'أي تطبيق لتدليك القلب الخارجي للأطفال (1–8 سنوات) صحيح؟',
    'ضغط بيد واحدة على النصف السفلي لعظم القص',
    'ضغط بيدتين على عظم القص',
    'ضغط على البطن',
    'ضغط قرب الترقوة',
    'doc/cocuk-kalp-masaji.png');

$add('ilkyardim',
    "I. Sinir uçlarının sonlandığı bölge etkilendiği için oldukça ağrılıdır.\nII. Derinin dış tabakasının bir bölümünün kaybı ile oluşan yaralanmalardır.\nIII. Bu tip yaralanmalarda kanama daha çok fışkırır tarzdadır.\nIV. İç organlarda meydana gelebilecek hasarlar hayati tehlike oluşturabilir.\nNumaralanmış özelliklerden hangileri \"sıyrık yaralar\" ile ilgili değildir?",
    'I ve II', 'I ve III', 'II ve IV', 'III ve IV', 'D',
    "I. مؤلمة جداً لأن نهايات الأعصاب تتأثر.\nII. إصابات بفقدان جزء من الطبقة الخارجية للجلد.\nIII. النزف في هذا النوع يكون عادة نافوراً.\nIV. أضرار الأعضاء الداخلية قد تشكل خطراً على الحياة.\nأي الخصائص لا تتعلق بجروح السحج؟",
    'I و II', 'I و III', 'II و IV', 'III و IV');

$add('ilkyardim',
    'Trafik kazası sonucu olay yerinde bulunan ilk yardımcı, yaralanan dört kazazedeye tespit işlemi uygulamıştır. İlk yardımcı, bu kazazedelerden hangisinin sağ omzunun çıktığını düşünmüştür?',
    'Sağ kolu askıya alınmış kazazede',
    'Sol kolu askıya alınmış kazazede',
    'Boyun ve omuz bölgesi sarılı kazazede',
    'Her iki omzu saran sekiz bandajlı kazazede',
    'A',
    'طبق المسعف تثبيتاً لأربعة مصابين في موقع الحادث. أي مصاب ظن أن كتفه الأيمن مخلوع؟',
    'المصاب الذي عُلّق ذراعه الأيمن بحمالة',
    'المصاب الذي عُلّق ذراعه الأيسر بحمالة',
    'المصاب الملفوف حول العنق والكتف',
    'المصاب بضماد على شكل 8 يحيط بالكتفين',
    'doc/omuz-cikigi-tespit.png');

$add('ilkyardim',
    'Nefes alabilen, öksüren ve tıkandığını ifade edebilen kazazedeye hangi ilk yardım uygulaması yapılmalıdır?',
    'Bacakları üzerine ata biner şekilde oturulmalı ve bir elin topuğuyla göbeğin üzerinden kürek kemiklerine doğru eğik bir baskı uygulanmalıdır.',
    'Bir elin topuk kısmıyla iki kürek kemiğinin arasına 5 kez kuvvetlice vurulmalıdır.',
    'Bir elin başparmağı midenin üst kısmına, göğüs kemiği altına gelecek şekilde yumruk yaparak konulmalı, kuvvetle arkaya ve yukarı doğru bastırılmalıdır.',
    'Dokunulmadan öksürmeye teşvik edilmelidir.',
    'D',
    'ما الإسعاف الواجب لمصاب يستطيع التنفس والسعال والتعبير عن انسداد؟',
    'الجلوس فوق ساقيه والضغط بميل من فوق السرة نحو لوحي الكتف',
    'الضرب بقوة 5 مرات بين لوحي الكتف بكعب اليد',
    'وضع قبضة تحت عظم القص والضغط بقوة للخلف وللأعلى',
    'تشجيعه على السعال دون لمسه');

$add('trafik',
    'Hız sınırlarını yüzde otuzdan fazla aşmak suretiyle suçunun işlendiği tarihten geriye doğru bir yıl içerisinde aynı kuralı beş defa ihlal ettiği tespit edilenlerin sürücü belgeleri kaç yıl süre ile geri alınır?',
    '1', '2', '3', '5', 'A',
    'لمن ثبتت مخالفته لتجاوز حد السرعة بأكثر من ثلاثين بالمئة خمس مرات خلال سنة واحدة من تاريخ المخالفة، كم سنة تُسحب رخصة قيادته؟',
    '1', '2', '3', '5');

$add('trafik',
    "I. Kar yağışlı havalarda\nII. Sisli havalarda\nIII. Toz bulutlarının yoğun olduğu havalarda\nNumaralanmış hava koşullarının hangilerinde araç farlarını yakmak gereklidir?",
    'I ve II', 'I ve III', 'II ve III', 'I, II ve III', 'D',
    "I. في الطقس الثلجي\nII. في الضباب\nIII. عند كثافة الغبار\nفي أي الظروف يجب تشغيل أضواء المركبة؟",
    'I و II', 'I و III', 'II و III', 'I و II و III');

$add('trafik',
    'Geçiş yolları üzerinde hangisinin yapılması yasaktır?',
    'Durmak', 'Park etmek', 'Yavaş gitmek', 'Vites küçültmek', 'B',
    'أي مما يلي ممنوع على طرق العبور/المداخل؟',
    'التوقف', 'الوقوف (الركن)', 'السير ببطء', 'تخفيض السرعة');

$add('ilkyardim',
    'Uzun süre egzersiz yaptıktan sonra trafiğe çıkan sürücüde korku, terleme, hızlı nabız, titreme, aniden acıkma, yorgunluk ve bulantı gibi belirtiler oluşmuştur. Bilinci yerinde olan ve kusması olmayan sürücünün yaşam bulguları değerlendirildikten sonra yapılması gereken ilk yardım uygulaması hangisidir?',
    'Koma pozisyonu verilir.',
    'Kusturulmaya çalışılır.',
    'Soğuk uygulama yapılır.',
    'Ağızdan şeker ve şekerli içecekler verilir.',
    'D',
    'ظهر على سائق بعد تمرين طويل خوف وتعرق ونبض سريع ورعشة وجوع مفاجئ وتعب وغثيان. وهو واعٍ ولا يتقيأ. بعد تقييم العلامات الحيوية ما الإسعاف المطلوب؟',
    'وضع الغيبوبة',
    'محاولة القيء',
    'تطبيق بارد',
    'إعطاء سكر ومشروبات سكرية عن طريق الفم');

$add('trafik',
    'Hangisi "park etme" kurallarındandır?',
    'Aracın kapılarını açık tutmak',
    '5 dakikayı geçmeyecek şekilde beklemek',
    'Park yerindeki araçların çıkışını engellememek',
    'Aracın anahtarını park görevlilerine teslim etmek',
    'C',
    'أي مما يلي من قواعد الوقوف؟',
    'إبقاء أبواب المركبة مفتوحة',
    'الانتظار بما لا يزيد عن 5 دقائق',
    'عدم عرقلة خروج المركبات في الموقف',
    'تسليم مفتاح المركبة لمسؤولي الموقف');

$add('arac',
    "I. Kabloların oksitlenmesi\nII. Marş motorunun arızalanması\nIII. Sigortanın atması\nKorna çalışmıyorsa, sebebi numaralanmış durumlardan hangileri olabilir?",
    'I ve II', 'I ve III', 'II ve III', 'I, II ve III', 'B',
    "I. تأكسد الكابلات\nII. عطل محرك التشغيل\nIII. احتراق الفيوز\nإذا لم يعمل البوق، أي الأسباب محتملة؟",
    'I و II', 'I و III', 'II و III', 'I و II و III');

// —— Görselli DÖÇ soruları ——

$add('ilkyardim',
    'İlk yardımın ABC’sine göre “A” (hava yolu açıklığı) değerlendirmesini gösteren görsel hangisidir?',
    'Baş-çene pozisyonu',
    'Bak-dinle-hisset',
    'Boyun nabzı kontrolü',
    'Bilinç kontrolü',
    'A',
    'حسب ABC للإسعاف، أي صورة تُظهر تقييم A (مجرى الهواء)؟',
    'وضعية الرأس والذقن',
    'انظر-اسمع-اشعر',
    'فحص نبض العنق',
    'فحص الوعي',
    'doc/ilkyardim-abc-a.png');

$add('ilkyardim',
    'Kanama resimlerinden hangisi atardamar kanamasına aittir?',
    'Kılcal sızıntı',
    'Damlayan kan',
    'Yüzeysel sızıntı',
    'Fışkırır tarzdaki parlak kırmızı kan',
    'D',
    'أي صورة للنزف تخص نزف الشريان؟',
    'تسرب شعري',
    'دم يتقاطر',
    'تسرب سطحي',
    'دم أحمر لامع يندفع نافوراً',
    'doc/atardamar-kanamasi.png');

$add('ilkyardim',
    'Olay yerinde ilk yardımcının yapabileceği uygulamalardan hangisi görselde doğru gösterilmiştir?',
    'Burun sürüntüsü almak',
    'Burun kanamasına müdahale',
    'Ambu ile suni solunum',
    'Boyun nabzı ölçümü',
    'B',
    'أي تطبيق يمكن للمسعف فعله في موقع الحادث كما في الصورة؟',
    'أخذ مسحة أنف',
    'معالجة نزيف الأنف',
    'تنفس صناعي بالأمبو',
    'قياس نبض العنق',
    'doc/burun-kanamasi-mudahale.png');

$add('trafik',
    'Kavşakta bulunulan yolun tali yol olduğunu ve ana yola çıkarken yol verilmesi gerektiğini belirten trafik işareti hangisidir?',
    'Ters üçgen (yol ver) levhası',
    'Karşıdan gelene yol ver',
    'Giriş yok',
    'Ana yol kavşağı uyarı işareti',
    'A',
    'أي إشارة تدل على أن الطريق فرعي ويجب إعطاء الأولوية عند الخروج إلى الطريق الرئيسي؟',
    'مثلث مقلوب (أعط الأولوية)',
    'أعط الأولوية للقادم من المقابل',
    'ممنوع الدخول',
    'تحذير تقاطع طريق رئيسي',
    'doc/trafik-tali-yol-isareti.png');

$add('trafik',
    'Görsele göre ileride sola dönecek sürücü hangi şeridi izlemelidir?',
    'İstediği şeridi',
    '1 numaralı şeridi',
    '2 numaralı şeridi',
    '3 numaralı şeridi',
    'B',
    'وفقاً للصورة، أي مسار يجب أن يتبعه السائق الذي سينعطف يساراً؟',
    'أي مسار يريد',
    'المسار رقم 1',
    'المسار رقم 2',
    'المسار رقم 3',
    'doc/trafik-sola-donus-seridi.png');

$add('trafik',
    'Görseldeki devamlı (kesintisiz) yol çizgisi sürücüye neyi bildirir?',
    'Karşı şeride geçilebilir',
    'Sağ şeritten gidilemez',
    'Hiçbir sebeple durulamaz',
    'Öndeki araçlar geçilemez / şerit değiştirilemez',
    'D',
    'ماذا يُخبر الخط المتصل في الصورة السائق؟',
    'يمكن تجاوز المسار المقابل',
    'لا يُسمح بالمسار الأيمن',
    'لا يُوقف لأي سبب',
    'لا يجوز تجاوز المركبات الأمامية / تغيير المسار',
    'doc/trafik-devamli-yol-cizgisi.png');

$add('trafik',
    'Görseldeki yatay trafik işaretlemesi otomobil sürücüsüne neyi bildirir?',
    'Kavşağa 50 m kaldığını',
    'Azami hızın 50 km/s olduğunu',
    '50 m sonra tehlikeli viraj olduğunu',
    'Öndeki aracın 50 m takip mesafesiyle izlenmesi gerektiğini',
    'D',
    'ماذا تُخبر العلامة الأفقية في الصورة سائق السيارة؟',
    'بقي 50 م للتقاطع',
    'الحد الأقصى 50 كم/س',
    'منعطف خطر بعد 50 م',
    'يجب متابعة المركبة الأمامية بمسافة 50 م',
    'doc/trafik-takip-mesafesi-isaret.png');

$add('trafik',
    'Videoya / görsele göre kırmızı otomobil sürücüsü hakkında hangisi kesinlikle söylenebilir?',
    'Hız limitini aştığı',
    'Takip mesafesini koruduğu',
    'Hatalı sollama / geçme yaptığı',
    'Yorgun veya uykusuz olduğu',
    'C',
    'وفقاً للفيديو/الصورة، أي عبارة عن سائق السيارة الحمراء مؤكدة؟',
    'تجاوز حد السرعة',
    'حافظ على مسافة المتابعة',
    'قام بتجاوز خاطئ',
    'كان متعباً أو ناعساً',
    'doc/trafik-kirmizi-otomobil-video.png');

return $bank;
