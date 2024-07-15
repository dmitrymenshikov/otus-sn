<?php

declare(strict_types=1);

namespace App\User\Model\Exception;

use Exception;

class UserAlreadyExist extends Exception
{
    public const DEFAULT_MESSAGE = 'User with this name already exists.';

    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE);
    }
}
