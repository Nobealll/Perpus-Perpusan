<?php

declare(strict_types=1);

/** Data transaksi peminjaman dan pengembalian buku. */
class Peminjaman
{
    private const DENDA_PER_HARI = 5000;

    public function __construct(private PDO $db) {}

    public function getAll(): array
    {
        $sql = 'SELECT pm.*, a.nama AS nama_anggota,
                    b.judul AS judul_buku, pt.nama_petugas
                FROM peminjaman pm
                JOIN anggota a ON a.id_anggota = pm.id_anggota
                JOIN buku b ON b.id_buku = pm.id_buku
                JOIN petugas pt ON pt.id_petugas = pm.id_petugas
                ORDER BY pm.id_peminjaman DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function getByAnggota(int $idAnggota): array
    {
        $sql = 'SELECT pm.*, b.judul AS judul_buku
                FROM peminjaman pm
                JOIN buku b ON b.id_buku = pm.id_buku
                WHERE pm.id_anggota = :id
                ORDER BY pm.id_peminjaman DESC';
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $idAnggota]);

        return $query->fetchAll();
    }

    public function getAktif(): array
    {
        $sql = "SELECT pm.*, a.nama AS nama_anggota, b.judul AS judul_buku
                FROM peminjaman pm
                JOIN anggota a ON a.id_anggota = pm.id_anggota
                JOIN buku b ON b.id_buku = pm.id_buku
                WHERE pm.status = 'dipinjam'
                ORDER BY pm.tanggal_kembali";

        return $this->db->query($sql)->fetchAll();
    }

    public function pinjamBuku(
        int $idAnggota,
        int $idBuku,
        ?int $idPetugas = null,
        int $durasiHari = 7
    ): int {
        if ($durasiHari < 1) {
            throw new InvalidArgumentException(
                'Durasi peminjaman harus lebih dari 0 hari.'
            );
        }

        try {
            $this->db->beginTransaction();

            // Kunci baris buku agar stok tidak dipinjam bersamaan hingga habis.
            $book = $this->getBookForUpdate($idBuku);
            if (!$book || (int) $book['stok'] < 1) {
                throw new DomainException(
                    'Buku tidak ditemukan atau stok sedang habis.'
                );
            }

            // Peminjaman mahasiswa dicatat atas nama petugas pertama jika belum dipilih.
            if ($idPetugas === null) {
                $idPetugas = $this->getFirstStaffId();
            }

            $tanggalPinjam = new DateTimeImmutable('today');
            $tanggalKembali = $tanggalPinjam->modify('+' . $durasiHari . ' days');

            $this->insertLoan(
                $idAnggota,
                $idBuku,
                $idPetugas,
                $tanggalPinjam,
                $tanggalKembali
            );
            $this->decreaseBookStock($idBuku);

            $idPeminjaman = (int) $this->db->lastInsertId();
            $this->db->commit();

            return $idPeminjaman;
        } catch (Throwable $exception) {
            $this->rollbackIfNeeded();
            throw $exception;
        }
    }

    public function kembalikanBuku(
        int $idPeminjaman,
        ?DateTimeImmutable $tanggalAktual = null
    ): array {
        $tanggalAktual ??= new DateTimeImmutable('today');

        try {
            $this->db->beginTransaction();

            // Ambil dan kunci transaksi supaya tidak dikembalikan dua kali.
            $loan = $this->getLoanForUpdate($idPeminjaman);
            if (!$loan || $loan['status'] !== 'dipinjam') {
                throw new DomainException(
                    'Transaksi tidak ditemukan atau sudah dikembalikan.'
                );
            }

            $hariTerlambat = $this->hitungHariTerlambat(
                $loan['tanggal_kembali'],
                $tanggalAktual
            );
            $denda = $this->hitungDenda($hariTerlambat);

            $this->markLoanReturned(
                $idPeminjaman,
                $tanggalAktual,
                $denda
            );
            $this->increaseBookStock((int) $loan['id_buku']);

            $this->db->commit();

            return [
                'hari_terlambat' => $hariTerlambat,
                'denda' => $denda,
            ];
        } catch (Throwable $exception) {
            $this->rollbackIfNeeded();
            throw $exception;
        }
    }

    public function hitungDenda(int $hariTerlambat): float
    {
        $hariYangDihitung = max(0, $hariTerlambat);

        return (float) ($hariYangDihitung * self::DENDA_PER_HARI);
    }

    private function getBookForUpdate(int $idBuku): array|false
    {
        $query = $this->db->prepare(
            'SELECT stok FROM buku WHERE id_buku = :id FOR UPDATE'
        );
        $query->execute(['id' => $idBuku]);

        return $query->fetch();
    }

    private function getFirstStaffId(): int
    {
        $sql = 'SELECT id_petugas
                FROM petugas
                ORDER BY id_petugas
                LIMIT 1';
        $idPetugas = (int) $this->db->query($sql)->fetchColumn();

        if ($idPetugas < 1) {
            throw new DomainException(
                'Belum ada petugas terdaftar untuk mencatat peminjaman.'
            );
        }

        return $idPetugas;
    }

    private function insertLoan(
        int $idAnggota,
        int $idBuku,
        int $idPetugas,
        DateTimeImmutable $tanggalPinjam,
        DateTimeImmutable $tanggalKembali
    ): void {
        $sql = "INSERT INTO peminjaman
                    (id_anggota, id_buku, id_petugas, tanggal_pinjam, tanggal_kembali, status)
                VALUES
                    (:anggota, :buku, :petugas, :pinjam, :kembali, 'dipinjam')";
        $query = $this->db->prepare($sql);
        $query->execute([
            'anggota' => $idAnggota,
            'buku' => $idBuku,
            'petugas' => $idPetugas,
            'pinjam' => $tanggalPinjam->format('Y-m-d'),
            'kembali' => $tanggalKembali->format('Y-m-d'),
        ]);
    }

    private function decreaseBookStock(int $idBuku): void
    {
        $query = $this->db->prepare(
            'UPDATE buku SET stok = stok - 1 WHERE id_buku = :id'
        );
        $query->execute(['id' => $idBuku]);
    }

    private function getLoanForUpdate(int $idPeminjaman): array|false
    {
        $sql = 'SELECT id_buku, tanggal_kembali, status
                FROM peminjaman
                WHERE id_peminjaman = :id
                FOR UPDATE';
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $idPeminjaman]);

        return $query->fetch();
    }

    private function markLoanReturned(
        int $idPeminjaman,
        DateTimeImmutable $tanggalAktual,
        float $denda
    ): void {
        $sql = "UPDATE peminjaman
                SET tanggal_pengembalian_aktual = :aktual,
                    denda = :denda,
                    status = 'dikembalikan'
                WHERE id_peminjaman = :id";
        $query = $this->db->prepare($sql);
        $query->execute([
            'aktual' => $tanggalAktual->format('Y-m-d'),
            'denda' => $denda,
            'id' => $idPeminjaman,
        ]);
    }

    private function increaseBookStock(int $idBuku): void
    {
        $query = $this->db->prepare(
            'UPDATE buku SET stok = stok + 1 WHERE id_buku = :id'
        );
        $query->execute(['id' => $idBuku]);
    }

    private function hitungHariTerlambat(
        string $tanggalJatuhTempo,
        DateTimeImmutable $tanggalAktual
    ): int {
        $jatuhTempo = new DateTimeImmutable($tanggalJatuhTempo);

        if ($tanggalAktual <= $jatuhTempo) {
            return 0;
        }

        return (int) $jatuhTempo->diff($tanggalAktual)->days;
    }

    private function rollbackIfNeeded(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }
}
