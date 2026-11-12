<?php
require_once __DIR__ . '/bootstrap.php';
require_staff();
$controller = new AnggotaController($db);
$error = '';
$aksiBerhasil = '';
$editing = false;
if (isset($_GET['edit'])) {
    $editing = $controller->show((int) $_GET['edit']);
}

// Simpan perubahan data anggota yang dikirim petugas.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        if (isset($_POST['delete_id'])) {
            $controller->destroy((int) $_POST['delete_id']);
            $aksiBerhasil = 'Data anggota berhasil dihapus.';
        } elseif (!empty($_POST['id'])) {
            (new AuthController($db))->updateMember((int) $_POST['id'], $_POST);
            $aksiBerhasil = 'Data anggota berhasil diperbarui.';
        } else {
            (new AuthController($db))->register($_POST);
            $aksiBerhasil = 'Anggota baru berhasil ditambahkan.';
        }
        set_flash('success', $aksiBerhasil);
        redirect('anggota.php');
    } catch (Throwable $exception) {
        if ($exception instanceof DomainException || $exception instanceof InvalidArgumentException) {
            $error = $exception->getMessage();
        } else {
            $error = 'Tidak dapat menyimpan atau mengarsipkan anggota. Jika masih ada pinjaman aktif, proses hapus perlu menunggu pengembalian.';
        }
    }
}
$flash = get_flash();
$rows = $controller->index();
$pageTitle = 'Anggota';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>
<main class="app-layout wrap"><?php require __DIR__ . '/components/sidebar.php'; ?><section class="page-content">
        <div class="page-heading">
            <div><span class="eyebrow">PENGELOLAAN DATA</span>
                <h1>Anggota perpustakaan</h1>
                <p class="muted">Kelola akun mahasiswa yang terdaftar.</p>
            </div>
        </div>
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <section class="panel">
            <h2><?= $editing ? 'Ubah data anggota' : 'Tambah anggota' ?></h2>
            <form method="post" class="form-stack form-grid compact-form" <?php if ($editing): ?>data-confirm="Simpan perubahan data anggota?" data-confirm-title="Simpan perubahan?" data-confirm-action="Simpan perubahan"<?php endif; ?>>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= e($editing['id_anggota'] ?? '') ?>">

                <label>Nama lengkap
                    <input name="nama" maxlength="100" required value="<?= e($editing['nama'] ?? '') ?>">
                </label>
                <label>Username
                    <input name="username" minlength="3" maxlength="50" required value="<?= e($editing['username'] ?? '') ?>">
                </label>
                <label class="span-two">Alamat
                    <input name="alamat" maxlength="150" required value="<?= e($editing['alamat'] ?? '') ?>">
                </label>
                <label>Nomor telepon
                    <input name="no_hp" maxlength="15" required value="<?= e($editing['no_hp'] ?? '') ?>">
                </label>

                <?php if (!$editing): ?>
                    <label>Password awal
                        <input type="password" name="password" minlength="8" required>
                    </label>
                <?php endif; ?>

                <button class="button button-small span-two" type="submit">
                    <?= $editing ? 'Simpan perubahan' : 'Tambah anggota' ?>
                </button>
            </form>
        </section>
        <section class="panel table-panel">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Telepon</th>
                            <th>Tanggal daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($rows as $row): ?><tr>
                                <td><?= e($row['nama']) ?></td>
                                <td><?= e($row['username']) ?></td>
                                <td><?= e($row['no_hp']) ?></td>
                                <td><?= e($row['tanggal_daftar']) ?></td>
                                <td class="actions"><a class="text-link" href="?edit=<?= e($row['id_anggota']) ?>">Ubah</a>
                                    <form method="post" data-confirm="Hapus anggota ini?" data-confirm-title="Hapus anggota?" data-confirm-action="Hapus" data-confirm-variant="danger"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="delete_id" value="<?= e($row['id_anggota']) ?>"><button class="link-button" type="submit">Hapus</button></form>
                                </td>
                            </tr><?php endforeach; ?></tbody>
                </table>
            </div>
        </section>
    </section>
</main><?php require __DIR__ . '/components/footer.php'; ?>
