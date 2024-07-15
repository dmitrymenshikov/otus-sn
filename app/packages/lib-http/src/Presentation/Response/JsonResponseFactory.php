<?php

declare(strict_types=1);

namespace Hustle\Http\Presentation\Response;

use Hustle\Http\DTO\JsonResponseDTO;

/**
 * @author Dmitry Menshikov <dmitrymenshikov@gmail.com>
 */
class JsonResponseFactory
{
    public static function createResponse(JsonResponseDTO $dto): JsonResponse
    {
        return new JsonResponse(
            $dto->buildResponseData(),
            $dto->getStatusCode(),
            $dto->getHeaders()
        );
    }
}
