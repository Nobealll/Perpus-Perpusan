<?php
require_once __DIR__ . '/bootstrap.php';
$user = require_login();
$profile = (new AuthController($db))->getProfile((int) $user['id'], $user['role']);
if (!$profile) {
    http_response_code(404);
    exit('Profil tidak ditemukan.');
}
$pageTitle = 'Profil';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>
<main class="app-layout wrap"><?php require __DIR__ . '/components/sidebar.php'; ?><section class="page-content">
        <div class="page-heading">
            <div><span class="eyebrow">AKUN SAYA</span>
                <h1>Profil</h1>
                <p class="muted">Informasi akun yang terdaftar di PerpusKita.</p>
            </div>
        </div>
        <section class="panel profile-panel">
            <div class="profile-avatar">
                <?= e(strtoupper(substr($profile['nama'], 0, 1))) ?>
            </div>

            <div class="profile-fields">
                <div>
                    <small>Nama</small>
                    <strong><?= e($profile['nama']) ?></strong>
                </div>
                <div>
                    <small>Username</small>
                    <strong><?= e($profile['username']) ?></strong>
                </div>
                <div>
                    <small>Peran</small>
                    <strong><?= e(ucfirst($profile['role'] ?? 'mahasiswa')) ?></strong>
                </div>
                <div>
                    <small>Nomor telepon</small>
                    <strong><?= e($profile['no_hp']) ?></strong>
                </div>

                <?php if (isset($profile['alamat'])): ?>
                    <div class="profile-full">
                        <small>Alamat</small>
                        <strong><?= e($profile['alamat']) ?></strong>
                    </div>
                    <div>
                        <small>Tanggal daftar</small>
                        <strong><?= e($profile['tanggal_daftar']) ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </section>
</main><?php require __DIR__ . '/components/footer.php'; ?>
