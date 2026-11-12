<?php

declare(strict_types=1);

/** Query dan perubahan data anggota perpustakaan. */
class Anggota
{
    public function __construct(private PDO $db) {}

    public function getAll(): array
    {
        $sql = 'SELECT id_anggota, nama, alamat, no_hp, tanggal_daftar, username
                FROM anggota
                WHERE deleted_at IS NULL
                ORDER BY id_anggota DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $sql = 'SELECT id_anggota, nama, alamat, no_hp, tanggal_daftar, username
                FROM anggota
                WHERE id_anggota = :id AND deleted_at IS NULL';
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $id]);

        return $query->fetch();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO anggota
                    (nama, alamat, no_hp, tanggal_daftar, username, password_hash)
                VALUES
                    (:nama, :alamat, :no_hp, :tanggal_daftar, :username, :password_hash)';
        $query = $this->db->prepare($sql);
        $query->execute([
            'nama' => trim($data['nama']),
            'alamat' => trim($data['alamat']),
            'no_hp' => trim($data['no_hp']),
            'tanggal_daftar' => $data['tanggal_daftar'],
            'username' => trim($data['username']),
            'password_hash' => $data['password_hash'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE anggota
                SET nama = :nama,
                    alamat = :alamat,
                    no_hp = :no_hp,
                    username = :username
                WHERE id_anggota = :id AND deleted_at IS NULL';
        $query = $this->db->prepare($sql);

        return $query->execute([
            'nama' => trim($data['nama']),
            'alamat' => trim($data['alamat']),
            'no_hp' => trim($data['no_hp']),
            'username' => trim($data['username']),
            'id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $activeLoans = $this->db->prepare(
            "SELECT COUNT(*) FROM peminjaman
             WHERE id_anggota = :id AND status = 'dipinjam'"
        );
        $activeLoans->execute(['id' => $id]);

        if ((int) $activeLoans->fetchColumn() > 0) {
            throw new DomainException('Anggota masih memiliki buku yang belum dikembalikan.');
        }

        $query = $this->db->prepare(
            'UPDATE anggota SET deleted_at = NOW()
             WHERE id_anggota = :id AND deleted_at IS NULL'
        );

        return $query->execute(['id' => $id]);
    }
}
