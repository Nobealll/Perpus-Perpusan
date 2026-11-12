<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Anggota.php';
require_once __DIR__ . '/../models/Petugas.php';

/** Mengurus pendaftaran, login, logout, dan profil pengguna. */
class AuthController
{
    private Anggota $anggota;
    private Petugas $petugas;

    public function __construct(private PDO $db)
    {
        $this->anggota = new Anggota($db);
        $this->petugas = new Petugas($db);
    }

    public function register(array $data): int
    {
        $nama = trim($data['nama'] ?? '');
        $alamat = trim($data['alamat'] ?? '');
        $noHp = trim($data['no_hp'] ?? '');
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        $this->validateRegistration($nama, $alamat, $noHp, $username, $password);
        $this->assertUsernameAvailable($username);

        return $this->anggota->create([
            'nama' => $nama,
            'alamat' => $alamat,
            'no_hp' => $noHp,
            'tanggal_daftar' => date('Y-m-d'),
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }

    public function login(string $username, string $password): array
    {
        $username = trim($username);

        // Akun petugas dan mahasiswa memakai satu form login.
        $user = $this->petugas->getByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            $identity = [
                'id' => (int) $user['id_petugas'],
                'nama' => $user['nama_petugas'],
                'username' => $user['username'],
                'role' => $user['role'],
            ];
        } else {
            $identity = $this->loginAsMember($username, $password);
        }

        $this->startSession();
        session_regenerate_id(true);
        $_SESSION['user'] = $identity;

        return $identity;
    }

    public function logout(): void
    {
        $this->startSession();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $cookie = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httponly']
            );
        }

        session_destroy();
    }

    public function getProfile(int $id, string $role): array|false
    {
        if (in_array($role, ['admin', 'petugas'], true)) {
            $sql = 'SELECT id_petugas AS id, nama_petugas AS nama, username, role, no_hp
                    FROM petugas
                    WHERE id_petugas = :id';
        } else {
            $sql = 'SELECT id_anggota AS id, nama, username, alamat, no_hp, tanggal_daftar
                    FROM anggota
                    WHERE id_anggota = :id';
        }

        $query = $this->db->prepare($sql);
        $query->execute(['id' => $id]);

        return $query->fetch();
    }

    public function updateMember(int $id, array $data): bool
    {
        $username = trim($data['username'] ?? '');

        if (!$this->isValidUsername($username)) {
            throw new InvalidArgumentException(
                'Username harus 3 sampai 50 karakter. Gunakan huruf, angka, titik, garis bawah, atau tanda hubung.'
            );
        }

        $sql = 'SELECT 1
                FROM anggota
                WHERE username = :member_username AND id_anggota <> :id
                UNION ALL
                SELECT 1
                FROM petugas
                WHERE username = :staff_username
                LIMIT 1';
        $query = $this->db->prepare($sql);
        $query->execute([
            'member_username' => $username,
            'id' => $id,
            'staff_username' => $username,
        ]);

        if ($query->fetchColumn()) {
            throw new DomainException('Username sudah digunakan.');
        }

        return $this->anggota->update($id, $data);
    }

    private function validateRegistration(
        string $nama,
        string $alamat,
        string $noHp,
        string $username,
        string $password
    ): void {
        if ($nama === '' || $alamat === '' || $noHp === '' || $username === '') {
            throw new InvalidArgumentException('Semua data anggota wajib diisi.');
        }

        if (!$this->isValidUsername($username)) {
            throw new InvalidArgumentException(
                'Username harus 3 sampai 50 karakter. Gunakan huruf, angka, titik, garis bawah, atau tanda hubung.'
            );
        }

        if (!preg_match('/^[0-9+() -]{8,15}$/', $noHp)) {
            throw new InvalidArgumentException('Nomor telepon tidak valid.');
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password minimal 8 karakter.');
        }
    }

    private function isValidUsername(string $username): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username);
    }

    private function loginAsMember(string $username, string $password): array
    {
        $sql = 'SELECT id_anggota, nama, username, password_hash
                FROM anggota
                WHERE username = :username AND deleted_at IS NULL
                LIMIT 1';
        $query = $this->db->prepare($sql);
        $query->execute(['username' => $username]);
        $member = $query->fetch();

        if (!$member || !password_verify($password, $member['password_hash'])) {
            throw new RuntimeException('Username atau password tidak sesuai.');
        }

        return [
            'id' => (int) $member['id_anggota'],
            'nama' => $member['nama'],
            'username' => $member['username'],
            'role' => 'mahasiswa',
        ];
    }

    private function assertUsernameAvailable(string $username): void
    {
        $sql = 'SELECT 1 FROM anggota WHERE username = :member_username
                UNION ALL
                SELECT 1 FROM petugas WHERE username = :staff_username
                LIMIT 1';
        $query = $this->db->prepare($sql);
        $query->execute([
            'member_username' => $username,
            'staff_username' => $username,
        ]);

        if ($query->fetchColumn()) {
            throw new DomainException('Username sudah digunakan.');
        }
    }

    private function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
