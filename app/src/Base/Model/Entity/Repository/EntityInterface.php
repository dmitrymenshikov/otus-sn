<?php

declare(strict_types=1);

namespace App\Base\Model\Entity\Repository;

use App\Base\Model\DTO\EntityDtoInterface;

interface EntityInterface
{
    public static function create(EntityDtoInterface $dto): EntityInterface;
}