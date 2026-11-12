<header class="topbar">
    <a class="brand" href="index.php">
        <span class="brand-mark">P</span>
        Perpus Perpusan
    </a>

    <nav class="nav-links" aria-label="Navigasi utama">
        <a href="index.php">Beranda</a>

        <?php if (current_user()): ?>
            <a href="dashboard.php">Dashboard</a>

            <form action="logout.php" method="post" class="inline-form" data-confirm="Keluar dari akun sekarang?" data-confirm-title="Keluar dari akun?" data-confirm-description="Anda perlu masuk lagi untuk menggunakan akun." data-confirm-action="Keluar" data-confirm-variant="danger">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <button class="button button-danger button-small" type="submit">Keluar</button>
            </form>
        <?php else: ?>
            <a href="login.php">Masuk</a>
            <a class="button button-small button-green" href="register.php">Daftar</a>
        <?php endif; ?>
    </nav>
</header>
