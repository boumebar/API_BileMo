<?php


namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\Security\Core\Security;



class UserService
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function UserVerif(?User $user)
    {
        if (!$user) {
            throw new JsonException("This user don't exist", JsonResponse::HTTP_BAD_REQUEST);
        }

        /** @var User */
        $connnectedUser = $this->security->getUser();
        if ($connnectedUser->getId() !==  $user->getCustomer()->getId()) {
            throw new JsonException("You do not have the required rights to make this request", JsonResponse::HTTP_UNAUTHORIZED);
        }
    }
}
