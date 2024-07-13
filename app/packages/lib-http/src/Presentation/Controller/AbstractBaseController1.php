<?php

declare(strict_types=1);

namespace Hustle\Http\Presentation\Controller;

use Hustle\Http\Presentation\Response\JsonResponse;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

abstract class AbstractBaseController1
{
    protected function handleApiCall(callable $callable): JsonResponse
    {
        try {
            $callResult = call_user_func($callable);

            return $this->createOkResponse($callResult['data'], $callResult['meta']);
        }
        catch (Exception $e) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                $e->getMessage(),
                $e->getPrevious(),
                [],
                $e->getCode()
            );
        }
        catch (Throwable $e) {
            throw new HttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage(),
                $e->getPrevious(),
                [],
                $e->getCode()
            );
        }
    }

    protected function createOkResponse(?array $data = null, array $meta = []): JsonResponse
    {
        return $this->createOkJsonResponse(Response::HTTP_OK, $data, $meta);
    }

    private function createOkJsonResponse(int $statusCode, $data = null, array $meta = []): JsonResponse
    {
        return $this->createJsonResponse(
            $statusCode,
            [
                'data' => $data,
                'meta' => $meta,
            ]
        );
    }

    private function createJsonResponse(int $statusCode, array $data, ?bool $success = null): JsonResponse
    {
        if (null === $success) {
            $success = $statusCode < 400;
        }

        $body = array_merge(
            [
                'statusCode' => $statusCode,
                'success' => $success,
            ],
            $data
        );

        return new JsonResponse($body, $statusCode, [], false);
    }

    public function createErrorJsonResponse(int $statusCode, array $errors = [], ?bool $success = null): JsonResponse
    {
        return $this->createJsonResponse(
            $statusCode,
            [
                'data' => null,
                'errors' => $errors,
                'meta' => [],
            ],
            $success
        );
    }

    protected function createDefaultApiResponseArray(array $data, array $meta = []): array
    {
        return [
            'data' => $data,
            'meta' => $meta
        ];
    }
}