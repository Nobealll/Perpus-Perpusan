<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Peminjaman.php';

/** Mengelola daftar buku aktif dan proses pengembaliannya. */
class PengembalianController
{
    private Peminjaman $model;

    public function __construct(PDO $db)
    {
        $this->model = new Peminjaman($db);
    }

    public function index(): array
    {
        return $this->model->getAktif();
    }

    public function kembalikan(
        int $idPeminjaman,
        ?DateTimeImmutable $tanggalAktual = null
    ): array {
        return $this->model->kembalikanBuku(
            $idPeminjaman,
            $tanggalAktual
        );
    }
}
