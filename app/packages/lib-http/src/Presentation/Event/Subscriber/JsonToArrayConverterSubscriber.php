<?php

declare(strict_types=1);

namespace Hustle\Http\Presentation\Event\Subscriber;

use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

/**
 * Парсит запрос, если видит content-type = json, то пытается декодировать тело запроса через json_decode
 * и получившийся результат заменить в объекте Request
 */
final readonly class JsonToArrayConverterSubscriber implements EventSubscriberInterface
{
    public function __construct(private RouterInterface $router)
    {}

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'convert',
        ];
    }

    public function convert(ControllerEvent $event): void
    {
        if (!$this->isJsonApiCall($event)) {
            return;
        }

        $request = $event->getRequest();

        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $request->request->replace($data);
        } catch (Exception $exception) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }
    }

    private function isJsonApiCall(ControllerEvent $event): bool
    {
        $request = $event->getRequest();

        if (!$this->isApiRoute($request->get('_route'))) {
            return false;
        }

        if ($request->getContentTypeFormat() !== 'json') {
            return false;
        }

        if (empty($request->getContent())) {
            return false;
        }

        return true;
    }

    private function isApiRoute(?string $route = null): bool
    {
        if (null === $route) return false;

        $type = $this->router->getRouteCollection()->get($route)->getOption('type');

        return 'api' === $type;
    }
}
