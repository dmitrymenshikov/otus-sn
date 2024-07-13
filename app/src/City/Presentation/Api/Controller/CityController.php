<?php

namespace App\City\Presentation\Api\Controller;

use App\City\Persistent\Repository\CityRepository;
use Doctrine\DBAL\Exception;
use Hustle\Http\Presentation\Controller\AbstractBaseController;
use Hustle\Http\Presentation\Response\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/cities', name: 'cities_')]
class CityController extends AbstractBaseController
{
    /**
     * @throws Exception
     */
    #[Route('/get/all', name: 'view', options: ['type' => 'api'], methods: ['GET'])]
    public function viewUser(CityRepository $repository): JsonResponse
    {
        return $this->createOkResponse(['cities' => $repository->fetchAll()]);
    }
}
