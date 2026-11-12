
-- ==========================================
-- DATABASE PERPUSTAKAAN MAHASISWA
-- ==========================================

CREATE DATABASE IF NOT EXISTS db_perpustakaan;

USE db_perpustakaan;


-- ==========================================
-- 1. TABEL ANGGOTA
-- ==========================================

CREATE TABLE anggota (
    id_anggota INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    alamat VARCHAR(150) NOT NULL,
    no_hp VARCHAR(15) NOT NULL,
    tanggal_daftar DATE NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    deleted_at DATETIME NULL DEFAULT NULL
);


-- ==========================================
-- 2. TABEL PETUGAS
-- ==========================================

CREATE TABLE petugas (
    id_petugas INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_petugas VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'petugas') DEFAULT 'petugas',
    no_hp VARCHAR(15) NOT NULL
);


-- ==========================================
-- 3. TABEL KATEGORI
-- ==========================================

CREATE TABLE kategori (
    id_kategori INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(80) NOT NULL UNIQUE,
    deskripsi TEXT NULL
);


-- ==========================================
-- 4. TABEL PENERBIT
-- ==========================================

CREATE TABLE penerbit (
    id_penerbit INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_penerbit VARCHAR(100) NOT NULL,
    alamat VARCHAR(150) NOT NULL,
    no_telepon VARCHAR(15) NOT NULL
);


-- ==========================================
-- 5. TABEL BUKU
-- ==========================================

CREATE TABLE buku (
    id_buku INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    deskripsi TEXT NULL,
    gambar VARCHAR(255) NULL,
    rak VARCHAR(50) NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    tahun_terbit YEAR NOT NULL,
    id_kategori INT UNSIGNED NOT NULL,
    id_penerbit INT UNSIGNED NOT NULL,
    stok INT UNSIGNED DEFAULT 0,

    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori),
    FOREIGN KEY (id_penerbit) REFERENCES penerbit(id_penerbit)
);


-- ==========================================
-- 6. TABEL PEMINJAMAN
-- ==========================================

CREATE TABLE peminjaman (
    id_peminjaman INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_anggota INT UNSIGNED NOT NULL,
    id_buku INT UNSIGNED NOT NULL,
    id_petugas INT UNSIGNED NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE NOT NULL,
    tanggal_pengembalian_aktual DATE NULL,
    denda DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',

    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota),
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku),
    FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas)
);


