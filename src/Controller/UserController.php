<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
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
    public function index(UserRepository $userRepository, SerializerInterface $serializer, PaginationService $paginator): JsonResponse
    {
        /** @var User */
        $connnectedUser = $this->getUser();
        $query = $userRepository->findByCustomerId($connnectedUser->getId());
        $result = $paginator->paginate($query, 5);
        $context = SerializationContext::create()->setGroups(["user:index"]);
        $json = $serializer->serialize($result, 'json', $context);
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="api_users_show", methods="GET")
     */
    public function show(UserRepository $userRepository, $id, SerializerInterface $serializer): JsonResponse
    {
        $user = $userRepository->find($id);
        /** @var User */
        $connnectedUser = $this->getUser();
        if ($connnectedUser->getId() !==  $user->getCustomer()->getId()) {
            throw new JsonException("You do not have the required rights to make this request", JsonResponse::HTTP_UNAUTHORIZED);
        }
        $context = SerializationContext::create()->setGroups(["user:show"]);
        $json = $serializer->serialize($user, 'json', $context);
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/users", name="api_users_add", methods="POST")
     */
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {

        $data = $request->getContent();

        // $errors = $validator->validate($data);

        // if ($errors->count() > 0) {
        //     return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        // }

        $user = $serializer->deserialize($data, User::class, 'json');
        /** @var User */
        $connnectedUser = $this->getUser();
        if ($connnectedUser->getId() !==  $user->getCustomer()->getId()) {
            throw new JsonException("You do not have the required rights to make this request", JsonResponse::HTTP_UNAUTHORIZED);
        }
        $em->persist($user);
        $em->flush();


        $context = SerializationContext::create()->setGroups(array("user:show"));
        $userJson = $serializer->serialize($user, 'json', $context);

        $response = new JsonResponse($userJson, 201, [], true);

        return $response;
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="api_users_delete", methods="DELETE")
     */
    public function delete(int $id, EntityManagerInterface $em, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        /** @var User */
        $connnectedUser = $this->getUser();
        if ($connnectedUser->getId() !==  $user->getCustomer()->getId()) {
            throw new JsonException("You do not have the required rights to make this request", JsonResponse::HTTP_UNAUTHORIZED);
        }
        $em->remove($user);
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}
