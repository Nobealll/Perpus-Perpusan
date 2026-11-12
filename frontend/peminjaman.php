<?php
require_once __DIR__ . '/bootstrap.php';

$pengguna = require_login();
$peminjaman = new PeminjamanController($db);

// Mahasiswa hanya melihat transaksi miliknya.
if ($pengguna['role'] === 'mahasiswa') {
    $daftarPeminjaman = $peminjaman->byAnggota((int) $pengguna['id']);
    $tampilkanNamaAnggota = false;
} else {
    $daftarPeminjaman = $peminjaman->index();
    $tampilkanNamaAnggota = true;
}

$pageTitle = 'Data Peminjaman';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>

<main class="app-layout wrap">
    <?php require __DIR__ . '/components/sidebar.php'; ?>

    <section class="page-content">
        <div class="page-heading">
            <div>
                <span class="eyebrow">TRANSAKSI</span>
                <h1><?= $tampilkanNamaAnggota ? 'Semua peminjaman' : 'Peminjaman saya' ?></h1>
                <p class="muted">Pantau status dan tanggal pengembalian buku.</p>
            </div>
        </div>

        <section class="panel table-panel">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <?php if ($tampilkanNamaAnggota): ?>
                                <th>Anggota</th>
                            <?php endif; ?>
                            <th>Buku</th>
                            <th>Tanggal pinjam</th>
                            <th>Jatuh tempo</th>
                            <th>Tanggal kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftarPeminjaman as $item): ?>
                            <tr>
                                <?php if ($tampilkanNamaAnggota): ?>
                                    <td><?= e($item['nama_anggota']) ?></td>
                                <?php endif; ?>
                                <td><?= e($item['judul_buku']) ?></td>
                                <td><?= e($item['tanggal_pinjam']) ?></td>
                                <td><?= e($item['tanggal_kembali']) ?></td>
                                <td><?= e($item['tanggal_pengembalian_aktual'] ?? '—') ?></td>
                                <td>
                                    <span class="badge <?= $item['status'] === 'dipinjam' ? 'badge-yellow' : 'badge-green' ?>">
                                        <?= e(ucfirst($item['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $item['denda'] > 0 ? 'badge-red' : '' ?>">
                                        Rp <?= number_format((float) $item['denda'], 0, ',', '.') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (!$daftarPeminjaman): ?>
                            <tr>
                                <td colspan="<?= $tampilkanNamaAnggota ? 7 : 6 ?>" class="empty-state">
                                    Belum ada transaksi peminjaman.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</main>

<?php require __DIR__ . '/components/footer.php'; ?>