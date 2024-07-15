<?php

declare(strict_types=1);

namespace Hustle\Http\Presentation\Controller;

use Hustle\Http\DTO\JsonResponseDTO;
use Hustle\Http\Infrastructure\Form\FormTrait;
use Hustle\Http\Presentation\Response\JsonResponse;
use Hustle\Http\Presentation\Response\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractBaseController extends AbstractController
{
    use FormTrait;

    protected function createOkResponse(array $data = [], array $meta = []): JsonResponse
    {
        return $this->createJsonResponse(Response::HTTP_OK, $data, $meta);
    }

    protected function createErrorResponse(
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $data = [],
        array $errors = [],
    ): JsonResponse
    {
        return $this->createJsonResponse($statusCode, $data, [], false, $errors);
    }

    private function createJsonResponse(
        int $statusCode,
        array $data,
        array $meta,
        ?bool $success = null,
        ?array $errors = null
    ): JsonResponse
    {
        if (null === $success) {
            $success = $statusCode < 400;
        }

        $dto = new JsonResponseDTO(
            $statusCode,
            $data,
            $meta,
            $success,
            $errors
        );

        return JsonResponseFactory::createResponse($dto);
    }
}