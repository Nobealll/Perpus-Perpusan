<?php

declare(strict_types=1);

/** Mengambil angka ringkasan untuk dashboard mahasiswa dan petugas. */
class DashboardController
{
    public function __construct(private PDO $db) {}

    public function petugas(): array
    {
        return [
            'anggota' => $this->count('SELECT COUNT(*) FROM anggota WHERE deleted_at IS NULL'),
            'buku' => $this->count('SELECT COUNT(*) FROM buku'),
            'kategori' => $this->count('SELECT COUNT(*) FROM kategori'),
            'peminjaman' => $this->count('SELECT COUNT(*) FROM peminjaman'),
            'dipinjam' => $this->count(
                "SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'"
            ),
            'terlambat' => $this->count(
                "SELECT COUNT(*) FROM peminjaman
                 WHERE status = 'dipinjam' AND tanggal_kembali < CURDATE()"
            ),
        ];
    }

    public function mahasiswa(int $idAnggota): array
    {
        $jumlahBukuTersedia = (int) $this->db
            ->query('SELECT COALESCE(SUM(stok), 0) FROM buku')
            ->fetchColumn();

        $jumlahSedangDipinjam = $this->countForMember(
            "SELECT COUNT(*) FROM peminjaman
             WHERE id_anggota = :id AND status = 'dipinjam'",
            $idAnggota
        );

        $riwayat = $this->getMemberHistory($idAnggota);
        $totalDenda = $this->getMemberFines($idAnggota);

        return [
            'buku_tersedia' => $jumlahBukuTersedia,
            'dipinjam' => $jumlahSedangDipinjam,
            'riwayat' => $riwayat,
            'total_denda' => $totalDenda,
        ];
    }

    private function count(string $sql): int
    {
        return (int) $this->db->query($sql)->fetchColumn();
    }

    private function countForMember(string $sql, int $idAnggota): int
    {
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $idAnggota]);

        return (int) $query->fetchColumn();
    }

    private function getMemberHistory(int $idAnggota): array
    {
        $sql = 'SELECT pm.*, b.judul AS judul_buku
                FROM peminjaman pm
                JOIN buku b ON b.id_buku = pm.id_buku
                WHERE pm.id_anggota = :id
                ORDER BY pm.id_peminjaman DESC
                LIMIT 5';
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $idAnggota]);

        return $query->fetchAll();
    }

    private function getMemberFines(int $idAnggota): float
    {
        $sql = "SELECT COALESCE(SUM(denda), 0)
                FROM peminjaman
                WHERE id_anggota = :id AND status = 'dikembalikan'";
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $idAnggota]);

        return (float) $query->fetchColumn();
    }
}
