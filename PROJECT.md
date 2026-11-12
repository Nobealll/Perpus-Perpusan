Baca dan pahami seluruh instruksi pengembangan aplikasi yang terdapat dalam file PROJECT.md.

Gunakan file tersebut sebagai pedoman utama selama pengerjaan project.

Saya ingin mengembangkan aplikasi secara bertahap. Jangan langsung membuat seluruh aplikasi sekaligus.

Mulai dengan:
1. Menganalisis struktur project.
2. Memeriksa kebutuhan database.
3. Membuat file SQL beserta tabel, relasi, dan data dummy.

Setelah tahap pertama selesai, jelaskan cara menjalankan dan menguji database sebelum melanjutkan ke tahap berikutnya.

Pastikan setiap kode yang dibuat mengikuti struktur folder dan teknologi yang telah ditentukan dalam PROJECT.md.

# MASTER PROMPT: PEMBUATAN APLIKASI WEB PERPUSTAKAAN MAHASISWA

## 1. PERAN DAN TUGAS

Bertindaklah sebagai seorang Web Developer yang berpengalaman dalam pengembangan aplikasi web menggunakan PHP Native, MySQL, HTML, CSS, dan JavaScript.

Saya ingin membuat sebuah aplikasi berbasis web sederhana dengan tema **Sistem Informasi Perpustakaan Mahasiswa**.

Tugasmu adalah membantu saya membangun aplikasi ini dari awal sampai selesai, mulai dari perancangan database, struktur folder, pembuatan backend, frontend, koneksi database, hingga aplikasi dapat dijalankan secara lokal.

**PENTING:**
- Buat aplikasi yang sederhana, mudah dipahami, dan tidak berlebihan.
- Jangan menambahkan fitur yang tidak diperlukan.
- Jangan menggunakan framework PHP seperti Laravel atau CodeIgniter.
- Gunakan PHP Native dengan konsep OOP.
- Jangan membuat arsitektur yang terlalu kompleks.
- Prioritaskan aplikasi yang benar-benar berfungsi daripada sekadar tampilan.
- Pastikan seluruh kode saling terhubung dan dapat dijalankan.

## 2. TEKNOLOGI YANG DIGUNAKAN

Gunakan teknologi berikut:

| Bagian | Teknologi |
|---|---|
| Backend | PHP Native |
| Konsep pemrograman | OOP |
| Frontend | HTML5 |
| Styling | CSS3 + Tailwind CSS |
| JavaScript | Vanilla JavaScript |
| Database | MySQL |
| Database Connection | PDO |
| Local Server | Laragon |
| Web Server | Apache |
| Database Management | phpMyAdmin |

Gunakan PDO untuk menghubungkan PHP dengan MySQL.

## 3. STRUKTUR FOLDER PROJECT

Ikuti struktur folder berikut. Jangan memindahkan file PHP ke folder frontend atau sebaliknya.

```text
perpustakaan/
│
├── backend/
│   ├── config/
│   │   └── Database.php
│   │
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── AnggotaController.php
│   │   ├── BukuController.php
│   │   ├── PeminjamanController.php
│   │   └── PengembalianController.php
│   │
│   └── models/
│       ├── Anggota.php
│       ├── Petugas.php
│       ├── Buku.php
│       ├── Kategori.php
│       ├── Penerbit.php
│       └── Peminjaman.php
│
├── frontend/
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css
│   │   │
│   │   └── js/
│   │       └── script.js
│   │
│   ├── components/
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   ├── navbar.php
│   │   └── footer.php
│   │
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── anggota.php
│   ├── buku.php
│   ├── kategori.php
│   ├── penerbit.php
│   ├── peminjaman.php
│   ├── pengembalian.php
│   └── profile.php
│
└── database/
    └── db_perputstakaan.sql
```

Ketentuan struktur:
- Seluruh file PHP yang menangani proses backend disimpan dalam folder backend.
- Seluruh file tampilan HTML, CSS, dan JavaScript disimpan dalam folder frontend.
- File SQL disimpan dalam folder database.
- File PHP pada frontend hanya bertugas menampilkan halaman dan menghubungkan tampilan dengan controller.
- Gunakan include atau require untuk komponen yang digunakan berulang kali.

