<?php

declare(strict_types=1);

namespace Hustle\Http\Presentation\Response;

use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;

class JsonResponse extends SymfonyJsonResponse
{
    public function __construct(private readonly array $tempData, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($tempData, $status, $headers, $json);
    }

    private function getTempData(): array
    {
        return $this->tempData;
    }

    public function mergeData(array $newData): void
    {
        $data = array_merge_recursive(
            $this->getTempData(),
            $newData
        );

        $this->setData($data);
    }
}
