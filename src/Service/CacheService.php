<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class CacheService
{

    public function cache($request, JsonResponse $jsonResponse)
    {

        $jsonResponse->setEtag(md5($jsonResponse->getContent()));

        //ajoute la reponse au cache
        $jsonResponse->setPublic()
            ->setMaxAge(3600);

        //Verifie si la reponse est differente du cache
        if ($jsonResponse->isNotModified($request)) {

            return $jsonResponse;
        }

        //besoin de revalider si le temps est passé
        $jsonResponse->headers->addCacheControlDirective('must-revalidate', true);

        return $jsonResponse;
    }
}
