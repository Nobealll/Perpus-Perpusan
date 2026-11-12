-- Jalankan satu kali pada db_perpustakaan yang sudah terpasang.
-- Arsip anggota mempertahankan riwayat peminjaman lama.
USE db_perpustakaan;

ALTER TABLE anggota
    ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL AFTER password_hash;
