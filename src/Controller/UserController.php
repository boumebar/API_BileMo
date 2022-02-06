<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CacheService;
use App\Service\PaginationService;
use App\Service\UserService;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="api_user_index", methods="GET")
     */
    public function index(UserRepository $userRepository, SerializerInterface $serializer, PaginationService $pagination,  Request $request, CacheService $cache): JsonResponse
    {
        /** @var User */
        $connnectedUser = $this->getUser();
        $users = $userRepository->findByCustomerId($connnectedUser->getId());

        $paginatedCollection = $pagination->paginate($request, $users, 5, 'api_products_index');

        $json = $serializer->serialize($paginatedCollection, 'json');
        $response = new JsonResponse($json, 200, [], true);

        return $cache->cache($request, $response);
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="api_users_show", methods="GET")
     */
    public function show(UserRepository $userRepository, $id, SerializerInterface $serializer, UserService $userService): JsonResponse
    {
        $user = $userRepository->find($id);

        $userService->UserVerif($user);
        $json = $serializer->serialize($user, 'json');
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/users", name="api_users_add", methods="POST")
     */
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, UserService $userService)
    {
        /** @var User*/
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setCustomer($this->getUser());

        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $userService->UserVerif($user);

        $em->persist($user);
        $em->flush();


        $userJson = $serializer->serialize($user, 'json');

        $response = new JsonResponse($userJson, 201, [], true);

        return $response;
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="api_users_delete", methods="DELETE")
     */
    public function delete(int $id, EntityManagerInterface $em, UserRepository $userRepository, UserService $userService)
    {
        $user = $userRepository->find($id);

        $userService->UserVerif($user);
        $em->remove($user);
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}
