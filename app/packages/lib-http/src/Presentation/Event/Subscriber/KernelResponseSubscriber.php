<?php

declare(strict_types=1);

namespace Hustle\Http\Presentation\Event\Subscriber;

use Hustle\Http\Presentation\Response\JsonResponse;
use Hustle\Http\Presentation\Response\Trait\ResponseMetaTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

final class KernelResponseSubscriber implements EventSubscriberInterface
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
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }

    public function onKernelResponse(ResponseEvent $responseEvent): void
    {
        $response = $responseEvent->getResponse();

        if (!$response instanceof JsonResponse) {
            return;
        }

        $response->mergeData($this->getDefaultMeta($this->serviceName, $this->serviceVersion));
        $responseEvent->setResponse($response);
    }
}
