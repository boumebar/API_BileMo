<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/api/users", name="api_users_add", methods="POST")
     */
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json');
        $em->persist($user);
        $em->flush();


        $context = SerializationContext::create()->setGroups(array("user:show"));
        $userJson = $serializer->serialize($user, 'json', $context);

        $response = new JsonResponse($userJson, 201, [], true);

        return $response;
    }
}
