<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Peminjaman.php';

/** Menyediakan data dan tindakan peminjaman untuk halaman. */
class PeminjamanController
{
    private Peminjaman $model;

    public function __construct(PDO $db)
    {
        $this->model = new Peminjaman($db);
    }

    public function index(): array
    {
        return $this->model->getAll();
    }

    public function byAnggota(int $idAnggota): array
    {
        return $this->model->getByAnggota($idAnggota);
    }

    public function aktif(): array
    {
        return $this->model->getAktif();
    }

    public function pinjam(
        int $idAnggota,
        int $idBuku,
        ?int $idPetugas = null,
        int $durasiHari = 7
    ): int {
        return $this->model->pinjamBuku(
            $idAnggota,
            $idBuku,
            $idPetugas,
            $durasiHari
        );
    }
}
