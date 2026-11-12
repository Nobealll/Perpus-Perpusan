<?php
$pengguna = current_user();
$petugas = in_array($pengguna['role'], ['admin', 'petugas'], true);
?>

<aside class="sidebar">
    <a class="sidebar-link" href="dashboard.php">Ringkasan</a>
    <a class="sidebar-link" href="profile.php">Profil saya</a>

    <?php if ($petugas): ?>
        <span class="sidebar-label">PENGELOLAAN</span>
        <a class="sidebar-link" href="anggota.php">Anggota</a>
        <a class="sidebar-link" href="buku.php">Kelola buku</a>
        <a class="sidebar-link" href="kategori.php">Kategori</a>
        <a class="sidebar-link" href="penerbit.php">Penerbit</a>
        <a class="sidebar-link" href="peminjaman.php">Peminjaman</a>
        <a class="sidebar-link" href="pengembalian.php">Pengembalian</a>
    <?php else: ?>
        <a class="sidebar-link" href="katalog.php">Katalog buku</a>
        <a class="sidebar-link" href="peminjaman.php">Peminjaman saya</a>
    <?php endif; ?>
</aside>
