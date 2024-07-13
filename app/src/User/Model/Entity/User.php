<?php

declare(strict_types=1);

namespace App\User\Model\Entity;

use App\User\Model\Enum\GenderEnum;
use App\User\Model\Form\DTO\UserRegisterTypeDTO;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class User implements UserInterface, PasswordAuthenticatedUserInterface, \JsonSerializable
{
    public function __construct(
        public Uuid $uuid,
        public string $email,
        public string $password,
        public string $firstname,
        public string $lastname,
        public DateTime $birthday,
        public GenderEnum $gender,
        public string $city,
        public string $about
    )
    {}

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getBirthday(): DateTime
    {
        return $this->birthday;
    }

    public function getGender(): GenderEnum
    {
        return $this->gender;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getAbout(): string
    {
        return $this->about;
    }

    public static function create(UserRegisterTypeDTO $dto): self
    {
        return new self(
            Uuid::v7(),
            $dto->email,
            $dto->password,
            $dto->firstname,
            $dto->lastname,
            $dto->birthday,
            $dto->gender,
            $dto->city,
            $dto->about
        );
    }

    public static function createFromArray(
        string $uuid,
        string $email,
        string $password,
        string $firstname,
        string $lastname,
        string $birthday,
        int $gender,
        string $city,
        string $about
    ): self
    {
        return new self(
            Uuid::fromString($uuid),
            $email,
            $password,
            $firstname,
            $lastname,
            \DateTime::createFromFormat('Y-m-d', $birthday),
            GenderEnum::from($gender),
            $city,
            $about
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->getUuid(),
            'email' => $this->getEmail(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'birthday' => $this->getBirthday()->format('d.m.Y'),
            'gender' => $this->getGender()->name,
            'city' => $this->getCity(),
            'about' => $this->getAbout()
        ];
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {

    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}