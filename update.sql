-- ============================================================
-- WildPedia — Update image_url ke path lokal + Tabel Komentar + Admin
-- Jalankan di phpMyAdmin: pilih database wildpedia, tab SQL, paste & GO
-- ============================================================

USE wildpedia;

-- -------------------------------------------------------
-- 1. UPDATE image_url ke path lokal (folder: public/assets/animals/)
-- -------------------------------------------------------
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Harimau_Sumatera.jpg'        WHERE slug = 'harimau-sumatera';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Orangutan_Sumatra.webp'      WHERE slug = 'orangutan-sumatra';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Badak_Jawa.webp'             WHERE slug = 'badak-jawa';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Gajah_Sumatera.webp'         WHERE slug = 'gajah-sumatera';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Pesut_Mahakam.webp'          WHERE slug = 'pesut-mahakam';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Kura_kura_Leher_Ular_Roti.webp' WHERE slug = 'kura-kura-leher-ular-roti';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Komodo.webp'                 WHERE slug = 'komodo';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Elang_Jawa.webp'             WHERE slug = 'elang-jawa';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Tapir_Asia.webp'             WHERE slug = 'tapir-asia';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Hiu_Paus.webp'               WHERE slug = 'hiu-paus';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Bekantan.webp'               WHERE slug = 'bekantan';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Merak_Hijau.webp'            WHERE slug = 'merak-hijau';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Owa_Jawa.webp'               WHERE slug = 'owa-jawa';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Kucing_Bakau.jpg'            WHERE slug = 'kucing-bakau';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Anoa_Dataran_Rendah.jpg'     WHERE slug = 'anoa-dataran-rendah';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Lutung_Jawa.webp'            WHERE slug = 'lutung-jawa';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Maleo.jpg'                   WHERE slug = 'maleo';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Penyu_Belimbing.jpg'         WHERE slug = 'penyu-belimbing';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Kukang_Sumatera.jpg'         WHERE slug = 'kukang-sumatera';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Rangkong_Badak.webp'         WHERE slug = 'rangkong-badak';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Babi_Rusa.webp'              WHERE slug = 'babi-rusa';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Tarsius_Sulawesi.webp'       WHERE slug = 'tarsius-sulawesi';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Kepiting_Kenari.webp'        WHERE slug = 'kepiting-kenari';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Buaya_Muara.jpg'             WHERE slug = 'buaya-muara';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Cenderawasih_Kuning_Kecil.webp' WHERE slug = 'cenderawasih-kuning-kecil';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Kancil.jpg'                  WHERE slug = 'kancil';
UPDATE animals SET image_url = '/wildpedia/public/assets/animals/Monyet_Ekor_Panjang.webp'    WHERE slug = 'monyet-ekor-panjang';

-- -------------------------------------------------------
-- 2. Tabel komentar pengunjung
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS comments (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    animal_id  INT UNSIGNED NOT NULL,
    nama       VARCHAR(100) NOT NULL,
    isi        TEXT NOT NULL,
    approved   TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE,
    INDEX idx_animal (animal_id),
    INDEX idx_approved (approved)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- 3. Tabel admin
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin default: username=admin, password=admin123 (ganti setelah login pertama!)
INSERT IGNORE INTO admins (username, password)
VALUES ('admin', '$2y$10$ZQHNFlr3R3fMdyew2ovJQ.DCJXowuAkVXnoTxCfJifOvZFptJ6fX2');
-- password di atas adalah hash bcrypt dari: admin123
