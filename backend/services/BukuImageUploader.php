<?php

declare(strict_types=1);

class BukuImageUploader
{
    private const MAX_SIZE = 2 * 1024 * 1024;
    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private string $directory;

    public function __construct()
    {
        $this->directory = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'buku';
    }

    /** Mengembalikan null jika pengguna tidak memilih file. */
    public function save(?array $file): ?string
    {
        // Form edit boleh dikirim tanpa memilih gambar baru.
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Gambar gagal diunggah. Silakan pilih ulang file.');
        }

        $temporaryFile = $file['tmp_name'] ?? '';
        if (!is_uploaded_file($temporaryFile)) {
            throw new RuntimeException('File gambar yang diterima tidak valid.');
        }

        $actualSize = filesize($temporaryFile);
        if (($file['size'] ?? 0) > self::MAX_SIZE || $actualSize > self::MAX_SIZE) {
            throw new InvalidArgumentException('Ukuran gambar maksimal 2 MB.');
        }

        // Tentukan tipe file dari isinya, bukan dari nama yang dikirim pengguna.
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($temporaryFile);
        if (!isset(self::ALLOWED_TYPES[$mimeType])) {
            throw new InvalidArgumentException('Gunakan gambar JPG, PNG, atau WEBP.');
        }

        if (!is_dir($this->directory) && !mkdir($this->directory, 0755, true) && !is_dir($this->directory)) {
            throw new RuntimeException('Folder penyimpanan gambar tidak dapat dibuat.');
        }

        // Nama acak mencegah nama file bentrok dan mengabaikan nama asli file.
        $fileName = bin2hex(random_bytes(16)) . '.' . self::ALLOWED_TYPES[$mimeType];
        $destination = $this->directory . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($temporaryFile, $destination)) {
            throw new RuntimeException('Gambar tidak berhasil disimpan.');
        }

        return 'uploads/buku/' . $fileName;
    }

    public function delete(?string $relativePath): void
    {
        // Hanya hapus path yang memang berada di folder sampul buku.
        if (!$relativePath || !str_starts_with($relativePath, 'uploads/buku/')) {
            return;
        }

        $fileName = basename($relativePath);
        if (
            $relativePath !== 'uploads/buku/' . $fileName
            || !preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $fileName)
        ) {
            return;
        }

        $filePath = $this->directory . DIRECTORY_SEPARATOR . $fileName;
        if (is_file($filePath) && !is_link($filePath)) {
            unlink($filePath);
        }
    }
}
