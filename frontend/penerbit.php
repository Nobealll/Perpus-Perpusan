<?php
require_once __DIR__ . '/bootstrap.php';
require_staff();
$controller = new PenerbitController($db);
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
            $aksiBerhasil = 'Penerbit berhasil dihapus.';
        } elseif (!empty($_POST['id'])) {
            $controller->update((int) $_POST['id'], $_POST);
            $aksiBerhasil = 'Data penerbit berhasil diperbarui.';
        } else {
            $controller->store($_POST);
            $aksiBerhasil = 'Penerbit baru berhasil ditambahkan.';
        }
        set_flash('success', $aksiBerhasil);
        redirect('penerbit.php');
    } catch (Throwable $exception) {
        $error = 'Tidak dapat menyimpan penerbit. Pastikan data valid dan penerbit tidak sedang dipakai buku.';
    }
}
$flash = get_flash();
$rows = $controller->index();
$pageTitle = 'Penerbit';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>
<main class="app-layout wrap"><?php require __DIR__ . '/components/sidebar.php'; ?><section class="page-content">
        <div class="page-heading">
            <div><span class="eyebrow">PENGELOLAAN DATA</span>
                <h1>Penerbit</h1>
                <p class="muted">Informasi penerbit koleksi perpustakaan.</p>
            </div>
        </div>
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <section class="panel">
            <h2><?= $editing ? 'Ubah penerbit' : 'Tambah penerbit' ?></h2>
            <form method="post" class="form-stack form-grid compact-form" <?php if ($editing): ?>data-confirm="Simpan perubahan data penerbit?" data-confirm-title="Simpan perubahan?" data-confirm-action="Simpan perubahan"<?php endif; ?>>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= e($editing['id_penerbit'] ?? '') ?>">

                <label>Nama penerbit
                    <input name="nama_penerbit" maxlength="100" required value="<?= e($editing['nama_penerbit'] ?? '') ?>">
                </label>

                <label>Alamat
                    <input name="alamat" maxlength="150" required value="<?= e($editing['alamat'] ?? '') ?>">
                </label>

                <label>Nomor telepon
                    <input name="no_telepon" maxlength="15" required value="<?= e($editing['no_telepon'] ?? '') ?>">
                </label>

                <button class="button button-small span-two" type="submit">
                    <?= $editing ? 'Simpan perubahan' : 'Tambah penerbit' ?>
                </button>
            </form>
        </section>
        <section class="panel table-panel">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Nama penerbit</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?= e($row['nama_penerbit']) ?></td>
                                <td><?= e($row['alamat']) ?></td>
                                <td><?= e($row['no_telepon']) ?></td>
                                <td class="actions">
                                    <a class="text-link" href="?edit=<?= e($row['id_penerbit']) ?>">Ubah</a>
                                    <form method="post" data-confirm="Hapus penerbit ini?" data-confirm-title="Hapus penerbit?" data-confirm-action="Hapus" data-confirm-variant="danger">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="delete_id" value="<?= e($row['id_penerbit']) ?>">
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
