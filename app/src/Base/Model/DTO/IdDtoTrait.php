<?php

declare(strict_types=1);

namespace App\Base\Model\DTO;

trait IdDtoTrait
{
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
