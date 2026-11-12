<?php
require_once __DIR__ . '/bootstrap.php';

$pengguna = require_login();
$dashboard = new DashboardController($db);
$petugas = in_array($pengguna['role'], ['admin', 'petugas'], true);

if ($petugas) {
    $ringkasan = $dashboard->petugas();
} else {
    $ringkasan = $dashboard->mahasiswa((int) $pengguna['id']);
}

$pageTitle = 'Dashboard';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>

<main class="app-layout wrap">
    <?php require __DIR__ . '/components/sidebar.php'; ?>

    <section class="page-content">
        <?php if ($petugas): ?>
            <?php require __DIR__ . '/components/dashboard_petugas.php'; ?>
        <?php else: ?>
            <?php require __DIR__ . '/components/dashboard_mahasiswa.php'; ?>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/components/footer.php'; ?>
