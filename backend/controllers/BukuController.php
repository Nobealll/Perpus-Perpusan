<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Buku.php';
require_once __DIR__ . '/../services/BukuImageUploader.php';

class BukuController
{
    private Buku $model;
    private BukuImageUploader $imageUploader;

    public function __construct(PDO $db)
    {
        $this->model = new Buku($db);
        $this->imageUploader = new BukuImageUploader();
    }

    public function index(string $search = ''): array
    {
        return $this->model->getAll(trim($search));
    }

    public function available(): array
    {
        return $this->model->getAvailable();
    }

    public function show(int $id): array|false
    {
        return $this->model->getById($id);
    }

    public function store(array $data, ?array $image = null): int
    {
        // Simpan gambar dahulu, lalu hapus lagi jika penyimpanan data gagal.
        $newImagePath = $this->imageUploader->save($image);
        $data['gambar'] = $newImagePath;

        try {
            return $this->model->create($data);
        } catch (Throwable $exception) {
            $this->imageUploader->delete($newImagePath);
            throw $exception;
        }
    }

    public function update(int $id, array $data, ?array $image = null): bool
    {
        $oldBook = $this->model->getById($id);
        if (!$oldBook) {
            throw new RuntimeException('Buku yang akan diubah tidak ditemukan.');
        }

        // Jika tidak ada gambar baru, gunakan kembali path gambar lama.
        $newImagePath = $this->imageUploader->save($image);
        $data['gambar'] = $newImagePath ?? $oldBook['gambar'];

        try {
            $updated = $this->model->update($id, $data);
        } catch (Throwable $exception) {
            $this->imageUploader->delete($newImagePath);
            throw $exception;
        }

        // Hapus sampul lama hanya setelah perubahan database berhasil.
        if ($updated && $newImagePath !== null) {
            $this->imageUploader->delete($oldBook['gambar']);
        }

        return $updated;
    }

    public function destroy(int $id): bool
    {
        $book = $this->model->getById($id);
        if (!$book) {
            return false;
        }

        // Relasi transaksi dapat menolak penghapusan buku; jangan hapus gambar dulu.
        $deleted = $this->model->delete($id);
        if ($deleted) {
            $this->imageUploader->delete($book['gambar']);
        }

        return $deleted;
    }
}
