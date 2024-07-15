<?php

namespace App\Base\Model\Entity;

use App\Base\Model\DTO\EntityDtoInterface;
use App\Base\Model\Entity\Repository\EntityInterface;
use App\Base\Model\Exception\DataIsNotConsistentException;
use Exception;

abstract class AbstractEntity implements EntityInterface
{
    protected array $entityCreateFields = [];

    /**
     * @throws DataIsNotConsistentException
     */
    public static function create(EntityDtoInterface $dto): EntityInterface
    {
        $entity = new static();

        $entity->fill($dto);

        return $entity;
    }

    /**
     * @throws DataIsNotConsistentException
     */
    protected function fill(EntityDtoInterface $dto): void
    {
        try {
            foreach ($this->getEntityCreateFields() as $fieldName) {
                $setter = 'set' . ucfirst($fieldName);
                $getter = 'get' . ucfirst($fieldName);

                $this->$setter($dto->$getter());
            }
        } catch (Exception $e) {
            throw new DataIsNotConsistentException($e);
        }
    }

    protected function getEntityCreateFields(): array
    {
        return $this->entityCreateFields;
    }
}