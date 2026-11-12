<?php
require_once __DIR__ . '/bootstrap.php';
require_staff();

$pengembalian = new PengembalianController($db);
$pesan = '';
$pesanError = '';

// Proses pengembalian jika petugas mengirim formulir.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    try {
        $hasil = $pengembalian->kembalikan(
            (int) ($_POST['id_peminjaman'] ?? 0)
        );

        $denda = number_format($hasil['denda'], 0, ',', '.');
        $pesan = "Pengembalian berhasil. Terlambat {$hasil['hari_terlambat']} hari; denda Rp {$denda}.";
    } catch (Throwable $exception) {
        $pesanError = $exception->getMessage();
    }
}

$daftarPeminjaman = $pengembalian->index();
$pageTitle = 'Pengembalian';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>

<main class="app-layout wrap">
    <?php require __DIR__ . '/components/sidebar.php'; ?>

    <section class="page-content">
        <div class="page-heading">
            <div>
                <span class="eyebrow">TRANSAKSI</span>
                <h1>Proses pengembalian</h1>
                <p class="muted">Denda Rp5.000 dihitung untuk setiap hari keterlambatan.</p>
            </div>
        </div>

        <?php if ($pesan !== ''): ?>
            <div class="alert alert-success"><?= e($pesan) ?></div>
        <?php endif; ?>

        <?php if ($pesanError !== ''): ?>
            <div class="alert alert-error"><?= e($pesanError) ?></div>
        <?php endif; ?>

        <section class="panel table-panel">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Tanggal pinjam</th>
                            <th>Jatuh tempo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftarPeminjaman as $peminjaman): ?>
                            <tr>
                                <td><?= e($peminjaman['nama_anggota']) ?></td>
                                <td><?= e($peminjaman['judul_buku']) ?></td>
                                <td><?= e($peminjaman['tanggal_pinjam']) ?></td>
                                <td><?= e($peminjaman['tanggal_kembali']) ?></td>
                                <td>
                                    <form method="post" data-confirm="Proses pengembalian buku ini?" data-confirm-title="Konfirmasi pengembalian" data-confirm-description="Sistem akan mencatat tanggal pengembalian dan menghitung denda keterlambatan." data-confirm-action="Kembalikan buku" data-confirm-variant="danger">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="id_peminjaman" value="<?= e($peminjaman['id_peminjaman']) ?>">
                                        <button class="button button-small" type="submit">Kembalikan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (!$daftarPeminjaman): ?>
                            <tr>
                                <td colspan="5" class="empty-state">
                                    Tidak ada buku yang menunggu pengembalian.
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
