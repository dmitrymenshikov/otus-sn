<?php

declare(strict_types=1);

namespace Hustle\Http\DTO;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

/**
 * @author Dmitry Menshikov <dmitrymenshikov@gmail.com>
 */
class JsonResponseDTO
{
    public function __construct(
        protected int    $statusCode = 200,
        protected array  $data = [],
        protected array  $meta = [],
        protected bool   $success = true,
        protected ?array $errors = null,
        protected array  $headers = []
    )
    {
    }

    public function getData(): array
    {
        if (null !== $this->getErrors()) {
            $this->data['errors'] = $this->getErrors();
        }

        return $this->data;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function buildResponseData(): array
    {
        return [
            'success' => $this->isSuccess(),
            'data' => $this->getData(),
            'meta' => $this->getMeta(),
        ];
    }
}