Jika diperlukan penyesuaian struktur folder, lakukan seminimal mungkin dan jelaskan alasannya.

## 4. PERANCANGAN DATABASE MYSQL

Nama database:

`db_perputstakaan`

Database harus mengikuti rancangan ERD yang saya berikan, dengan tabel utama sebagai berikut:

### A. Tabel anggota

Menyimpan informasi mahasiswa yang menjadi anggota perpustakaan.

Atribut:
- id_anggota (PK, AUTO_INCREMENT)
- nama (VARCHAR 100)
- alamat (VARCHAR 150)
- no_hp (VARCHAR 15)
- tanggal_daftar (DATE)

Tambahkan atribut autentikasi apabila memang diperlukan untuk menghubungkan akun mahasiswa.

### B. Tabel petugas

Menyimpan informasi petugas perpustakaan.

Atribut:
- id_petugas (PK, AUTO_INCREMENT)
- nama_petugas (VARCHAR 100)
- username (VARCHAR 50, UNIQUE)
- password_hash (VARCHAR 255)
- role (ENUM: admin, petugas)
- no_hp (VARCHAR 15)

### C. Tabel buku

Menyimpan informasi buku yang tersedia di perpustakaan.

Atribut:
- id_buku (PK, AUTO_INCREMENT)
- judul (VARCHAR 150)
- penulis (VARCHAR 100)
- isbn (VARCHAR 20)
- tahun_terbit (YEAR)
- id_kategori (FK)
- id_penerbit (FK)

Tambahkan atribut stok untuk mengetahui jumlah buku yang tersedia.

### D. Tabel kategori

Menyimpan kategori buku.

Atribut:
- id_kategori (PK, AUTO_INCREMENT)
- nama_kategori (VARCHAR 80)
- deskripsi (TEXT)

### E. Tabel penerbit

Menyimpan informasi penerbit buku.

Atribut:
- id_penerbit (PK, AUTO_INCREMENT)
- nama_penerbit (VARCHAR 100)
- alamat (VARCHAR 150)
- no_telepon (VARCHAR 15)

### F. Tabel peminjaman

Menyimpan transaksi peminjaman dan pengembalian buku.

Atribut:
- id_peminjaman (PK, AUTO_INCREMENT)
- id_anggota (FK)
- id_buku (FK)
- id_petugas (FK)
- tanggal_pinjam (DATE)
- tanggal_kembali (DATE)
- tanggal_pengembalian_aktual (DATE, NULL)
- denda (DECIMAL 10,2)
- status (ENUM: dipinjam, dikembalikan)

Keterangan:
- tanggal_kembali merupakan tanggal batas pengembalian atau jatuh tempo.
- tanggal_pengembalian_aktual merupakan tanggal buku benar-benar dikembalikan.
- denda menyimpan nominal denda keterlambatan.
- status menunjukkan apakah buku masih dipinjam atau sudah dikembalikan.

### G. Relasi antar tabel

Buat relasi menggunakan foreign key:

1. anggota → peminjaman (1:N)
2. petugas → peminjaman (1:N)
3. buku → peminjaman (1:N)
4. kategori → buku (1:N)
5. penerbit → buku (1:N)

Gunakan InnoDB agar foreign key dapat berjalan.

### H. Data dummy

Masukkan minimal 10 data percobaan.

Usahakan data dummy tersedia untuk seluruh tabel, misalnya:
- 10 anggota.
- 10 buku.
- 5 kategori.
- 5 penerbit.
- 2 petugas.
- 10 transaksi peminjaman.

Pastikan seluruh foreign key memiliki data yang valid.

Buat satu file SQL lengkap yang berisi:
1. CREATE DATABASE.
2. USE DATABASE.
3. CREATE TABLE.
4. PRIMARY KEY.
5. FOREIGN KEY.
6. UNIQUE.
7. INSERT INTO untuk data dummy.

File harus bisa langsung di-import melalui phpMyAdmin.

## 5. FITUR APLIKASI

