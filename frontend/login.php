<?php
require_once __DIR__ . '/bootstrap.php';
if (current_user()) {
    redirect('dashboard.php');
}
$error = '';
$auth = new AuthController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    try {
        $auth->login(
            $_POST['username'] ?? '',
            $_POST['password'] ?? ''
        );
        redirect('dashboard.php');
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}
$pageTitle = 'Masuk';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>
<main class="auth-shell">
    <section class="auth-card">
        <span class="eyebrow">SENANG BERTEMU LAGI</span>
        <h1>Masuk ke akunmu</h1>
        <p class="muted">Lanjutkan perjalanan membaca dari sini.</p>
        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post" class="form-stack">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Username<input name="username" required maxlength="50" autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>"></label>
            <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
            <button class="button button-wide" type="submit">Masuk</button>
        </form>
        <p class="form-foot">Belum punya akun? <a href="register.php">Daftar sebagai mahasiswa</a></p>
        <div class="demo-hint">Akun demo: <strong>nobel</strong> atau <strong>admin</strong> · password <strong>password</strong></div>
    </section>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>
