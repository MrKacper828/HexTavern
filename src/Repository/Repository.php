<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database;
use PDO;

abstract class Repository
{
    protected PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection ?? Database::getInstance()->getConnection();
    }
}
