<div class="page-heading">
    <div>
        <span class="eyebrow">DASHBOARD MAHASISWA</span>
        <h1>Halo, <?= e($pengguna['nama']) ?>!</h1>
        <p class="muted">Berikut ringkasan aktivitas membaca kamu.</p>
    </div>
    <a class="button" href="katalog.php">Jelajahi buku</a>
</div>

<div class="stats-grid stats-student">
    <article class="stat-card stat-yellow">
        <span>Stok tersedia</span>
        <strong><?= e($ringkasan['buku_tersedia']) ?></strong>
        <small>buku siap dipinjam</small>
    </article>
    <article class="stat-card stat-blue">
        <span>Sedang dipinjam</span>
        <strong><?= e($ringkasan['dipinjam']) ?></strong>
        <small>transaksi aktifmu</small>
    </article>
    <article class="stat-card stat-red">
        <span>Total denda</span>
        <strong>Rp <?= number_format($ringkasan['total_denda'], 0, ',', '.') ?></strong>
        <small>dari riwayat pengembalian</small>
    </article>
</div>

<section class="panel table-panel">
    <div class="panel-heading">
        <div>
            <h2>Riwayat peminjaman</h2>
            <p>Aktivitas bacaan terakhirmu.</p>
        </div>
        <a href="peminjaman.php">Lihat semua →</a>
    </div>

    <?php if (!$ringkasan['riwayat']): ?>
        <p class="empty-state">Belum ada peminjaman. Yuk, pilih buku pertamamu!</p>
    <?php else: ?>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Buku</th>
                        <th>Tanggal pinjam</th>
                        <th>Jatuh tempo</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ringkasan['riwayat'] as $peminjaman): ?>
                        <tr>
                            <td><?= e($peminjaman['judul_buku']) ?></td>
                            <td><?= e($peminjaman['tanggal_pinjam']) ?></td>
                            <td><?= e($peminjaman['tanggal_kembali']) ?></td>
                            <td>
                                <span class="badge <?= $peminjaman['status'] === 'dipinjam' ? 'badge-yellow' : 'badge-green' ?>">
                                    <?= e(ucfirst($peminjaman['status'])) ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format((float) $peminjaman['denda'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
