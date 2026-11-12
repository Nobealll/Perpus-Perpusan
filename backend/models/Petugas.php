<?php

declare(strict_types=1);

/** Mengambil data akun petugas. */
class Petugas
{
    public function __construct(private PDO $db) {}

    public function getByUsername(string $username): array|false
    {
        $sql = 'SELECT id_petugas, nama_petugas, username, password_hash, role, no_hp
                FROM petugas
                WHERE username = :username
                LIMIT 1';
        $query = $this->db->prepare($sql);
        $query->execute(['username' => trim($username)]);

        return $query->fetch();
    }

    public function getAll(): array
    {
        $sql = 'SELECT id_petugas, nama_petugas, username, role, no_hp
                FROM petugas
                ORDER BY id_petugas';

        return $this->db->query($sql)->fetchAll();
    }
}
