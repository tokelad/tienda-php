<?php

namespace config;

use PDO;
use PDOException;
use Dotenv\Dotenv as DotenvAlias;

class Config
{
    private ?PDO $db = null;
    private string $uploadPath;
    private string $uploadUrl;
    private string $rootPath;
    private static ?Config $instance = null;

    private function __construct()
    {
        $this->rootPath = dirname(__DIR__, 2);
        $this->uploadPath = $this->rootPath . "/public/uploads/";
        $this->uploadUrl = "http://localhost:8080/uploads/";
    }

    public function connect(): PDO
    {
        if ($this->db === null) {
            $dotenv = DotenvAlias::createImmutable($this->rootPath);
            $dotenv->load();

            $dbHost     = $_ENV["POSTGRES_HOST"] ?? "localhost";
            $dbPort     = $_ENV["POSTGRES_PORT"] ?? "5432";
            $dbName     = $_ENV["POSTGRES_DB"] ?? "tienda";
            $dbUser     = $_ENV["POSTGRES_USER"] ?? "";
            $dbPassword = $_ENV["POSTGRES_PASSWORD"] ?? "";

            try {
                $this->db = new PDO(
                    "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName",
                    $dbUser,
                    $dbPassword,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                error_log("Error de conexión a la BD: " . $e->getMessage());
                die("No se puede conectar con la base de datos.");
            }
        }

        return $this->db;
    }

    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getUploadPath(): string
    {
        return $this->uploadPath;
    }

    public function getUploadUrl(): string
    {
        return $this->uploadUrl;
    }
}
