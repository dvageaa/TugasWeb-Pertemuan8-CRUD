<?php
/**
 * config/database.php
 * Koneksi database menggunakan PDO dengan SINGLETON PATTERN.
 * Constructor bersifat private supaya class ini tidak bisa
 * di-instantiate langsung (new Database()) — satu-satunya cara
 * mendapatkan koneksi adalah lewat Database::getInstance().
 */

class Database
{
    /** @var PDO|null instance tunggal yang dipakai bersama di seluruh aplikasi */
    private static ?PDO $instance = null;

    // Konfigurasi koneksi — sesuaikan dengan environment kamu (XAMPP/Laragon)
    private const HOST    = 'localhost';
    private const PORT    = '8111';
    private const DBNAME  = 'inventaris_db';
    private const USER    = 'root';
    private const PASS    = '';
    private const CHARSET = 'utf8mb4';

    // Private constructor -> mencegah instansiasi dari luar class
    private function __construct()
    {
    }

    // Private clone -> mencegah instance digandakan
    private function __clone()
    {
    }

    // Mencegah instance dibuat ulang lewat unserialize()
    public function __wakeup()
    {
        throw new Exception('Tidak boleh unserialize singleton Database.');
    }

    /**
     * Satu-satunya pintu masuk untuk mendapatkan koneksi PDO.
     * Jika instance belum ada, buat sekali saja lalu simpan (lazy init).
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . self::HOST
                . ';dbname=' . self::DBNAME
                . ';charset=' . self::CHARSET;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // pakai native prepared statement
            ];

            try {
                self::$instance = new PDO($dsn, self::USER, self::PASS, $options);
            } catch (PDOException $e) {
                // Jangan bocorkan detail koneksi ke user, cukup log & pesan umum
                die('Koneksi database gagal. Pastikan MySQL aktif dan database "inventaris_db" sudah dibuat (import schema.sql). Detail: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
