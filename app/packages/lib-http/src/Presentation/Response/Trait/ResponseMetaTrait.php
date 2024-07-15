<?php

declare(strict_types=1);

namespace Hustle\Http\Presentation\Response\Trait;

trait ResponseMetaTrait
{
    private function getDefaultMeta(string $serviceName = '', string $version = ''): array
    {
        return [
            'meta' => [
                'serviceName' => $serviceName,
                'version' => $version
            ]
        ];
    }
}