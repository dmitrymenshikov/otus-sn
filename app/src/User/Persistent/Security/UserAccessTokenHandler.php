<?php

declare(strict_types=1);

namespace App\User\Persistent\Security;

use App\User\Persistent\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

readonly class UserAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private UserRepository $repository
    ) {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $user = $this->repository->loadUserByToken($accessToken);

        if (null === $user) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($user->getUserIdentifier(), function () use ($user) {
            return $user;
        });
    }
}