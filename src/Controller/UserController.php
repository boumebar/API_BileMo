<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use App\Service\CacheService;
use OpenApi\Annotations as OA;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Security as OASecurity;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @OASecurity(name="Bearer")
 * @OA\Tag(name="User")
 */

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="api_user_index", methods="GET")
     * @OA\Get(summary="Get list of users by customer")
     *  @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="page number",
     *     @OA\Schema(type="int", default = "1")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of items by page",
     *     @OA\Schema(type="int", default = 5)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns users list",
     *     )),
     * @OA\Response(
     *     response=404,
     *     description="Page Not found",
     *     )
     *
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
     * @OA\Get(summary="Get one user by his Id ")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns users list",
     *     )),
     * @OA\Response(
     *     response=404,
     *     description="Page Not found",
     *     )
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
     * @OA\Post(summary="Add user from his customer")
     * @OA\Response(
     *     response=JsonResponse::HTTP_CREATED,
     *     description="Create a user and returns it"
     * )
     * @OA\Response(
     *     response=JsonResponse::HTTP_BAD_REQUEST,
     *     description="Bad Json syntax or incorrect data"
     * )
     * @OA\Response(
     *     response=JsonResponse::HTTP_UNAUTHORIZED,
     *     description="Unauthorized request")
     * 
     * @OA\RequestBody(
     *     description="The new user to create",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/Json",
     *         @OA\Schema(
     *             type="object",   
     *             @OA\Property(
     *                 property="firstname",
     *                 description="firstname",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="lastname",
     *                 description="lastname",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 description="email",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
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
     * @OA\Delete(summary="Remove user from his customer")
     *
     *  @OA\Response(
     *     response=JsonResponse::HTTP_NO_CONTENT,
     *     description="Delete a user"
     * )
     * @OA\Response(
     *     response=JsonResponse::HTTP_UNAUTHORIZED,
     *     description="Unauthorized request"
     * )
     * @OA\Response(
     *     response=JsonResponse::HTTP_NOT_FOUND,
     *     description="User not found"
     * )
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
