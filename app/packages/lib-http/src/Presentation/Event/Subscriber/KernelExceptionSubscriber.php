<?php

declare(strict_types=1);

namespace Hustle\Http\Presentation\Event\Subscriber;

use Hustle\Http\DTO\JsonResponseDTO;
use Hustle\Http\Infrastructure\Contracts\HttpExceptionInterface;
use Hustle\Http\Presentation\Response\JsonResponseFactory;
use Hustle\Http\Presentation\Response\Trait\ResponseMetaTrait;
use Hustle\Http\Presentation\Response\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Throwable;

final class KernelExceptionSubscriber implements EventSubscriberInterface
{
    use ResponseMetaTrait;

    public function __construct(public string $serviceName,
                                public string $serviceVersion,
                                public RouterInterface $router)
    {}

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    /**
     * Обработчик HTTP ошибок
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $event->setResponse($this->getResponse($exception));
    }

    private function getResponse(Throwable $exception): JsonResponse
    {
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $errors = $exception->getErrors();
        } else {
            if ($exception instanceof HttpException) {
                $statusCode = $exception->getStatusCode();
            } else {
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            $errors = [
                [
                    'code' => (string) $exception->getCode(),
                    'title' => $exception->getMessage(),
                ],
            ];
        }

        $responseDto = new JsonResponseDto($statusCode, [], [], false, $errors, []);

        return JsonResponseFactory::createResponse($responseDto);
    }
}
