-- Create Database
CREATE DATABASE IF NOT EXISTS `dashboard_pa2`;
USE `dashboard_pa2`;

-- ============================================
-- Table: Guru
-- ============================================
CREATE TABLE `guru` (
  `id_guru` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_guru` VARCHAR(100) NOT NULL,
  `no_hp` VARCHAR(15) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_guru`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Kelas
-- ============================================
CREATE TABLE `kelas` (
  `id_kelas` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_guru` BIGINT UNSIGNED NOT NULL,
  `nama_kelas` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_kelas`),
  CONSTRAINT `kelas_id_guru_foreign` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Siswa
-- ============================================
CREATE TABLE `siswa` (
  `nomor_induk_siswa` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_kelas` BIGINT UNSIGNED NOT NULL,
  `nama_orgtua` VARCHAR(150) NOT NULL,
  `tgl_lahir` DATE NOT NULL,
  `jenis_kelamin` ENUM('L', 'P') NOT NULL,
  `alamat` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`nomor_induk_siswa`),
  CONSTRAINT `siswa_id_kelas_foreign` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Akun
-- ============================================
CREATE TABLE `akun` (
  `id_akun` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_guru` BIGINT UNSIGNED NULL,
  `nomor_induk_siswa` BIGINT UNSIGNED NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'orangtua') NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_akun`),
  CONSTRAINT `akun_id_guru_foreign` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  CONSTRAINT `akun_nomor_induk_siswa_foreign` FOREIGN KEY (`nomor_induk_siswa`) REFERENCES `siswa` (`nomor_induk_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Pengumuman (Announcement)
-- ============================================
CREATE TABLE `pengumuman` (
  `id_pengumuman` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_guru` BIGINT UNSIGNED NOT NULL,
  `judul` VARCHAR(150) NOT NULL,
  `media` LONGTEXT DEFAULT NULL,
  `waktu_unggah` DATETIME NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_pengumuman`),
  CONSTRAINT `pengumuman_id_guru_foreign` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Perkembangan (Student Progress)
-- ============================================
CREATE TABLE `perkembangan` (
  `id_perkembangan` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_guru` BIGINT UNSIGNED NOT NULL,
  `nomor_induk_siswa` BIGINT UNSIGNED NOT NULL,
  `kategori` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_perkembangan`),
  CONSTRAINT `perkembangan_id_guru_foreign` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  CONSTRAINT `perkembangan_nomor_induk_siswa_foreign` FOREIGN KEY (`nomor_induk_siswa`) REFERENCES `siswa` (`nomor_induk_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Tagihan (Invoice)
-- ============================================
CREATE TABLE `tagihan` (
  `id_tagihan` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_induk_siswa` BIGINT UNSIGNED NOT NULL,
  `jumlah_tagihan` DECIMAL(10, 2) NOT NULL,
  `periode` VARCHAR(20) NOT NULL,
  `status` ENUM('belum_bayar', 'lunas') NOT NULL DEFAULT 'belum_bayar',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_tagihan`),
  CONSTRAINT `tagihan_nomor_induk_siswa_foreign` FOREIGN KEY (`nomor_induk_siswa`) REFERENCES `siswa` (`nomor_induk_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Pembayaran (Payment)
-- ============================================
CREATE TABLE `pembayaran` (
  `id_pembayaran` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_tagihan` BIGINT UNSIGNED NOT NULL,
  `jumlah_bayar` DECIMAL(10, 2) NOT NULL,
  `tgl_pembayaran` DATE NOT NULL,
  `status_bayar` ENUM('menunggu', 'diterima', 'ditolak') NOT NULL DEFAULT 'menunggu',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_pembayaran`),
  CONSTRAINT `pembayaran_id_tagihan_foreign` FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan` (`id_tagihan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Create Indexes
-- ============================================
CREATE INDEX `idx_guru_email` ON `guru`(`email`);
CREATE INDEX `idx_kelas_id_guru` ON `kelas`(`id_guru`);
CREATE INDEX `idx_siswa_id_kelas` ON `siswa`(`id_kelas`);
CREATE INDEX `idx_akun_username` ON `akun`(`username`);
CREATE INDEX `idx_pengumuman_id_guru` ON `pengumuman`(`id_guru`);
CREATE INDEX `idx_perkembangan_id_guru` ON `perkembangan`(`id_guru`);
CREATE INDEX `idx_perkembangan_nomor_induk_siswa` ON `perkembangan`(`nomor_induk_siswa`);
CREATE INDEX `idx_tagihan_nomor_induk_siswa` ON `tagihan`(`nomor_induk_siswa`);
CREATE INDEX `idx_pembayaran_id_tagihan` ON `pembayaran`(`id_tagihan`);