Buat fitur berikut saja. Jangan menambahkan fitur yang terlalu kompleks.

### A. Halaman utama

Halaman utama berisi:
- Nama aplikasi Perpustakaan Mahasiswa.
- Deskripsi singkat aplikasi.
- Informasi layanan perpustakaan.
- Tombol login.
- Tombol registrasi.
- Navigasi yang sederhana.

### B. Login dan registrasi

Aplikasi memiliki dua jenis pengguna:

**1. Mahasiswa**
- Registrasi akun.
- Login.
- Melihat daftar buku.
- Melihat informasi buku.
- Melakukan peminjaman buku.
- Melihat riwayat peminjaman.
- Melihat status pengembalian.
- Melihat denda apabila terlambat mengembalikan buku.

**2. Petugas**
- Login menggunakan akun petugas.
- Melihat dashboard.
- Mengelola anggota.
- Mengelola buku.
- Mengelola kategori.
- Mengelola penerbit.
- Melihat seluruh transaksi peminjaman.
- Memproses pengembalian buku.
- Melihat informasi denda.

Registrasi digunakan untuk membuat akun mahasiswa. Akun petugas dapat disediakan melalui data awal database.

Gunakan session PHP untuk mempertahankan status login.

Setelah login:
- Mahasiswa diarahkan ke dashboard mahasiswa.
- Petugas diarahkan ke dashboard petugas.

Tambahkan validasi username agar tidak boleh sama dengan akun yang sudah terdaftar.

Gunakan password_hash() dan password_verify() untuk pengelolaan password. Password harus memiliki validasi yang layak, tetapi tidak perlu dipaksakan unik karena password bukan identitas pengguna.

### C. Dashboard

Buat dashboard yang berbeda untuk mahasiswa dan petugas.

**Dashboard petugas:**
- Total anggota.
- Total buku.
- Total kategori.
- Total peminjaman.
- Jumlah buku yang sedang dipinjam.
- Jumlah transaksi terlambat.

**Dashboard mahasiswa:**
- Total buku yang tersedia.
- Buku yang sedang dipinjam.
- Riwayat peminjaman.
- Informasi denda jika ada.

Gunakan card sederhana untuk menampilkan informasi tersebut.

### D. CRUD anggota

Petugas dapat:
- Menambahkan anggota.
- Melihat daftar anggota.
- Mengedit anggota.
- Menghapus anggota.

Gunakan tabel yang rapi dan form sederhana.

### E. CRUD buku

Petugas dapat:
- Menambahkan buku.
- Melihat daftar buku.
- Mengedit buku.
- Menghapus buku.

Form buku harus memiliki pilihan kategori dan penerbit dari database menggunakan dropdown.

### F. CRUD kategori dan penerbit

Petugas dapat melakukan CRUD sederhana untuk kategori dan penerbit.

### G. Transaksi peminjaman

Buat fitur transaksi peminjaman yang benar-benar berinteraksi dengan beberapa tabel.

Alur peminjaman:

1. Mahasiswa memilih buku yang ingin dipinjam.
2. Sistem memeriksa ketersediaan stok.
3. Mahasiswa mengajukan peminjaman.
4. Sistem menyimpan transaksi ke tabel peminjaman.
5. Sistem mengurangi stok buku sebanyak satu.
6. Sistem menetapkan tanggal pinjam.
7. Sistem menentukan tanggal jatuh tempo pengembalian.
8. Petugas dapat melihat transaksi tersebut.

Gunakan tanggal pinjam dan tanggal jatuh tempo dengan durasi peminjaman sederhana, misalnya 7 hari.

Pastikan satu transaksi peminjaman tidak dapat diproses jika stok buku habis.

### H. Transaksi pengembalian dan denda

Buat fitur pengembalian buku.

Alur pengembalian:

1. Petugas membuka daftar buku yang sedang dipinjam.
2. Petugas memilih transaksi yang akan dikembalikan.
3. Sistem mengambil tanggal jatuh tempo.
4. Sistem membandingkan tanggal jatuh tempo dengan tanggal pengembalian aktual.
5. Sistem menghitung denda jika terjadi keterlambatan.
6. Sistem mengubah status transaksi menjadi dikembalikan.
7. Sistem menyimpan tanggal pengembalian aktual.
8. Sistem menambahkan kembali stok buku sebanyak satu.

