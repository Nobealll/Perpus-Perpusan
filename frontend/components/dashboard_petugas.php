<div class="page-heading">
    <div>
        <span class="eyebrow">DASHBOARD PETUGAS</span>
        <h1>Halo, <?= e($pengguna['nama']) ?>!</h1>
        <p class="muted">Ringkasan aktivitas perpustakaan.</p>
    </div>
    <a class="button" href="buku.php">Kelola buku</a>
</div>

<div class="stats-grid">
    <a class="stat-card stat-yellow" href="anggota.php">
        <span>Total anggota</span>
        <strong><?= e($ringkasan['anggota']) ?></strong>
        <small>anggota terdaftar</small>
    </a>
    <a class="stat-card stat-blue" href="buku.php">
        <span>Total buku</span>
        <strong><?= e($ringkasan['buku']) ?></strong>
        <small>judul dalam katalog</small>
    </a>
    <a class="stat-card stat-red" href="kategori.php">
        <span>Kategori</span>
        <strong><?= e($ringkasan['kategori']) ?></strong>
        <small>kelompok koleksi</small>
    </a>
    <a class="stat-card stat-green" href="peminjaman.php">
        <span>Peminjaman aktif</span>
        <strong><?= e($ringkasan['dipinjam']) ?></strong>
        <small>dari <?= e($ringkasan['peminjaman']) ?> transaksi</small>
    </a>
    <a class="stat-card stat-white" href="pengembalian.php">
        <span>Terlambat</span>
        <strong><?= e($ringkasan['terlambat']) ?></strong>
        <small>perlu ditindaklanjuti</small>
    </a>
</div>

<section class="panel dashboard-shortcuts">
    <div>
        <h2>Kelola perpustakaan</h2>
        <p>Kelola anggota dan pantau transaksi buku.</p>
    </div>
    <div class="button-row">
        <a class="button button-light" href="anggota.php">Data anggota</a>
        <a class="button button-light" href="peminjaman.php">Transaksi</a>
        <a class="button" href="pengembalian.php">Proses pengembalian</a>
    </div>
</section>
