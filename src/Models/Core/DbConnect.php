<?php
namespace App\Models\Core;

use PDO;
use PDOException;
use Dotenv\Dotenv;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';


class DbConnect
{
    private static ?PDO $connection = null;

    public function __construct()
    {
        $this->connect();
    }

    private function connect(): void
    {
        $dotenvPath = dirname(__DIR__, 3) . '/.env'; 
        if (file_exists($dotenvPath)) {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 3));  
            $dotenv->load();
        } else {
            die('No .env file found.');
        }
        $host = $_ENV['DB_HOST'] ?? ''; 
        $dbname = $_ENV['DB_NAME'] ?? '';
        $user = $_ENV['DB_USER'] ?? '';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=$charset";

        try {
            self::$connection = new PDO($dsn, $user, $password);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getConnection(): ?PDO
    {
        if (self::$connection === null) new self(); 
        return self::$connection;
    }
}

