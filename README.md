# METRO Sürücü Kursu — Web Siteleri (güncel sunucu yedeği)

Sunucudan (155.103.70.66) 2026-09-14 tarihinde indirildi.

## Yapı

- `sinav/` → **sinav.metromtsk.xyz** (PHP e-sınav portalı, 13.5.2026 güncel)
  - `admin/` — yönetim paneli
  - `kursiyer/` — kursiyer paneli (e-sınav, videolar, kitaplar, SRC)
  - `includes/` — db/auth/esinav/src kütüphaneleri
  - `data/` — soru havuzu + video ders verileri (PHP)
  - `assets/` — css/js/img
  - `sql/schema.sql` — boş şema
  - `config.local.php.example` — kopyalayıp `config.local.php` yapın
- `site/` → **metromtsk.xyz** (tanıtım sitesi, statik index.html)
- `server/` → nginx vhost konfigürasyonları (referans)
- `db/` → `esinav.sql` — **şema + içerik** (soru/videolar/ayarlar; kursiyer + yönetici
  kayıtları KVKK nedeniyle bu repoda TUTULMAMALI — push öncesi `db/esinav.sql`
  içinden `kursiyerler`, `yoneticiler`, `kursiyer_aktivite`, `esinav_oturum`
  INSERT satırlarını silin)

## Kurulum (sinav)

1. `sinav/` içeriğini webroot'a kopyalayın
2. `sinav/config.local.php.example` → `sinav/config.local.php` (DB bilgilerini yazın)
3. `sinav/sql/schema.sql` + `db/esinav.sql` (temizlenmiş) içeriğini import edin
4. nginx: `server/sinav-metromtsk.conf` referans alın

## Notlar

- `.bak` / `.gsmbak` yedek dosyaları repoya alınmadı (sunucuda duruyor).
- `kursiyer/videos.json` (~800KB) video katalog önbelleğidir.
