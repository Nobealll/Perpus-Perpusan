<?php
require_once __DIR__ . '/bootstrap.php';

$idBuku = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idBuku || $idBuku < 1) {
    http_response_code(404);
    exit('Buku tidak ditemukan.');
}

$buku = (new BukuController($db))->show($idBuku);
if (!$buku) {
    http_response_code(404);
    exit('Buku tidak ditemukan.');
}

$pageTitle = $buku['judul'];
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>

<main class="wrap book-detail-page">
    <a class="text-link" href="katalog.php">← Kembali ke katalog</a>

    <article class="book-detail panel">
        <img
            class="book-detail-image"
            src="<?= e(book_image_url($buku['gambar'] ?? null)) ?>"
            alt="Sampul buku <?= e($buku['judul']) ?>"
            data-image-fallback="assets/img/book-placeholder.svg">

        <div class="book-detail-info">
            <span class="book-category"><?= e($buku['nama_kategori']) ?></span>
            <h1><?= e($buku['judul']) ?></h1>
            <p class="book-author">Ditulis oleh <?= e($buku['penulis']) ?></p>

            <div class="book-detail-facts">
                <p><strong>Penerbit:</strong> <?= e($buku['nama_penerbit']) ?></p>
                <p><strong>Tahun terbit:</strong> <?= e($buku['tahun_terbit']) ?></p>
                <p><strong>ISBN:</strong> <?= e($buku['isbn']) ?></p>
                <p><strong>Lokasi rak:</strong>
                    <span class="badge <?= e($buku['rak']) != 0 ? 'badge-green' : 'badge-red' ?>">
                        
                        <?= e($buku['rak'] ?: 'Belum ditentukan') ?>
                    </span>
                </p>

                <p><strong>Stok tersedia:</strong>
                    <span class="badge <?= e($buku['stok']) != 0 ? 'badge-green' : 'badge-red' ?>">
                        <?= e(ucfirst($buku['stok'])) ?>
                        buku
                    </span>
                </p>

            </div>

            <section class="book-description">
                <h2>Deskripsi buku</h2>
                <?php if (trim((string) $buku['deskripsi']) !== ''): ?>
                    <p><?= nl2br(e($buku['deskripsi'])) ?></p>
                <?php else: ?>
                    <p>Belum ada deskripsi untuk buku ini.</p>
                <?php endif; ?>
            </section>

            <?php if ((int) $buku['stok'] > 0 && current_user() && current_user()['role'] === 'mahasiswa'): ?>
                <form action="katalog.php" method="post" data-confirm="Anda akan meminjam buku ini selama 7 hari. Lanjutkan?" data-confirm-title="Konfirmasi peminjaman" data-confirm-action="Pinjam buku">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id_buku" value="<?= e($buku['id_buku']) ?>">
                    <button class="button" type="submit">Pinjam buku</button>
                </form>
            <?php elseif ((int) $buku['stok'] < 1): ?>
                <span class="stock out-stock">Stok sedang habis</span>
            <?php endif; ?>
        </div>
    </article>
</main>

<?php require __DIR__ . '/components/footer.php'; ?>
