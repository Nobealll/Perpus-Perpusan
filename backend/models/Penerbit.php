<?php

declare(strict_types=1);

/** Query dan perubahan data penerbit buku. */
class Penerbit
{
    public function __construct(private PDO $db) {}

    public function getAll(): array
    {
        $sql = 'SELECT id_penerbit, nama_penerbit, alamat, no_telepon
                FROM penerbit
                ORDER BY nama_penerbit';

        return $this->db->query($sql)->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $sql = 'SELECT id_penerbit, nama_penerbit, alamat, no_telepon
                FROM penerbit
                WHERE id_penerbit = :id';
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $id]);

        return $query->fetch();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO penerbit (nama_penerbit, alamat, no_telepon)
                VALUES (:nama, :alamat, :telepon)';
        $query = $this->db->prepare($sql);
        $query->execute([
            'nama' => trim($data['nama_penerbit']),
            'alamat' => trim($data['alamat']),
            'telepon' => trim($data['no_telepon']),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE penerbit
                SET nama_penerbit = :nama,
                    alamat = :alamat,
                    no_telepon = :telepon
                WHERE id_penerbit = :id';
        $query = $this->db->prepare($sql);

        return $query->execute([
            'nama' => trim($data['nama_penerbit']),
            'alamat' => trim($data['alamat']),
            'telepon' => trim($data['no_telepon']),
            'id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $query = $this->db->prepare(
            'DELETE FROM penerbit WHERE id_penerbit = :id'
        );

        return $query->execute(['id' => $id]);
    }
}
