<?php

declare(strict_types=1);

namespace Hustle\Http\Infrastructure\Contracts;

interface HttpExceptionInterface
{
    public function getStatusCode(): int;

    public function getErrors(): array;
}