**Aturan denda:**

Denda sebesar Rp5.000 per hari keterlambatan.

Rumus:

Denda = Jumlah Hari Keterlambatan × Rp5.000

Contoh:
- Dikembalikan tepat waktu = Rp0.
- Terlambat 1 hari = Rp5.000.
- Terlambat 2 hari = Rp10.000.
- Terlambat 3 hari = Rp15.000.
- Terlambat 5 hari = Rp25.000.

Gunakan perhitungan tanggal dari PHP agar hasil denda otomatis.

Tampilkan informasi:
- Nama mahasiswa.
- Judul buku.
- Tanggal pinjam.
- Tanggal jatuh tempo.
- Tanggal pengembalian aktual.
- Jumlah hari keterlambatan.
- Total denda.

Tidak perlu membuat sistem pembayaran online. Cukup tampilkan nominal denda yang harus dibayar.

## 6. DESAIN UI/UX: NEOBRUTALISM

Saya ingin seluruh tampilan aplikasi menggunakan gaya desain **Neobrutalism**.

Referensi desain:
- Bold typography.
- Border hitam yang tebal.
- Shadow keras tanpa blur.
- Warna cerah dan kontras.
- Card dengan sudut sedikit membulat.
- Tombol dengan border tebal dan efek pressed.
- Layout sederhana dan tegas.

Gunakan palet warna berikut:

- Background utama: #FFFDF5
- Primary: #FFD84D
- Secondary: #A7D8FF
- Accent: #FF8B8B
- Success: #B7F7C1
- Text: #171717
- Border: #171717

Karakteristik desain:
1. Gunakan border hitam sekitar 2-3px.
2. Gunakan box-shadow keras seperti 4px 4px 0px #171717.
3. Hindari shadow blur yang terlalu lembut.
4. Gunakan font sans-serif tebal.
5. Gunakan warna yang konsisten.
6. Gunakan tombol dengan efek hover dan active.
7. Buat tabel dengan header berwarna.
8. Gunakan badge untuk status peminjaman.
9. Gunakan card untuk dashboard.
10. Pastikan desain responsif di desktop, tablet, dan smartphone.

Jangan membuat tampilan terlalu ramai.

**Halaman yang harus memiliki desain konsisten:**
- Landing page.
- Login.
- Registrasi.
- Dashboard mahasiswa.
- Dashboard petugas.
- Data anggota.
- Data buku.
- Kategori.
- Penerbit.
- Peminjaman.
- Pengembalian.
- Profil.

Gunakan sidebar untuk dashboard petugas dan navigasi sederhana untuk mahasiswa.

## 7. KETENTUAN PEMROGRAMAN PHP OOP

Gunakan konsep Object-Oriented Programming sederhana.

Contoh penerapan:
- Class Database untuk koneksi.
- Class Anggota untuk pengelolaan anggota.
- Class Buku untuk pengelolaan buku.
- Class Peminjaman untuk transaksi.
- Class AuthController untuk login dan registrasi.

Gunakan function untuk memisahkan proses:
- create()
- getAll()
- getById()
- update()
- delete()
- login()
- register()
- pinjamBuku()
- kembalikanBuku()
- hitungDenda()

Tidak perlu menggunakan design pattern yang kompleks.

Pastikan koneksi database memiliki penanganan error menggunakan try-catch.

Gunakan PDO dengan konfigurasi error mode exception.

## 8. KETENTUAN TRANSAKSI DATABASE

Fitur peminjaman dan pengembalian harus menggunakan transaksi database.

Gunakan:
- beginTransaction()
- commit()
- rollBack()

Contoh:
- Ketika peminjaman berhasil, transaksi disimpan dan stok buku berkurang.
- Jika proses gagal, perubahan database dibatalkan.
- Ketika pengembalian berhasil, status diperbarui dan stok buku bertambah.
- Jika proses gagal, seluruh perubahan dibatalkan.

