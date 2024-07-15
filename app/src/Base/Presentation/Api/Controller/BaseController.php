<?php

namespace App\Base\Presentation\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/ping', name: 'base_ping', methods: ['GET'])]
    public function ping(): JsonResponse
    {
        return new JsonResponse(['pong']);
    }
}
