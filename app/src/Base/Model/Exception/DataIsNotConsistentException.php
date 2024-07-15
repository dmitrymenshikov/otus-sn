<?php

declare(strict_types=1);

namespace App\Base\Model\Exception;

use Exception;

class DataIsNotConsistentException extends Exception
{
    public const DEFAULT_MESSAGE = 'Dto and Entity fields is not consistent.';

    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE);
    }
}
