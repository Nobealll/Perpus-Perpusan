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
        $auth->register($_POST);
        $auth->login(
            $_POST['username'] ?? '',
            $_POST['password'] ?? ''
        );
        redirect('dashboard.php');
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}
$pageTitle = 'Registrasi';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>
<main class="auth-shell">
    <section class="auth-card auth-card-wide">
        <span class="eyebrow">GABUNG JADI ANGGOTA</span>
        <h1>Buat akun anggotanya!</h1>
        <p class="muted">Isi data ini ya, untuk mulai meminjam buku.</p>
        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post" class="form-stack form-grid">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Nama lengkap<input name="nama" required maxlength="100" value="<?= e($_POST['nama'] ?? '') ?>"></label>
            <label>Username<input name="username" required minlength="3" maxlength="50" autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>"></label>
            <label class="span-two">Alamat<input name="alamat" required maxlength="150" value="<?= e($_POST['alamat'] ?? '') ?>"></label>
            <label>Nomor telepon<input name="no_hp" required maxlength="15" inputmode="tel" value="<?= e($_POST['no_hp'] ?? '') ?>"></label>
            <label>Password<input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
            <button class="button button-wide span-two" type="submit">Buat akun</button>
        </form>
        <p class="form-foot">Sudah terdaftar? <a href="login.php">Masuk di sini</a></p>
    </section>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>
