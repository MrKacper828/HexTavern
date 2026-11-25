<?php

declare(strict_types=1);

namespace App;

use LogicException;
use PDO;
use RuntimeException;

final class Database
{
    private static ?self $instance = null;

    private PDO $connection;

    private function __construct()
    {
        $host = $this->environment('DB_HOST', 'db');
        $port = $this->environment('DB_PORT', '5432');
        $name = $this->requireEnvironment('DB_NAME');
        $user = $this->requireEnvironment('DB_USER');
        $password = $this->requireEnvironment('DB_PASSWORD');

        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $name);

        $this->connection = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    private function __clone(): void
    {
    }

    public function __wakeup(): never
    {
        throw new LogicException('Cannot unserialize Database singleton.');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    private function environment(string $name, string $default): string
    {
        $value = getenv($name);

        return $value === false || $value === '' ? $default : $value;
    }

    private function requireEnvironment(string $name): string
    {
        $value = getenv($name);

        if ($value === false || $value === '') {
            throw new RuntimeException(sprintf('Missing required database environment variable: %s', $name));
        }

        return $value;
    }
}
