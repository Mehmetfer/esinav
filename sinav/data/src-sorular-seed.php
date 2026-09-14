<?php
declare(strict_types=1);

/**
 * SRC soru havuzu başlangıç verileri.
 * Takılan yer: src_sorular (ders, soru, secenek_a..d, dogru).
 * @return list<array{ders:string,soru:string,a:string,b:string,c:string,d:string,dogru:string}>
 */
return [
    // —— 01 İş Sağlığı ve İş Güvenliği ——
    ['ders' => 'is_sagligi', 'soru' => 'İş sağlığı ve güvenliği ile ilgili olarak işveren ile çalışan arasındaki ilişkiyi düzenleyen kanun hangisidir?', 'a' => '4857 Sayılı İş Kanunu ve 6331 Sayılı İSG Kanunu', 'b' => '4925 Sayılı Karayolu Taşıma Kanunu', 'c' => '2918 Sayılı Karayolları Trafik Kanunu', 'd' => '1593 Sayılı Umumi Hıfzıssıhha Kanunu', 'dogru' => 'A'],
    ['ders' => 'is_sagligi', 'soru' => 'Araçlarda bulunması zorunlu olan güvenlik ekipmanları hangileridir?', 'a' => 'Yangın söndürme tüpü ve ilk yardım çantası', 'b' => 'Buzdolabı ve televizyon', 'c' => 'Yalnızca yedek lastik', 'd' => 'Ses sistemi ve navigasyon', 'dogru' => 'A'],
    ['ders' => 'is_sagligi', 'soru' => 'Motor veya yakıt tankı yangınında aşağıdakilerden hangisi KULLANILMAMALIDIR?', 'a' => 'Kuru tozlu söndürücü', 'b' => 'Su', 'c' => 'Kum', 'd' => 'Toprak', 'dogru' => 'B'],
    ['ders' => 'is_sagligi', 'soru' => 'İşyerlerinde yılda kaç kez yangın tatbikatı yapılması zorunludur?', 'a' => '1', 'b' => '2', 'c' => '3', 'd' => '4', 'dogru' => 'A'],

    // —— 02 İş Organizasyonu ——
    ['ders' => 'is_organizasyon', 'soru' => 'Görev talimatını alan personel, aracı kullanıma hazır hale getirmeden önce öncelikle ne yapmalıdır?', 'a' => 'Araç personeli ile tanışıp aracın temizlik ve teknik donanımını kontrol etmelidir', 'b' => 'Hemen yola çıkmalıdır', 'c' => 'Yakıt deposunu doldurmalıdır', 'd' => 'Radyoyu açmalıdır', 'dogru' => 'A'],
    ['ders' => 'is_organizasyon', 'soru' => 'Aracın temizliği, yolcu memnuniyeti ve firma itibarı açısından nasıl bir öneme sahiptir?', 'a' => 'Doğrudan etkiler', 'b' => 'Hiçbir önemi yoktur', 'c' => 'Yalnızca görsel amaçlıdır', 'd' => 'Sadece yaz aylarında önemlidir', 'dogru' => 'A'],
    ['ders' => 'is_organizasyon', 'soru' => 'Mekik taşımalarda sürüş organizasyonu bakımından aşağıdakilerden hangisi doğrudur?', 'a' => 'İlk dönüş seferi ile son gidiş seferi boş gerçekleştirilir', 'b' => 'Tüm seferler dolu yapılır', 'c' => 'İlk gidiş seferi boş yapılır', 'd' => 'Tüm seferler boş yapılır', 'dogru' => 'A'],
    ['ders' => 'is_organizasyon', 'soru' => 'Araç içi gündüz loş veya gece kullanımında hangi donanımlar mutlaka kullanılmalıdır?', 'a' => 'Ön ve arka ışıklar', 'b' => 'Yalnızca park lambaları', 'c' => 'Yalnızca dörtlü flaşör', 'd' => 'Hiçbiri', 'dogru' => 'A'],

    // —— 03 Aracın Yolculuk Öncesi Sürüş Hazırlığı ——
    ['ders' => 'surus_hazirlik', 'soru' => 'Lastik diş derinliğinin yasal alt sınırı kaç mm\'dir?', 'a' => '1.6 mm', 'b' => '2.0 mm', 'c' => '2.5 mm', 'd' => '3.0 mm', 'dogru' => 'A'],
    ['ders' => 'surus_hazirlik', 'soru' => 'Lastik hava basıncı ne zaman ölçülmelidir?', 'a' => 'Lastik soğukken ve aracın yüklü ağırlığı dikkate alınarak', 'b' => 'Uzun yolculuktan hemen sonra', 'c' => 'Yalnızca yaz aylarında', 'd' => 'Hiç ölçülmesine gerek yoktur', 'dogru' => 'A'],
    ['ders' => 'surus_hazirlik', 'soru' => 'Aşağıdakilerden hangisi lastiğin yapısal bölümlerinden değildir?', 'a' => 'Desen / taban', 'b' => 'Kuşaklar', 'c' => 'Karbüratör', 'd' => 'Yanak', 'dogru' => 'C'],
    ['ders' => 'surus_hazirlik', 'soru' => 'Kış aylarında radyatör suyunun donmasını engelleyen madde hangisidir?', 'a' => 'Antifriz', 'b' => 'Motor yağı', 'c' => 'Hidrolik yağı', 'd' => 'Gres yağı', 'dogru' => 'A'],

    // —— 04 Yolcu Taşıma Kuralları ——
    ['ders' => 'yolcu_tasima', 'soru' => 'Yolcuların güvenli bir şekilde bindirilmesi ve indirilmesi sırasında araç ne durumda olmalıdır?', 'a' => 'Tamamen durmuş ve el freni çekilmiş olmalıdır', 'b' => 'Yavaş hareket halinde olmalıdır', 'c' => 'Motor çalışır vaziyette beklemelidir', 'd' => 'Farketmez', 'dogru' => 'A'],
    ['ders' => 'yolcu_tasima', 'soru' => 'Engelli yolcuların erişimi için araçta hangi düzenleme gereklidir?', 'a' => 'Uygun rampa veya asansör sistemi bulunmalıdır', 'b' => 'Hiçbir düzenleme gerekmez', 'c' => 'Yalnızca ek koltuk konulmalıdır', 'd' => 'Engelli yolcu taşınmamalıdır', 'dogru' => 'A'],
    ['ders' => 'yolcu_tasima', 'soru' => 'Yolcu bagajlarının yerleştirilmesinde öncelik hangisidir?', 'a' => 'Acil çıkışları ve koridorları kapatmayacak şekilde sabitlenmesi', 'b' => 'Koridorlara yığılması', 'c' => 'Acil çıkış önüne konulması', 'd' => 'Koltuk üstlerine serbest bırakılması', 'dogru' => 'A'],

    // —— 05 Güvenli Sürüş Teknikleri ——
    ['ders' => 'guvenli_surus', 'soru' => 'Defansif (savunmacı) sürüşün temel ilkesi nedir?', 'a' => 'Olası tehlikeleri önceden görüp önlem almaktır', 'b' => 'Hızlı araç kullanmaktır', 'c' => 'Trafik kurallarını ihlal etmektir', 'd' => 'Yakın takip yapmaktır', 'dogru' => 'A'],
    ['ders' => 'guvenli_surus', 'soru' => 'Takip mesafesi kuralı olarak "2 saniye kuralı" neyi ifade eder?', 'a' => 'Öndeki araçla aradaki güvenli takip mesafesini', 'b' => 'Kalkış için gereken süreyi', 'c' => 'Fren mesafesini', 'd' => 'Dinlenme süresini', 'dogru' => 'A'],
    ['ders' => 'guvenli_surus', 'soru' => 'Sürüş sırasında "kör nokta" nedir?', 'a' => 'Aynalarda görülemeyen alandır', 'b' => 'Yolun bozuk olduğu yerdir', 'c' => 'Işıkların çalışmadığı bölgedir', 'd' => 'Park alanıdır', 'dogru' => 'A'],
    ['ders' => 'guvenli_surus', 'soru' => 'Zorlu hava şartlarında (yağmur/karlı) sürüşte hangisi doğrudur?', 'a' => 'Takip mesafesi artırılmalı ve hız düşürülmelidir', 'b' => 'Hız artırılarak daha çabuk varılmalıdır', 'c' => 'Takip mesafesi azaltılmalıdır', 'd' => 'Sinyal kullanılmamalıdır', 'dogru' => 'A'],

    // —— 06 Yolcu Taşıma Mevzuatı ——
    ['ders' => 'mevzuat', 'soru' => 'Karayolu taşımacılığını düzenleyen temel kanun hangisidir?', 'a' => '4925 Sayılı Karayolu Taşıma Kanunu', 'b' => '2918 Sayılı Trafik Kanunu', 'c' => '4857 Sayılı İş Kanunu', 'd' => '2004 Sayılı İcra İflas Kanunu', 'dogru' => 'A'],
    ['ders' => 'mevzuat', 'soru' => 'Yolcu taşımacılığı yapan işletmelerin faaliyette bulunabilmesi için hangisi zorunludur?', 'a' => 'İlgili yetki belgesi (D1, D2 vb.)', 'b' => 'Yalnızca vergi levhası', 'c' => 'Hiçbir belge gerekmez', 'd' => 'Yalnızca sürücü belgesi', 'dogru' => 'A'],
    ['ders' => 'mevzuat', 'soru' => 'Taşıma sözleşmesi kavramı aşağıdakilerden hangisini ifade eder?', 'a' => 'Taşıyıcı ile yolcu/yük sahibi arasındaki taşıma anlaşmasını', 'b' => 'Sürücünün ehliyet belgesini', 'c' => 'Aracın ruhsatını', 'd' => 'Sigorta poliçesini', 'dogru' => 'A'],

    // —— 07 Trafik Kuralları ve Cezalar ——
    ['ders' => 'trafik_cezalar', 'soru' => 'Yerleşim yeri içinde otomobiller için hız sınırı genel olarak kaç km/saattir?', 'a' => '50', 'b' => '90', 'c' => '110', 'd' => '30', 'dogru' => 'A'],
    ['ders' => 'trafik_cezalar', 'soru' => 'Alkollü araç kullanımının tespiti hangi yöntemle yapılır?', 'a' => 'Alkometre ölçümü ve/veya kan testi', 'b' => 'Yalnızca göz kontrolü', 'c' => 'Sürücü beyanı', 'd' => 'Yalnızca plaka kontrolü', 'dogru' => 'A'],
    ['ders' => 'trafik_cezalar', 'soru' => 'Geçiş üstünlüğü olan araçlar hangileridir?', 'a' => 'Acil yardım, itfaiye, polis ve benzeri görev araçları', 'b' => 'Tüm ticari araçlar', 'c' => 'Tüm özel otomobiller', 'd' => 'Taksiler', 'dogru' => 'A'],
    ['ders' => 'trafik_cezalar', 'soru' => 'Ceza puanı sisteminde sürücü belgesi geçici olarak kaç puanda geri alınır?', 'a' => '100 ceza puanında', 'b' => '50 ceza puanında', 'c' => '20 ceza puanında', 'd' => 'Ceza puanı uygulaması yoktur', 'dogru' => 'A'],

    // —— 08 Trafik ve Davranış Psikolojisi ——
    ['ders' => 'psikoloji', 'soru' => 'Öfke kontrolü sürüş güvenliği açısından neden önemlidir?', 'a' => 'Saldırgan sürüş davranışını ve kaza riskini azaltır', 'b' => 'Hiçbir önemi yoktur', 'c' => 'Yakıt tüketimini artırır', 'd' => 'Yalnızca yolcuları ilgilendirir', 'dogru' => 'A'],
    ['ders' => 'psikoloji', 'soru' => 'Yorgun ve uykusuz sürücülerde hangi risk artar?', 'a' => 'Dikkat kaybı, reflekslerde yavaşlama ve kaza riski', 'b' => 'Hiçbir risk artmaz', 'c' => 'Yalnızca hız artar', 'd' => 'Yalnızca yakıt tüketimi artar', 'dogru' => 'A'],
    ['ders' => 'psikoloji', 'soru' => 'Stresli bir sürücünün trafikteki davranışı genellikle nasıl olur?', 'a' => 'Daha hatalı, gergin ve sabırsız olur', 'b' => 'Daha dikkatli olur', 'c' => 'Daha sakin olur', 'd' => 'Hiç değişmez', 'dogru' => 'A'],

    // —— 09 Trafik Adabı ve Görgü Kuralları ——
    ['ders' => 'trafik_adabi', 'soru' => 'Trafik adabının temelinde aşağıdakilerden hangisi yer alır?', 'a' => 'Saygı, empati ve hoşgörü', 'b' => 'Rekabet ve üstünlük', 'c' => 'Umursamazlık', 'd' => 'Hız', 'dogru' => 'A'],
    ['ders' => 'trafik_adabi', 'soru' => 'Kayıtsız şartsız yol verme davranışı hangi kavramla açıklanır?', 'a' => 'Yol hakkı paylaşımı ve empati', 'b' => 'Saldırganlık', 'c' => 'Kural ihlali', 'd' => 'Dikkatsizlik', 'dogru' => 'A'],
    ['ders' => 'trafik_adabi', 'soru' => 'Trafikte iletişim etiği açısından doğru davranış hangisidir?', 'a' => 'Korna kullanımında ölçülü ve gerekli durumlarda olmak', 'b' => 'Sürekli korna çalmak', 'c' => 'Selektörle rahatsız etmek', 'd' => 'Yakın takip yapmak', 'dogru' => 'A'],

    // —— 10 İletişim Teknolojileri ve Harita Okuma ——
    ['ders' => 'iletisim', 'soru' => 'Dijital takograf aşağıdakilerden hangisini kaydeder?', 'a' => 'Sürüş süresi, mola ve hız bilgilerini', 'b' => 'Yalnızca yakıt miktarını', 'c' => 'Yolcu sayısını', 'd' => 'Lastik basıncını', 'dogru' => 'A'],
    ['ders' => 'iletisim', 'soru' => 'GPS tabanlı navigasyonun temel amacı nedir?', 'a' => 'Güzergâh belirleme ve yönlendirme', 'b' => 'Yakıt üretimi', 'c' => 'Araç yıkama', 'd' => 'Lastik tamiri', 'dogru' => 'A'],
    ['ders' => 'iletisim', 'soru' => 'Harita okuma bilgisinde "ölçek" neyi ifade eder?', 'a' => 'Harita üzerindeki uzunluğun gerçek uzunluğa oranını', 'b' => 'Aracın hızını', 'c' => 'Yolun genişliğini', 'd' => 'Benzin fiyatını', 'dogru' => 'A'],

    // —— 11 Gümrük - Kaçakçılık ve TIR Mevzuatı ——
    ['ders' => 'gumruk', 'soru' => 'Uluslararası taşımacılıkta gümrük kapılarında kullanılan transit belge hangisidir?', 'a' => 'TIR Karnesi', 'b' => 'Ehliyet', 'c' => 'Ruhsat', 'd' => 'Pasaport', 'dogru' => 'A'],
    ['ders' => 'gumruk', 'soru' => 'Geçici ithalat rejimi kapsamında eşya için kullanılan belge hangisidir?', 'a' => 'ATA Karnesi', 'b' => 'TIR Karnesi', 'c' => 'CMR belgesi', 'd' => 'Gümrük beyannamesi', 'dogru' => 'A'],
    ['ders' => 'gumruk', 'soru' => 'Kaçakçılıkla mücadele hangi kanun kapsamında yürütülür?', 'a' => '5607 Sayılı Kaçakçılıkla Mücadele Kanunu', 'b' => '2918 Sayılı Trafik Kanunu', 'c' => '4857 Sayılı İş Kanunu', 'd' => '4925 Sayılı Taşıma Kanunu', 'dogru' => 'A'],

    // —— 12 Yasal Sorumluluklar ve Sigorta ——
    ['ders' => 'yasal', 'soru' => 'Trafiğe çıkan her aracın yaptırması zorunlu olan sigorta hangisidir?', 'a' => 'Zorunlu Mali Sorumluluk (Trafik) Sigortası', 'b' => 'Kasko', 'c' => 'Yangın Sigortası', 'd' => 'Hırsızlık Sigortası', 'dogru' => 'A'],
    ['ders' => 'yasal', 'soru' => 'Uluslararası karayolu yük taşımacılığında taşıyıcının sorumluluğunu düzenleyen konvansiyon hangisidir?', 'a' => 'CMR Konvansiyonu', 'b' => 'Montreal Konvansiyonu', 'c' => 'Hamburg Kuralları', 'd' => 'Viyana Konvansiyonu', 'dogru' => 'A'],
    ['ders' => 'yasal', 'soru' => 'Kasko sigortası neyi kapsar?', 'a' => 'Aracın kaza, çalınma gibi risklere karşı isteğe bağlı güvence altına alınmasını', 'b' => 'Yalnızca zorunlu trafik risklerini', 'c' => 'Sürücü sağlığını', 'd' => 'Yolcu biletini', 'dogru' => 'A'],
    ['ders' => 'yasal', 'soru' => 'Lojistik coğrafya bilgisi hangi unsuru kapsar?', 'a' => 'Taşıma güzergâhlarının coğrafi ve ekonomik analizi', 'b' => 'Yalnızca hava durumunu', 'c' => 'Yalnızca araç modelini', 'd' => 'Yalnızca yakıt fiyatını', 'dogru' => 'A'],

    // —— 13 İlk Yardım ——
    ['ders' => 'ilk_yardim', 'soru' => 'Kaza yerinde ilk yapılması gereken nedir?', 'a' => 'Çevre ve kaza yerinin güvenliğini sağlamak', 'b' => 'Yaralıyı hemen taşımak', 'c' => 'Araçları çalıştırmak', 'd' => 'Kalabalık toplamak', 'dogru' => 'A'],
    ['ders' => 'ilk_yardim', 'soru' => 'Yetişkin bir kişiye temel yaşam desteği (kalp masajı) uygulanırken göğüs basısı hızı dakikada kaç olmalıdır?', 'a' => '100-120', 'b' => '40-50', 'c' => '20-30', 'd' => '200', 'dogru' => 'A'],
    ['ders' => 'ilk_yardim', 'soru' => 'Ciddi bir kanamada ilk yardım olarak ne yapılmalıdır?', 'a' => 'Kanayan bölgeye temiz bezle direkt basınç uygulanmalıdır', 'b' => 'Yara bol suyla yıkanmalıdır', 'c' => 'Yaralıya su içirilmelidir', 'd' => 'Yara açıkta bırakılmalıdır', 'dogru' => 'A'],
    ['ders' => 'ilk_yardim', 'soru' => 'Kırık şüphesi olan bir uzuvda ilk yardımda ne yapılmalıdır?', 'a' => 'Uzuv tespit edilmeli ve hareket ettirilmemelidir', 'b' => 'Kuvvetlice düzeltilmelidir', 'c' => 'Masaj yapılmalıdır', 'd' => 'Sıcak uygulanmalıdır', 'dogru' => 'A'],

    // —— 14 Araç Bilgisi ve Ekonomik Araç Kullanma ——
    ['ders' => 'arac_bilgisi', 'soru' => 'Yakıt tasarrufu için motor hangi devir bandında kullanılmalıdır?', 'a' => 'Yeşil (ekonomik) devir bandında', 'b' => 'En yüksek devirde', 'c' => 'En düşük devirde sürekli', 'd' => 'Farketmez', 'dogru' => 'A'],
    ['ders' => 'arac_bilgisi', 'soru' => 'Aracın düzenli bakımı yakıt tüketimini nasıl etkiler?', 'a' => 'Azaltır', 'b' => 'Artırır', 'c' => 'Değiştirmez', 'd' => 'Yalnızca motorda değişiklik yapar', 'dogru' => 'A'],
    ['ders' => 'arac_bilgisi', 'soru' => 'Ani kalkış ve sert frenleme yakıt tüketimini nasıl etkiler?', 'a' => 'Artırır', 'b' => 'Azaltır', 'c' => 'Etkilemez', 'd' => 'Yalnızca lastikleri etkiler', 'dogru' => 'A'],
    ['ders' => 'arac_bilgisi', 'soru' => 'Motorun çalışma prensibi temel olarak neye dayanır?', 'a' => 'Yakıtın yanmasıyla oluşan enerjinin mekanik harekete dönüştürülmesine', 'b' => 'Yalnızca elektrik üretimine', 'c' => 'Yalnızca soğutma işlemine', 'd' => 'Yalnızca egzoz emisyonuna', 'dogru' => 'A'],

    // —— 15 Mesleki Gelişim ——
    ['ders' => 'meslek_gelisim', 'soru' => 'Müşteri ilişkileri yönetiminde en önemli unsur hangisidir?', 'a' => 'Müşteri memnuniyeti ve güven', 'b' => 'Yalnızca hız', 'c' => 'Yalnızca maliyet', 'd' => 'Yalnızca reklam', 'dogru' => 'A'],
    ['ders' => 'meslek_gelisim', 'soru' => 'Mesleki etik açısından bir sürücüden beklenen davranış hangisidir?', 'a' => 'Dürüst, güvenilir ve sorumluluk sahibi olmak', 'b' => 'Kuralları ihlal etmek', 'c' => 'Yolculara ilgisiz davranmak', 'd' => 'Sadece kendi çıkarını düşünmek', 'dogru' => 'A'],
    ['ders' => 'meslek_gelisim', 'soru' => 'Kurumsal imaj yönetimi aşağıdakilerden hangisini kapsar?', 'a' => 'Firmanın dışa yansıyan profesyonel görünümü ve itibarı', 'b' => 'Yalnızca araç sayısını', 'c' => 'Yalnızca çalışan sayısını', 'd' => 'Yalnızca reklam bütçesini', 'dogru' => 'A'],
];
