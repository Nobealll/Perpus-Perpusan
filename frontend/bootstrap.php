<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/config/Database.php';
require_once __DIR__ . '/../backend/controllers/AuthController.php';
require_once __DIR__ . '/../backend/controllers/DashboardController.php';
require_once __DIR__ . '/../backend/controllers/BukuController.php';
require_once __DIR__ . '/../backend/controllers/PeminjamanController.php';
require_once __DIR__ . '/../backend/controllers/PengembalianController.php';
require_once __DIR__ . '/../backend/controllers/AnggotaController.php';
require_once __DIR__ . '/../backend/controllers/KategoriController.php';
require_once __DIR__ . '/../backend/controllers/PenerbitController.php';

// Session dipakai untuk menyimpan status login dan token formulir.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Semua halaman frontend memakai koneksi database yang sama.
try {
    $db = (new Database())->getConnection();
} catch (RuntimeException $exception) {
    http_response_code(500);
    exit(htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// Helper kecil untuk tampilan dan pemeriksaan akses.
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function book_image_url(?string $path): string
{
    if (!$path || !preg_match('#^uploads/buku/[a-f0-9]{32}\.(jpg|png|webp)$#', $path)) {
        return 'assets/img/book-placeholder.svg';
    }

    return '../' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        redirect('login.php');
    }
    return $user;
}

function require_staff(): array
{
    $user = require_login();
    if (!in_array($user['role'], ['admin', 'petugas'], true)) {
        http_response_code(403);
        exit('Halaman ini hanya dapat diakses petugas.');
    }
    return $user;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Permintaan tidak valid. Muat ulang halaman lalu coba kembali.');
    }
}

function set_flash(string $type, string $message): void
{
    $allowedTypes = ['success', 'error'];
    if (!in_array($type, $allowedTypes, true)) {
        $type = 'success';
    }

    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $message;
}
