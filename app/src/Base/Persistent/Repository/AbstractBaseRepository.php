<?php

declare(strict_types=1);

namespace App\Base\Persistent\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractBaseRepository extends ServiceEntityRepository
{
    protected function sanitizeSql(string $sql): string
    {
        return preg_replace("/\r|\n/", '', preg_replace('/\s+/', ' ', $sql));
    }
}