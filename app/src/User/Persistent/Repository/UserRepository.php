<?php

declare(strict_types=1);

namespace App\User\Persistent\Repository;

use App\Base\Persistent\Repository\AbstractBaseRepository;
use App\User\Model\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends AbstractBaseRepository
{
    protected Connection $conn;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);

        $this->conn = $registry->getConnection();
    }

    /**
     * @throws Exception
     */
    public function getUserByUuid(string $uuid): ?User
    {
        $sql = /** @lang PostgresSQL */
            'SELECT
                u.uuid,
                u.email,
                u.password,
                u.firstname,
                u.lastname,
                u.birthday,
                u.gender,
                c.name as "city",
                u.about
            FROM
               "user" AS u
            LEFT JOIN
               cities c on u.city = c.uuid
            WHERE
                u.uuid = :uuid';

        $result = $this->conn->executeQuery($this->sanitizeSql($sql), ['uuid' => $uuid]);

        if ($result->rowCount()) {
            $userData = $result->fetchAssociative();

            return User::createFromArray(...$userData);
        }

        return null;
    }

    /**
     * @throws Exception
     */
    public function loadUserByIdentifier(string $email): ?User
    {
        $sql = /** @lang PostgresSQL */
            'SELECT
                u.uuid,
                u.email,
                u.password,
                u.firstname,
                u.lastname,
                u.birthday,
                u.gender,
                c.name as "city",
                u.about
            FROM
               "user" AS u
            LEFT JOIN
               cities c on u.city = c.uuid
            WHERE
                email = :email;';

        $result = $this->conn->executeQuery($this->sanitizeSql($sql), ['email' => $email]);

        if (!$result->rowCount()) {
            return null;
        }

        $userData = $result->fetchAssociative();

        return User::createFromArray(...$userData);
    }

    /**
     * @throws Exception
     */
    public function loadUserByToken(string $token): ?User
    {
        $sql = /** @lang PostgresSQL */
            'SELECT
                u.uuid,
                u.email,
                u.password,
                u.firstname,
                u.lastname,
                u.birthday,
                u.gender,
                c.name as "city",
                u.about
            FROM
               "user" AS u
            LEFT JOIN
               cities c on u.city = c.uuid
            WHERE
                token = :token;';

        $result = $this->conn->executeQuery($this->sanitizeSql($sql), ['token' => $token]);

        if (!$result->rowCount()) {
            return null;
        }

        $userData = $result->fetchAssociative();

        return User::createFromArray(...$userData);
    }

    /**
     * @throws Exception
     */
    public function save(User $user): ?string
    {
        $sql = /** @lang PostgresSQL */
            'INSERT INTO
                "user" (
                    uuid,
                    email,
                    password,
                    firstname,
                    lastname,
                    birthday,
                    gender,
                    city,
                    about
                ) VALUES (
                    \''.$user->getUuid()->toString().'\',
                    \''.$user->getEmail().'\',
                    \''.$user->getPassword().'\',
                    \''.$user->getFirstname().'\',
                    \''.$user->getLastname().'\',
                    \''.$user->getBirthday()->format('Y-m-d').'\',
                    \''.$user->getGender()->value.'\',
                    \''.$user->getCity().'\',
                    \''.$user->getAbout().'\'
                )
            RETURNING uuid
            ;';

        $uuid = $this->conn->executeQuery($this->sanitizeSql($sql))->fetchOne();

        if (!$uuid) {
            return null;
        }

        return $uuid;
    }

    /**
     * @throws Exception
     */
    public function updateToken(User $user, string $newToken): void
    {
        $sql = /** @lang PostgresSQL */ 'UPDATE "user" SET token = :token WHERE uuid = :uuid;';

        $this->conn->executeQuery($this->sanitizeSql($sql), ['uuid' => $user->getUuid(), 'token' => $newToken]);
    }

    /**
     * @throws Exception
     */
    public function updatePassword(User $user, string $password): void
    {
        $sql = /** @lang PostgresSQL */ 'INSERT INTO "user" (password) VALUES (:pass) WHERE uuid = :uuid;';

        $this->conn->executeQuery($this->sanitizeSql($sql), ['uuid' => $user->getUuid(), 'pass' => $password]);
    }
}