<?php
require_once __DIR__ . '/bootstrap.php';

$pengguna = current_user();
$buku = new BukuController($db);
$kataKunci = trim((string) ($_GET['q'] ?? $_POST['q'] ?? ''));
$pesan = '';
$pesanError = '';

// Hanya mahasiswa yang dapat meminjam dari katalog.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (!$pengguna || $pengguna['role'] !== 'mahasiswa') {
        $pesanError = 'Masuk sebagai mahasiswa untuk meminjam buku.';
    } else {
        try {
            $peminjaman = new PeminjamanController($db);
            $peminjaman->pinjam((int) $pengguna['id'], (int) ($_POST['id_buku'] ?? 0));
            $pesan = 'Peminjaman berhasil. Kembalikan buku dalam 7 hari.';
        } catch (Throwable $exception) {
            $pesanError = $exception->getMessage();
        }
    }
}

$daftarBuku = $buku->index($kataKunci);
$pageTitle = 'Katalog Buku';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>

<main class="wrap catalog-page">
    <div class="page-heading">
        <div>
            <span class="eyebrow">KATALOG PERPUSTAKAAN</span>
            <h1>Pilih bacaanmu.</h1>
            <p class="muted">Koleksi buku untuk menemani belajar dan waktu luang.</p>
            
        </div>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-success"><?= e($pesan) ?></div>
    <?php endif; ?>

    <?php if ($pesanError !== ''): ?>
        <div class="alert alert-error"><?= e($pesanError) ?></div>
    <?php endif; ?>

    <form method="get" class="catalog-search">
        <label for="q">Cari berdasarkan judul atau penulis</label>
        <div class="catalog-search-row">
            <input id="q" name="q" type="search" value="<?= e($kataKunci) ?>" placeholder="Contoh: Pemrograman PHP">
            <button class="button" type="submit">Cari buku</button>
            <?php if ($kataKunci !== ''): ?>
                <a class="button button-light" href="katalog.php">Hapus pencarian</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="book-grid">
        <?php foreach ($daftarBuku as $item): ?>
            <article class="book-card">
                <img
                    class="book-cover-image"
                    src="<?= e(book_image_url($item['gambar'] ?? null)) ?>"
                    alt="Sampul buku <?= e($item['judul']) ?>"
                    data-image-fallback="assets/img/book-placeholder.svg"
                >

                <div class="book-info">
                    <span class="book-category"><?= e($item['nama_kategori']) ?></span>
                    <h2><?= e($item['judul']) ?></h2>
                    <p class="book-author">Oleh <?= e($item['penulis']) ?></p>

                    <?php if ((int) $item['stok'] > 0): ?>
                        <span class="stock in-stock">Tersedia · <?= e($item['stok']) ?> eksemplar</span>
                    <?php else: ?>
                        <span class="stock out-stock">Stok habis</span>
                    <?php endif; ?>

                    <p><?= e($item['nama_penerbit']) ?> · <?= e($item['tahun_terbit']) ?></p>
                    <p class="book-shelf-location">
                        <strong>Lokasi rak:</strong>
                        <?= e($item['rak'] ?: 'Belum ditentukan') ?>
                    </p>

                    <a class="button button-light button-small" href="detail_buku.php?id=<?= e($item['id_buku']) ?>">
                        Lihat detail
                    </a>

                    <?php if ($pengguna && $pengguna['role'] === 'mahasiswa' && (int) $item['stok'] > 0): ?>
                        <form method="post" data-confirm="Anda akan meminjam buku ini selama 7 hari. Lanjutkan?" data-confirm-title="Konfirmasi peminjaman" data-confirm-action="Pinjam buku">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id_buku" value="<?= e($item['id_buku']) ?>">
                            <input type="hidden" name="q" value="<?= e($kataKunci) ?>">
                            <button class="button button-small" type="submit">Pinjam buku</button>
                        </form>
                    <?php elseif (!$pengguna): ?>
                        <a class="text-link ms-5" href="login.php">Masuk untuk meminjam →</a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if (!$daftarBuku): ?>
            <p class="empty-state">Tidak ada buku yang cocok dengan pencarian tersebut.</p>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/components/footer.php'; ?>
