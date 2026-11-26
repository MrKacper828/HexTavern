<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class CampaignRepository extends Repository
{
    public function findAllWithDm(): array
    {
        $statement = $this->connection->prepare(
            'SELECT
                campaigns.id,
                campaigns.name,
                campaigns.description,
                campaigns.room_code,
                campaigns.last_played_at,
                campaigns.created_at,
                users.id AS dm_id,
                users.username AS dm_username,
                users.email AS dm_email
            FROM campaigns
            INNER JOIN users ON users.id = campaigns.dm_id
            ORDER BY campaigns.last_played_at DESC NULLS LAST, campaigns.id ASC',
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findByIdWithDm(int $id): ?array
    {
        $statement = $this->connection->prepare(
            'SELECT
                campaigns.id,
                campaigns.name,
                campaigns.description,
                campaigns.room_code,
                campaigns.last_played_at,
                campaigns.created_at,
                users.id AS dm_id,
                users.username AS dm_username,
                users.email AS dm_email
            FROM campaigns
            INNER JOIN users ON users.id = campaigns.dm_id
            WHERE campaigns.id = :id',
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $campaign = $statement->fetch();

        return $campaign === false ? null : $campaign;
    }
}
