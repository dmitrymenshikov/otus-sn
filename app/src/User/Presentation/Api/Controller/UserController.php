<?php

namespace App\User\Presentation\Api\Controller;

use App\User\Model\Entity\User;
use App\User\Model\Form\DTO\UserRegisterTypeDTO;
use App\User\Model\Form\Type\UserRegisterFormType;
use App\User\Persistent\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Hustle\Http\Presentation\Controller\AbstractBaseController;
use Hustle\Http\Presentation\Response\JsonResponse;
use Random\RandomException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/user', name: 'user_')]
class UserController extends AbstractBaseController
{
    /**
     * @throws RandomException
     * @throws Exception
     */
    #[Route('/login', name: 'login', methods: ["POST"])]
    public function loginPage(#[CurrentUser] ?User $user, UserRepository $repository): JsonResponse
    {
        $token = md5(random_bytes(10)); // kek
        $repository->updateToken($user, $token);

        return $this->createOkResponse(['token' => $token, 'user' => $user]);
    }

    /**
     * @throws Exception
     */
    #[Route('/register', name: 'register', options: ['type' => 'api'], methods: ['POST'])]
    public function register(
        UserRepository              $repository,
        Request                     $request,
        FormFactoryInterface        $formFactory,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        $form = $formFactory->create(UserRegisterFormType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->createFormValidationErrorResponse($form);
        }

        /** @var UserRegisterTypeDTO $userDto */
        $userDto = $form->getData();
        $user = User::create($userDto);
        $hPass = $passwordHasher->hashPassword($user, $userDto->password);

        $user->setPassword($hPass);

        $uuid = $repository->save($user);

        return $this->createOkResponse(['uuid' => $uuid]);
    }

    /**
     * @throws Exception
     */
    #[Route('/get/{uuid}', name: 'view', options: ['type' => 'api'], methods: ['GET'])]
    public function viewUser(string $uuid, UserRepository $repository): JsonResponse
    {
        $user = $repository->getUserByUuid($uuid);

        return $this->createOkResponse(['user' => $user]);
    }
}
