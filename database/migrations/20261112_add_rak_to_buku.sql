-- Jalankan satu kali pada db_perpustakaan yang sudah memiliki tabel buku.
USE db_perpustakaan;

ALTER TABLE buku
    ADD COLUMN rak VARCHAR(50) NULL AFTER gambar;

UPDATE buku
SET rak = 'Blok C - Rak 01'
WHERE rak IS NULL OR rak = '';
