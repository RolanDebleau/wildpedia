-- ============================================================
-- WildPedia — Tambah Sistem Register & Login User (untuk Komentar)
-- Jalankan di phpMyAdmin: pilih database wildpedia, tab SQL, paste & GO
-- ============================================================

USE wildpedia;

-- -------------------------------------------------------
-- 1. Tabel users (akun pengunjung biasa, beda dari admin)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- 2. Tambah kolom user_id ke tabel comments
--    (nullable, supaya komentar lama yang masih pakai nama manual tidak rusak)
-- -------------------------------------------------------
ALTER TABLE comments
    ADD COLUMN user_id INT UNSIGNED NULL AFTER animal_id,
    ADD CONSTRAINT fk_comments_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    ADD INDEX idx_user (user_id);

-- -------------------------------------------------------
-- 3. Perbaikan: hash password admin default di schema.sql lama tidak valid
--    (tidak cocok dengan 'admin123' sehingga admin tidak bisa login).
--    Baris ini menimpanya dengan hash yang benar.
--    Aman dijalankan walau kamu sudah ganti password sendiri (ada AND di WHERE).
-- -------------------------------------------------------
UPDATE admins
SET password = '$2y$10$ZQHNFlr3R3fMdyew2ovJQ.DCJXowuAkVXnoTxCfJifOvZFptJ6fX2'
WHERE username = 'admin'
  AND password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

