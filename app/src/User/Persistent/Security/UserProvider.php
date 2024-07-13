<?php

declare(strict_types=1);

namespace App\User\Persistent\Security;

use App\User\Model\Entity\User;
use App\User\Persistent\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(public UserRepository $userRepository)
    {}

    /**
     * @throws Exception
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        /** @var User $user */
        $user->setPassword($newHashedPassword);

        $this->userRepository->updatePassword($user, $newHashedPassword);
    }

    /**
     * @throws Exception
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        $refreshedUser = $this->userRepository->loadUserByIdentifier($user->getUserIdentifier());

        if (null === $refreshedUser) {
            throw new UserNotFoundException(\sprintf('User with id %s not found', \json_encode($user->getUserIdentifier())));
        }

        return $refreshedUser;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    /**
     * @throws Exception
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->userRepository->loadUserByIdentifier($identifier);
    }
}