-- ==========================================
-- DATA ANGGOTA
-- Password semua akun: password
-- ==========================================
INSERT INTO anggota
(nama, alamat, no_hp, tanggal_daftar, username, password_hash)
VALUES
('Andi Pratama', 'Jakarta', '081234567801', '2025-01-10', 'andi', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky'),
('Budi Santoso', 'Jakarta', '081234567802', '2025-01-12', 'budi', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky'),
('Citra Lestari', 'Depok', '081234567803', '2025-01-15', 'citra', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky'),
('Dewi Anggraini', 'Bekasi', '081234567804', '2025-01-18', 'dewi', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky'),
('Eko Saputra', 'Jakarta', '081234567805', '2025-01-20', 'eko', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky'),
('Fitri Handayani', 'Tangerang', '081234567806', '2025-01-22', 'fitri', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky'),
('Gilang Ramadhan', 'Bogor', '081234567807', '2025-01-25', 'gilang', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky'),
('Hana Putri', 'Depok', '081234567808', '2025-01-27', 'hana', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky'),
('Irfan Maulana', 'Bekasi', '081234567809', '2025-02-01', 'irfan', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky'),
('Jihan Nabila', 'Jakarta', '081234567810', '2025-02-03', 'jihan', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky');


-- ==========================================
-- DATA PETUGAS
-- Password semua akun: password
-- ==========================================

INSERT INTO petugas
(nama_petugas, username, password_hash, role, no_hp)
VALUES
('Admin Perpustakaan', 'admin', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky', 'admin', '081298765401'),
('Petugas Perpustakaan', 'petugas', '$2y$10$5wVig5nDX8AtQ4mUiIemduUqIuSbdUe3fgWd8PBmlQkfCQSdki9Ky', 'petugas', '081298765402');


-- ==========================================
-- DATA KATEGORI
-- ==========================================

INSERT INTO kategori (nama_kategori, deskripsi)
VALUES
('Pemrograman', 'Buku pemrograman komputer.'),
('Basis Data', 'Buku pengelolaan database.'),
('Jaringan Komputer', 'Buku jaringan komputer.'),
('Sains', 'Buku ilmu pengetahuan.'),
('Novel dan Sastra', 'Kumpulan novel dan karya sastra.');


-- ==========================================
-- DATA PENERBIT
-- ==========================================

INSERT INTO penerbit (nama_penerbit, alamat, no_telepon)
VALUES
('Informatika', 'Bandung', '0227001001'),
('Elex Media Komputindo', 'Jakarta', '0217001002'),
('Andi Publisher', 'Yogyakarta', '02747001003'),
('Gramedia Pustaka Utama', 'Jakarta', '0217001004'),
('Penerbit Erlangga', 'Jakarta', '0217001005');


-- ==========================================
-- DATA BUKU TERE LIYE
-- ==========================================

INSERT INTO buku
(judul, penulis, deskripsi, gambar, rak, isbn, tahun_terbit, id_kategori, id_penerbit, stok)
VALUES
('Bumi', 'Tere Liye', 'Novel fantasi tentang petualangan dan dunia paralel.', NULL, 'Blok C - Rak 01', '9786020332956', 2014, 5, 4, 5),
('Bulan', 'Tere Liye', 'Novel fantasi tentang perjalanan dan persahabatan.', NULL, 'Blok C - Rak 01', '9786020332949', 2015, 5, 4, 4),
('Matahari', 'Tere Liye', 'Novel fantasi tentang penjelajahan dunia yang penuh misteri.', NULL, 'Blok C - Rak 01', '9786020332963', 2016, 5, 4, 4),
('Bintang', 'Tere Liye', 'Novel fantasi tentang petualangan menghadapi tantangan baru.', NULL, 'Blok C - Rak 01', '9786020332970', 2017, 5, 4, 3),
('Ceros dan Batozar', 'Tere Liye', 'Novel fantasi tentang petualangan di dunia paralel.', NULL, 'Blok C - Rak 01', '9786020385915', 2018, 5, 4, 3),
('Komet', 'Tere Liye', 'Novel fantasi tentang perjalanan menuju tempat yang belum dikenal.', NULL, 'Blok C - Rak 01', '9786020385939', 2018, 5, 4, 4),
('Komet Minor', 'Tere Liye', 'Novel fantasi tentang kelanjutan petualangan di dunia paralel.', NULL, 'Blok C - Rak 01', '9786020623392', 2019, 5, 4, 3),
('Selena', 'Tere Liye', 'Novel fantasi yang mengisahkan perjalanan seorang tokoh muda.', NULL, 'Blok C - Rak 01', '9786020633170', 2020, 5, 4, 4),
('Nebula', 'Tere Liye', 'Novel fantasi tentang persahabatan dan petualangan lintas dunia.', NULL, 'Blok C - Rak 01', '9786020647826', 2020, 5, 4, 3),
('Si Putih', 'Tere Liye', 'Novel fantasi tentang seekor kucing dan petualangan yang dijalaninya.', NULL, 'Blok C - Rak 01', '9786239554595', 2021, 5, 4, 3);


-- ==========================================
-- DATA PEMINJAMAN
-- ==========================================

INSERT INTO peminjaman
(id_anggota, id_buku, id_petugas, tanggal_pinjam, tanggal_kembali, tanggal_pengembalian_aktual, denda, status)
VALUES
(1, 1, 1, '2025-02-01', '2025-02-08', '2025-02-07', 0, 'dikembalikan'),
(2, 2, 2, '2025-02-03', '2025-02-10', '2025-02-12', 10000, 'dikembalikan'),
(3, 3, 1, '2025-02-05', '2025-02-12', '2025-02-12', 0, 'dikembalikan'),
(4, 4, 2, '2025-02-07', '2025-02-14', '2025-02-16', 10000, 'dikembalikan'),
(5, 5, 1, '2025-02-09', '2025-02-16', '2025-02-15', 0, 'dikembalikan'),
(6, 6, 2, '2025-02-11', '2025-02-18', '2025-02-20', 10000, 'dikembalikan'),
(7, 7, 1, '2025-02-13', '2025-02-20', NULL, 0, 'dipinjam'),
(8, 8, 2, '2025-02-15', '2025-02-22', NULL, 0, 'dipinjam'),
(9, 9, 1, '2025-02-17', '2025-02-24', '2025-02-25', 5000, 'dikembalikan'),
(10, 10, 2, '2025-02-19', '2025-02-26', NULL, 0, 'dipinjam');