Jangan sampai stok buku berubah tanpa transaksi peminjaman atau pengembalian yang berhasil.

## 9. KETENTUAN HASIL AKHIR

Pastikan project memenuhi seluruh kriteria tugas kuliah berikut:

1. Menggunakan satu database MySQL.
2. Memiliki minimal 4 tabel.
3. Setiap tabel memiliki primary key.
4. Setiap tabel memiliki minimal 3 atribut.
5. Memiliki relasi antar tabel menggunakan foreign key.
6. Memiliki minimal 10 data dummy.
7. Tampilan responsif.
8. Memiliki halaman utama.
9. Memiliki header, konten, dan footer.
10. Memiliki fitur CRUD minimal 2 tabel.
11. Menggunakan PDO atau MySQLi.
12. Memiliki penanganan error koneksi database.
13. Memiliki login dan registrasi.
14. Memiliki validasi username.
15. Menggunakan konsep OOP.
16. Memiliki transaksi yang melibatkan beberapa tabel.
17. Memiliki fitur pengembalian dan perhitungan denda.

## 10. CARA PENGERJAAN PROJECT

Saya ingin pengerjaan dilakukan secara bertahap, bukan langsung menghasilkan seluruh kode dalam satu jawaban.

Ikuti urutan berikut:

**Tahap 1: Persiapan**
- Analisis kebutuhan aplikasi.
- Finalisasi struktur folder.
- Buat rancangan relasi database.
- Buat file SQL lengkap dengan dummy data.

**Tahap 2: Backend**
- Buat koneksi database PDO.
- Buat class dan model.
- Buat controller.
- Buat proses login dan registrasi.
- Buat proses CRUD.

**Tahap 3: Frontend**
- Buat desain Neobrutalism.
- Buat landing page.
- Buat login dan registrasi.
- Buat dashboard mahasiswa dan petugas.
- Buat halaman CRUD.

**Tahap 4: Transaksi**
- Implementasikan peminjaman buku.
- Implementasikan pengembalian buku.
- Implementasikan perhitungan denda otomatis.
- Pastikan stok buku diperbarui dengan benar.

**Tahap 5: Pengujian**
- Uji koneksi database.
- Uji login mahasiswa.
- Uji login petugas.
- Uji registrasi.
- Uji CRUD anggota.
- Uji CRUD buku.
- Uji transaksi peminjaman.
- Uji transaksi pengembalian.
- Uji perhitungan denda.
- Uji tampilan responsif.

## 11. ATURAN SAAT MEMBERIKAN KODE

Ini merupakan bagian yang sangat penting.

Setiap kali membuat kode:

1. Sebutkan nama file yang harus dibuat.
2. Sebutkan lokasi folder file tersebut.
3. Berikan kode lengkap, bukan potongan yang tidak jelas.
4. Jelaskan fungsi file secara singkat.
5. Jelaskan hubungan file tersebut dengan file lainnya.
6. Berikan instruksi jika ada konfigurasi yang perlu diubah.
7. Pastikan kode sesuai dengan struktur project.
8. Jangan menggunakan fungsi atau class yang belum dibuat.
9. Jangan mengubah struktur database tanpa menjelaskan alasannya.
10. Pastikan kode dapat langsung dicoba di Laragon.

Jika terdapat perubahan pada file yang sudah dibuat, berikan kode lengkap versi terbaru agar mudah disalin.

Jangan melanjutkan ke tahap berikutnya sebelum tahap saat ini selesai dan dapat dijalankan.

## 12. MULAI PENGERJAAN

Sekarang mulailah dari **TAHAP 1: PERSIAPAN**.

Pertama, analisis kebutuhan aplikasi dan buat rancangan database berdasarkan ERD yang saya lampirkan.

Setelah itu, buat file SQL lengkap beserta seluruh tabel, relasi, dan minimal 10 data dummy.

Jangan langsung membuat seluruh aplikasi.

Fokus pada aplikasi perpustakaan yang sederhana, fungsional, mudah dipahami oleh mahasiswa, dan memenuhi seluruh ketentuan tugas kuliah.