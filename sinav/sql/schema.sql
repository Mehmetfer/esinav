-- METRO e-SINAV — u2759108_esinav
-- phpMyAdmin veya mysql istemcisi ile calistirin.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS yoneticiler (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tc_kimlik CHAR(11) NULL,
  telefon VARCHAR(20) NOT NULL,
  sifre_hash VARCHAR(255) NOT NULL,
  ad_soyad VARCHAR(120) NOT NULL,
  email VARCHAR(150) NULL,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_yonetici_tel (telefon)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

CREATE TABLE IF NOT EXISTS kursiyerler (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tc_kimlik CHAR(11) NULL,
  telefon VARCHAR(20) NOT NULL,
  sifre_hash VARCHAR(255) NOT NULL,
  ad VARCHAR(80) NOT NULL,
  soyad VARCHAR(80) NOT NULL,
  email VARCHAR(150) NULL,
  ehliyet_sinifi VARCHAR(10) NULL,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_kursiyer_tel (telefon)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

CREATE TABLE IF NOT EXISTS ayarlar (
  anahtar VARCHAR(64) NOT NULL PRIMARY KEY,
  deger TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

INSERT INTO ayarlar (anahtar, deger) VALUES
  ('kurs_adi', 'METRO SÜRÜCÜ KURSU'),
  ('site_baslik', 'METRO e-SINAV')
ON DUPLICATE KEY UPDATE deger = VALUES(deger);

-- Varsayilan yonetici: TC 10000000146 / sifre: Admin123!
-- (password_hash PHP PASSWORD_DEFAULT)
-- Varsayilan hesaplar (yonetici + demo kursiyer) install.php ile olusturulur.
-- Giris: telefon (GSM) numarasi + sifre.

SET FOREIGN_KEY_CHECKS = 1;

