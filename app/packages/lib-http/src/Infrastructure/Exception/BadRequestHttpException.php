<?php

declare(strict_types=1);

namespace Hustle\Http\Infrastructure\Exception;

use Hustle\Http\Infrastructure\Contracts\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException as BaseException;
use Throwable;

class BadRequestHttpException extends BaseException implements HttpExceptionInterface
{
    private string $errorCode;

    public function __construct(string $message, string $errorCode, ?Throwable $throwable = null)
    {
        parent::__construct($message, $throwable, 400);

        $this->errorCode = $errorCode;
    }

    public function getErrors(): array
    {
        return [
            [
                'code' => $this->errorCode,
                'title' => $this->getMessage(),
            ],
        ];
    }
}
