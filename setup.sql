-- ============================================================
--  SETUP DATABASE — Desa Sungai Bakau Kecil
--  Jalankan file ini di phpMyAdmin atau via Laragon terminal
-- ============================================================

CREATE DATABASE IF NOT EXISTS desa_sungaibakaukecil
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE desa_sungaibakaukecil;

-- ─────────────────────────────────────────────
-- Tabel: users (Warga yang terdaftar)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    nik          VARCHAR(16)  NOT NULL UNIQUE,
    no_hp        VARCHAR(20)  NOT NULL,
    alamat       TEXT         NOT NULL,
    password     VARCHAR(255) NOT NULL,
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ─────────────────────────────────────────────
-- Tabel: admins (Petugas desa / admin)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admins (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    nama       VARCHAR(100) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('superadmin','admin') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ─────────────────────────────────────────────
-- Tabel: pengaduan_kesehatan
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS pengaduan_kesehatan (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id          INT UNSIGNED NOT NULL,
    nama_lengkap     VARCHAR(100) NOT NULL,
    nik              VARCHAR(16)  NOT NULL,
    no_hp            VARCHAR(20)  NOT NULL,
    kategori         VARCHAR(100) NOT NULL,
    alamat_kejadian  TEXT         NOT NULL,
    detail_gejala    TEXT         NOT NULL,
    status           ENUM('masuk','proses','selesai') NOT NULL DEFAULT 'masuk',
    catatan_admin    TEXT         NULL,
    created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ─────────────────────────────────────────────
-- Tabel: pengaduan_hukum
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS pengaduan_hukum (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED NOT NULL,
    nama_pemohon   VARCHAR(100) NOT NULL,
    nik_pemohon    VARCHAR(16)  NOT NULL,
    no_telp        VARCHAR(20)  NOT NULL,
    jenis_masalah  VARCHAR(100) NOT NULL,
    ringkasan      TEXT         NOT NULL,
    status         ENUM('masuk','proses','selesai') NOT NULL DEFAULT 'masuk',
    catatan_admin  TEXT         NULL,
    created_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ─────────────────────────────────────────────
-- Tabel: statistik_desa (Data angka beranda)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS statistik_desa (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kunci        VARCHAR(50)  NOT NULL UNIQUE,
    label        VARCHAR(100) NOT NULL,
    nilai        VARCHAR(20)  NOT NULL,
    target_angka INT UNSIGNED NOT NULL DEFAULT 0,
    updated_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ─────────────────────────────────────────────
-- SEED DATA
-- ─────────────────────────────────────────────

-- Admin default (password: admin123)
INSERT INTO admins (username, nama, password, role) VALUES
('admin', 'Administrator Desa', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin')
ON DUPLICATE KEY UPDATE username = username;

-- Statistik beranda default
INSERT INTO statistik_desa (kunci, label, nilai, target_angka) VALUES
('penduduk',     'Penduduk',             '3.847', 3847),
('kk',           'Kepala Keluarga',      '1.124', 1124),
('layanan_bulan','Keseluruhan Layanan',  '247',   247),
('kepuasan',     'Kepuasan Warga',       '98%',   98)
ON DUPLICATE KEY UPDATE kunci = kunci;


-- ─────────────────────────────────────────────
-- Tabel: banner_slides (Gambar slider beranda)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS banner_slides (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    posisi     TINYINT UNSIGNED NOT NULL UNIQUE COMMENT '1, 2, atau 3',
    filename   VARCHAR(255) NOT NULL COMMENT 'Nama file di assets/images/',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed banner default
INSERT INTO banner_slides (posisi, filename) VALUES
(1, 'bg3.png'),
(2, 'bg1.jpg'),
(3, 'bg2.png')
ON DUPLICATE KEY UPDATE posisi = posisi;
