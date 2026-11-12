<?php

declare(strict_types=1);

/**
 * Membuat koneksi PDO ke database perpustakaan.
 * Sesuaikan kredensial jika konfigurasi MySQL Laragon Anda berbeda.
 */
class Database
{
    private string $host = 'localhost';
    private string $database = 'db_perpustakaan';
    private string $username = 'root';
    private string $password = '';
    private ?PDO $connection = null;

    public function getConnection(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $this->host,
            $this->database
        );

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            error_log('Koneksi database gagal: ' . $exception->getMessage());
            throw new RuntimeException(
                'Koneksi ke database gagal. Periksa layanan MySQL dan konfigurasi database.',
                0,
                $exception
            );
        }

        return $this->connection;
    }
}
