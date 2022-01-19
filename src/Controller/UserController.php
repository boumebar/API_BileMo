<?php

namespace App\Controller;

use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="api_user_index", methods="GET")
     */
    public function index(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();
        $context = SerializationContext::create()->setGroups(["user:index"]);
        $json = $serializer->serialize($users, 'json', $context);
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="api_users_show", methods="GET")
     */
    public function show(UserRepository $userRepository, $id, SerializerInterface $serializer): JsonResponse
    {
        $user = $userRepository->find($id);
        $context = SerializationContext::create()->setGroups(["user:show"]);
        $json = $serializer->serialize($user, 'json', $context);
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }
}
