<?php

declare(strict_types=1);

namespace App\City\Model\Entity;

use Symfony\Component\Uid\Uuid;

class City
{
    public function __construct(
        public Uuid $uuid,
        public string $name
    )
    {}

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public static function createFromArray(
        string $uuid,
        string $name
    ): self
    {
        return new self(
            Uuid::fromString($uuid),
            $name
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->getUuid(),
            'name' => $this->getName()
        ];
    }
}