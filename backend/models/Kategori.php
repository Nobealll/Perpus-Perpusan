<?php

declare(strict_types=1);

/** Query dan perubahan kategori buku. */
class Kategori
{
    public function __construct(private PDO $db) {}

    public function getAll(): array
    {
        $sql = 'SELECT id_kategori, nama_kategori, deskripsi
                FROM kategori
                ORDER BY nama_kategori';

        return $this->db->query($sql)->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $sql = 'SELECT id_kategori, nama_kategori, deskripsi
                FROM kategori
                WHERE id_kategori = :id';
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $id]);

        return $query->fetch();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO kategori (nama_kategori, deskripsi)
                VALUES (:nama, :deskripsi)';
        $query = $this->db->prepare($sql);
        $query->execute([
            'nama' => trim($data['nama_kategori']),
            'deskripsi' => trim($data['deskripsi'] ?? ''),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE kategori
                SET nama_kategori = :nama,
                    deskripsi = :deskripsi
                WHERE id_kategori = :id';
        $query = $this->db->prepare($sql);

        return $query->execute([
            'nama' => trim($data['nama_kategori']),
            'deskripsi' => trim($data['deskripsi'] ?? ''),
            'id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $query = $this->db->prepare(
            'DELETE FROM kategori WHERE id_kategori = :id'
        );

        return $query->execute(['id' => $id]);
    }
}
