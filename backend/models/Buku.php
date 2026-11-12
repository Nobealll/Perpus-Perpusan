<?php

declare(strict_types=1);

class Buku
{
    public function __construct(private PDO $db) {}

    public function getAll(string $search = ''): array
    {
        $sql = 'SELECT b.*, k.nama_kategori, p.nama_penerbit
                FROM buku b
                JOIN kategori k ON k.id_kategori = b.id_kategori
                JOIN penerbit p ON p.id_penerbit = b.id_penerbit';
        $parameters = [];

        // Pencarian katalog hanya mencocokkan judul atau nama penulis.
        if ($search !== '') {
            $sql .= ' WHERE b.judul LIKE :judul OR b.penulis LIKE :penulis';
            $term = '%' . $search . '%';
            $parameters = ['judul' => $term, 'penulis' => $term];
        }

        $sql .= ' ORDER BY b.judul';
        $query = $this->db->prepare($sql);
        $query->execute($parameters);
        return $query->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $sql = 'SELECT b.*, k.nama_kategori, p.nama_penerbit
                FROM buku b
                JOIN kategori k ON k.id_kategori = b.id_kategori
                JOIN penerbit p ON p.id_penerbit = b.id_penerbit
                WHERE b.id_buku = :id';
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->fetch();
    }

    public function getAvailable(): array
    {
        return $this->getAll();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO buku
                (judul, penulis, isbn, deskripsi, gambar, rak, tahun_terbit, id_kategori, id_penerbit, stok)
                VALUES
                (:judul, :penulis, :isbn, :deskripsi, :gambar, :rak, :tahun, :kategori, :penerbit, :stok)';
        $query = $this->db->prepare($sql);
        $query->execute($this->parameters($data));
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE buku SET
                    judul = :judul,
                    penulis = :penulis,
                    isbn = :isbn,
                    deskripsi = :deskripsi,
                    gambar = :gambar,
                    rak = :rak,
                    tahun_terbit = :tahun,
                    id_kategori = :kategori,
                    id_penerbit = :penerbit,
                    stok = :stok
                WHERE id_buku = :id';
        $parameters = $this->parameters($data);
        $parameters['id'] = $id;

        $query = $this->db->prepare($sql);
        return $query->execute($parameters);
    }

    public function delete(int $id): bool
    {
        $query = $this->db->prepare('DELETE FROM buku WHERE id_buku = :id');
        return $query->execute(['id' => $id]);
    }

    private function parameters(array $data): array
    {
        // Ubah data formulir menjadi nilai yang sudah dibersihkan dan divalidasi.
        $judul = trim($data['judul'] ?? '');
        $penulis = trim($data['penulis'] ?? '');
        $isbn = trim($data['isbn'] ?? '');
        $tahun = filter_var($data['tahun_terbit'] ?? null, FILTER_VALIDATE_INT);
        $kategori = filter_var($data['id_kategori'] ?? null, FILTER_VALIDATE_INT);
        $penerbit = filter_var($data['id_penerbit'] ?? null, FILTER_VALIDATE_INT);
        $stok = filter_var($data['stok'] ?? null, FILTER_VALIDATE_INT);

        if ($judul === '' || mb_strlen($judul) > 150) {
            throw new InvalidArgumentException('Judul wajib diisi dan maksimal 150 karakter.');
        }
        if ($penulis === '' || mb_strlen($penulis) > 100) {
            throw new InvalidArgumentException('Nama penulis wajib diisi dan maksimal 100 karakter.');
        }
        if ($isbn === '' || strlen($isbn) > 20) {
            throw new InvalidArgumentException('ISBN wajib diisi dan maksimal 20 karakter.');
        }
        if ($tahun === false || $tahun < 1901 || $tahun > 2155) {
            throw new InvalidArgumentException('Tahun terbit tidak valid.');
        }
        if (
            $kategori === false
            || $kategori < 1
            || $penerbit === false
            || $penerbit < 1
        ) {
            throw new InvalidArgumentException('Pilih kategori dan penerbit yang valid.');
        }
        if ($stok === false || $stok < 0) {
            throw new InvalidArgumentException('Stok harus berupa angka nol atau lebih.');
        }

        $deskripsi = trim($data['deskripsi'] ?? '');
        if (mb_strlen($deskripsi) > 10000) {
            throw new InvalidArgumentException('Deskripsi buku maksimal 10.000 karakter.');
        }
        $gambar = $data['gambar'] ?? null;
        $rak = trim($data['rak'] ?? '');

        if ($rak === '' || mb_strlen($rak) > 50) {
            throw new InvalidArgumentException(
                'Lokasi rak wajib diisi dan maksimal 50 karakter.'
            );
        }

        return [
            'judul' => $judul,
            'penulis' => $penulis,
            'isbn' => $isbn,
            'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
            'gambar' => $gambar !== '' ? $gambar : null,
            'rak' => $rak,
            'tahun' => $tahun,
            'kategori' => $kategori,
            'penerbit' => $penerbit,
            'stok' => $stok,
        ];
    }
}
