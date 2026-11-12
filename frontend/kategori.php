<?php
require_once __DIR__ . '/bootstrap.php';
require_staff();
$controller = new KategoriController($db);
$error = '';
$aksiBerhasil = '';
$editing = false;
if (isset($_GET['edit'])) {
    $editing = $controller->show((int) $_GET['edit']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        if (isset($_POST['delete_id'])) {
            $controller->destroy((int) $_POST['delete_id']);
            $aksiBerhasil = 'Kategori berhasil dihapus.';
        } elseif (!empty($_POST['id'])) {
            $controller->update((int) $_POST['id'], $_POST);
            $aksiBerhasil = 'Kategori berhasil diperbarui.';
        } else {
            $controller->store($_POST);
            $aksiBerhasil = 'Kategori baru berhasil ditambahkan.';
        }
        set_flash('success', $aksiBerhasil);
        redirect('kategori.php');
    } catch (Throwable $exception) {
        $error = 'Tidak dapat menyimpan kategori. Pastikan nama belum digunakan dan kategori tidak sedang dipakai buku.';
    }
}
$flash = get_flash();
$rows = $controller->index();
$pageTitle = 'Kategori';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>
<main class="app-layout wrap"><?php require __DIR__ . '/components/sidebar.php'; ?><section class="page-content">
        <div class="page-heading">
            <div><span class="eyebrow">PENGELOLAAN DATA</span>
                <h1>Kategori buku</h1>
                <p class="muted">Kelompokkan koleksi agar mudah ditemukan.</p>
            </div>
        </div>
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <section class="panel">
            <h2><?= $editing ? 'Ubah kategori' : 'Tambah kategori' ?></h2>
            <form method="post" class="form-stack form-grid compact-form" <?php if ($editing): ?>data-confirm="Simpan perubahan data kategori?" data-confirm-title="Simpan perubahan?" data-confirm-action="Simpan perubahan"<?php endif; ?>>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= e($editing['id_kategori'] ?? '') ?>">

                <label>Nama kategori
                    <input name="nama_kategori" maxlength="80" required value="<?= e($editing['nama_kategori'] ?? '') ?>">
                </label>

                <label>Deskripsi
                    <input name="deskripsi" value="<?= e($editing['deskripsi'] ?? '') ?>">
                </label>

                <button class="button button-small span-two" type="submit">
                    <?= $editing ? 'Simpan perubahan' : 'Tambah kategori' ?>
                </button>
            </form>
        </section>
        <section class="panel table-panel">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Nama kategori</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?= e($row['nama_kategori']) ?></td>
                                <td><?= e($row['deskripsi']) ?></td>
                                <td class="actions">
                                    <a class="text-link" href="?edit=<?= e($row['id_kategori']) ?>">Ubah</a>
                                    <form method="post" data-confirm="Hapus kategori ini?" data-confirm-title="Hapus kategori?" data-confirm-action="Hapus" data-confirm-variant="danger">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="delete_id" value="<?= e($row['id_kategori']) ?>">
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
</main><?php require __DIR__ . '/components/footer.php'; ?>
