<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Kategori.php';

/** Meneruskan permintaan halaman kategori ke model Kategori. */
class KategoriController
{
    private Kategori $model;

    public function __construct(PDO $db)
    {
        $this->model = new Kategori($db);
    }

    public function index(): array
    {
        return $this->model->getAll();
    }

    public function show(int $id): array|false
    {
        return $this->model->getById($id);
    }

    public function store(array $data): int
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function destroy(int $id): bool
    {
        return $this->model->delete($id);
    }
}
