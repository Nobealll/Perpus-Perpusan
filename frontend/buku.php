<?php
require_once __DIR__ . '/bootstrap.php';
require_staff();

$buku = new BukuController($db);
$pesanError = '';
$aksiBerhasil = '';
$bukuDiedit = isset($_GET['edit'])
    ? $buku->show((int) $_GET['edit'])
    : false;

// Form petugas membuat, mengubah, atau menghapus data buku.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    try {
        if (isset($_POST['hapus_id'])) {
            $buku->destroy((int) $_POST['hapus_id']);
            $aksiBerhasil = 'Buku berhasil dihapus.';
        } elseif (!empty($_POST['id_buku'])) {
            $buku->update((int) $_POST['id_buku'], $_POST, $_FILES['gambar'] ?? null);
            $aksiBerhasil = 'Data buku berhasil diperbarui.';
        } else {
            $buku->store($_POST, $_FILES['gambar'] ?? null);
            $aksiBerhasil = 'Buku baru berhasil ditambahkan.';
        }
        set_flash('success', $aksiBerhasil);

        redirect('buku.php');
    } catch (Throwable $exception) {
        $pesanError = $exception instanceof InvalidArgumentException
            ? $exception->getMessage()
            : 'Buku tidak dapat disimpan. Periksa data dan pastikan buku tidak sedang dipinjam.';
    }
}

$flash = get_flash();
$daftarBuku = $buku->index();
$kategori = (new KategoriController($db))->index();
$penerbit = (new PenerbitController($db))->index();

$pageTitle = 'Kelola Buku';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>

<main class="app-layout wrap">
    <?php require __DIR__ . '/components/sidebar.php'; ?>
    
    
    <section class="page-content">
        <div class="page-heading">
            <div>
                <span class="eyebrow">PENGELOLAAN DATA</span>
                <h1>Kelola buku</h1>
                <p class="muted">Tambah, ubah, dan hapus koleksi buku.</p>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <?php if ($pesanError !== ''): ?>
            <div class="alert alert-error"><?= e($pesanError) ?></div>
        <?php endif; ?>

        <section class="panel">
            <h2><?= $bukuDiedit ? 'Ubah buku' : 'Tambah buku' ?></h2>

            <form method="post" enctype="multipart/form-data" class="form-stack form-grid compact-form" <?php if ($bukuDiedit): ?>data-confirm="Simpan perubahan data buku?" data-confirm-title="Simpan perubahan?" data-confirm-action="Simpan perubahan"<?php endif; ?>>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id_buku" value="<?= e($bukuDiedit['id_buku'] ?? '') ?>">

                <label>Judul buku
                    <input name="judul" maxlength="150" required value="<?= e($bukuDiedit['judul'] ?? '') ?>">
                </label>

                <label>Penulis
                    <input name="penulis" maxlength="100" required value="<?= e($bukuDiedit['penulis'] ?? '') ?>">
                </label>

                <label>Lokasi rak
                    <p style="color: darkgrey;">Contoh: Blok C - Rak 01</p>
                    <input
                        name="rak"
                        maxlength="50"
                        required
                        value="<?= e($bukuDiedit['rak'] ?? '') ?>"
                    >
                </label>

                <label class="span-two">Deskripsi buku
                    <textarea name="deskripsi" rows="5" maxlength="10000"><?= e($bukuDiedit['deskripsi'] ?? '') ?></textarea>
                </label>

                <label>ISBN
                    <input name="isbn" maxlength="20" required value="<?= e($bukuDiedit['isbn'] ?? '') ?>">
                </label>

                <label>Tahun terbit
                    <input type="number" name="tahun_terbit" min="1901" max="2155" required value="<?= e($bukuDiedit['tahun_terbit'] ?? '') ?>">
                </label>

                <label>Kategori
                    <select name="id_kategori" required>
                        <option value="">Pilih kategori</option>
                        <?php foreach ($kategori as $item): ?>
                            <option value="<?= e($item['id_kategori']) ?>" <?= (int) ($bukuDiedit['id_kategori'] ?? 0) === (int) $item['id_kategori'] ? 'selected' : '' ?>>
                                <?= e($item['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>Penerbit
                    <select name="id_penerbit" required>
                        <option value="">Pilih penerbit</option>
                        <?php foreach ($penerbit as $item): ?>
                            <option value="<?= e($item['id_penerbit']) ?>" <?= (int) ($bukuDiedit['id_penerbit'] ?? 0) === (int) $item['id_penerbit'] ? 'selected' : '' ?>>
                                <?= e($item['nama_penerbit']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>Jumlah stok
                    <input type="number" name="stok" min="0" required value="<?= e($bukuDiedit['stok'] ?? 0) ?>">
                </label>

                <div class="span-two image-upload-field">
                    <label for="gambar">Gambar sampul (JPG, PNG, WEBP; maks. 2 MB)</label>
                    <input
                        id="gambar"
                        type="file"
                        name="gambar"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        data-image-preview="preview-sampul"
                    >
                    <img
                        id="preview-sampul"
                        class="book-edit-preview"
                        src="<?= e(book_image_url($bukuDiedit['gambar'] ?? null)) ?>"
                        alt="Preview sampul buku"
                        data-image-fallback="assets/img/book-placeholder.svg"
                    >
                    <?php if (!empty($bukuDiedit['gambar'])): ?>
                        <small>Sampul saat ini. Kosongkan pilihan file untuk tetap memakai gambar ini.</small>
                    <?php endif; ?>
                </div>

                <button class="button button-small span-two" type="submit">
                    <?= $bukuDiedit ? 'Simpan perubahan' : 'Tambah buku' ?>
                </button>
            </form>
        </section>

        <section class="panel table-panel">
            <h2>Daftar buku</h2>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Sampul</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>Lokasi rak</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftarBuku as $item): ?>
                            <tr>
                                <td><?= e($item['judul']) ?></td>
                                <td>
                                    <img
                                        class="book-table-thumbnail"
                                        src="<?= e(book_image_url($item['gambar'] ?? null)) ?>"
                                        alt="Sampul <?= e($item['judul']) ?>"
                                        data-image-fallback="assets/img/book-placeholder.svg"
                                    >
                                </td>
                                <td><?= e($item['penulis']) ?></td>
                                <td><?= e($item['nama_kategori']) ?></td>
                                <td><?= e($item['rak']) ?></td>
                                <td><?= e($item['stok']) ?></td>
                                <td class="actions">
                                    <a class="text-link" href="detail_buku.php?id=<?= e($item['id_buku']) ?>">Detail</a>
                                    <a class="text-link" href="?edit=<?= e($item['id_buku']) ?>">Ubah</a>
                                    <form method="post" class="data-confirm" data-confirm="Hapus buku ini?" data-confirm-title="Hapus buku?" data-confirm-action="Hapus" data-confirm-variant="danger">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="hapus_id" value="<?= e($item['id_buku']) ?>">
                                        <button class="link-button" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</main>

<?php require __DIR__ . '/components/footer.php'; ?>
