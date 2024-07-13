<?php

declare(strict_types=1);

namespace App\City\Persistent\Repository;

use App\Base\Persistent\Repository\AbstractBaseRepository;
use App\City\Model\Entity\City;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

class CityRepository extends AbstractBaseRepository
{
    protected Connection $conn;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);

        $this->conn = $registry->getConnection();
    }

    /**
     * @throws Exception
     * @return City[]|null
     */
    public function fetchAll(): ?array
    {
        $sql = /** @lang PostgresSQL */
            'SELECT
                *
            FROM
               "cities" AS c';

        $result = $this->conn->executeQuery($this->sanitizeSql($sql));

        if ($result->rowCount() === 0) {
            return null;
        }

        return array_map(function($cityData) {
            return City::createFromArray($cityData['uuid'], $cityData['name']);
        }, $result->fetchAllAssociative());
    }

    /**
     * @throws Exception
     */
    public function getCityByUuid(string $uuid): ?City
    {
        $sql = /** @lang PostgresSQL */
            'SELECT
                c.uuid,
                c.name
            FROM
               "city" AS c
            WHERE
                c.uuid = :uuid';

        $result = $this->conn->executeQuery($this->sanitizeSql($sql), ['uuid' => $uuid]);

        if ($result->rowCount()) {
            $userData = $result->fetchAssociative();

            return City::createFromArray(...$userData);
        }

        return null;
    }
}